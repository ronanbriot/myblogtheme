<?php

namespace App\View\Composers;

use DateTime;
use IntlDateFormatter;
use Roots\Acorn\View\Composer;
use Shotstack\Client\Api\EditApi;
use Shotstack\Client\ApiException;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\Clip;
use Shotstack\Client\Model\Edit;
use Shotstack\Client\Model\ImageAsset;
use Shotstack\Client\Model\Output;
use Shotstack\Client\Model\ShotstackDestination;
use Shotstack\Client\Model\Soundtrack;
use Shotstack\Client\Model\Timeline;
use Shotstack\Client\Model\Track;
use WP_Error;
use WP_Post;

class Post extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.page-header',
        'partials.content',
        'partials.content-*',
    ];

    /**
     * Data to be passed to view before rendering, but after merging.
     *
     * @return array
     */
    public function override()
    {
        $data = [
            'title' => $this->title(),
            'pagination' => $this->pagination(),
            'eventSlideshowUrl' => $this->eventSlideshow(),
            'eventDate' => $this->getEventDate()
        ];
        return $data;
    }

    /**
     * Retrieve the post title.
     *
     * @return string
     */
    public function title()
    {
        if ($this->view->name() !== 'partials.page-header') {
            return get_the_title();
        }

        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                return get_the_title($home);
            }

            return __('Latest Posts', 'sage');
        }

        if (is_archive()) {
            return get_the_archive_title();
        }

        if (is_search()) {
            return sprintf(
                /* translators: %s is replaced with the search query */
                __('Search Results for %s', 'sage'),
                get_search_query()
            );
        }

        if (is_404()) {
            return __('Not Found', 'sage');
        }

        return get_the_title();
    }

    /**
     * Retrieve the pagination links.
     *
     * @return string
     */
    public function pagination()
    {
        return wp_link_pages([
            'echo' => 0,
            'before' => '<p>' . __('Pages:', 'sage'),
            'after' => '</p>',
        ]);
    }

    public function eventSlideshow()
    {
        global $posts;
        foreach ($posts as $post) {
            if (str_contains($post->post_content, 'wp:gallery')) {
                $pattern = '/<img\s+src="([^"]+)"\s+alt="([^"]*)"\s+class="([^"]*)"\s*\/?>/';
                preg_match_all($pattern, $post->post_content, $matches);
            }
        }

        // On construit le tableau d'urls pour Shotstack
        $allowedExtensions = ['png', 'jpg', 'jpeg'];
        foreach ($matches[1] as $url) {
            if (preg_match('/\.(\w+)(?=\?|$)/i', $url, $matches)) {
                $extension = $matches[1];
                if (in_array($extension, $allowedExtensions)) {
                    $urls[] = $url;
                }
            } else {
                dd("Aucune extension trouvée.");
            }
        }

        $attachmentSlideshow = $this->getAttachmentSlideshow();
        $eventSlideshowUrl = !is_null($attachmentSlideshow) ? $attachmentSlideshow : $this->getShotstackSlideshow($urls, $posts[0]);

        return $eventSlideshowUrl;
    }

    // function shotstackCallback()
    // {
    //     $content = json_decode(file_get_contents("php://input"));
    //     dump( $content->status);
    // }

    public function getShotstackSlideshow($images, $post)
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost('https://api.shotstack.io/edit/stage')
            ->setApiKey('x-api-key', 'B8DZjFPqSPBa1DGx7CPSdigayTEwtCzlJ8vejDZZ'); // use the API key issued to you

        $client = new EditApi(null, $config);

        $clips = [];
        $start = 0.0;
        $length = 3.0;

        foreach ($images as $image) {
            $imageAsset = new ImageAsset();
            $imageAsset->setSrc($image);

            $clip = new Clip();
            $clip->setAsset($imageAsset)
                ->setLength($length)
                ->setStart($start)
                ->setEffect('zoomIn');

            $start = $start + $length;
            $clips[] = $clip;
        }

        $track = new Track();
        $track
            ->setClips($clips);

        $soundtrack = new Soundtrack();
        $soundtrack
            ->setSrc('https://cdn.pixabay.com/audio/2022/03/23/audio_07b2a04be3.mp3')
            ->setEffect(Soundtrack::EFFECT_FADE_IN_FADE_OUT)
            ->setVolume(1);

        $timeline = new Timeline();
        $timeline
            ->setSoundtrack($soundtrack)
            ->setBackground('#000000')
            ->setTracks([$track]);
        // dd($timeline);

        $shotstackDestination = new ShotstackDestination();
        $shotstackDestination
        ->setProvider('shotstack')
        ->setExclude(true);

        $output = new Output();
        $output
            ->setFormat('mp4')
            ->setResolution('sd')
            ->setDestinations([$shotstackDestination]);

        $edit = new Edit();
        $edit
            ->setTimeline($timeline)
            ->setOutput($output);
        // ->setCallback('shotstackCallback');
        // dd(json_encode($edit));

        try {
            $response = $client->postRender($edit)->getResponse();
        } catch (ApiException $e) {
            dd('Request failed: ' . $e->getMessage() . $e->getResponseBody());
        }

        $status = 'fetching';
        while ($status == 'fetching' || $status == 'rendering' || $status == 'saving' || $status == 'queued') {
            $response = $client->getRender($response->getId(), false, true)->getResponse();
            $status = $response->getStatus();
            sleep(1);
        }

        if ($response->getStatus() == 'done') {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $src = media_sideload_image($response->getUrl(), $post->ID, null, 'src');

            if (!$src instanceof WP_Error) {
                return $src;
            }
        }
    }

    public function getAttachmentSlideshow(): string|null
    {
        $attachmentsSlideshow = get_attached_media('video');
        if (!empty($attachmentsSlideshow)) {
            foreach ($attachmentsSlideshow as $attachmentSlideshow) {
                $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
                if (preg_match($pattern, $attachmentSlideshow->post_name)) {
                    return $attachmentSlideshow->guid;
                }
            }
        }
        return null;
    }

    public function getEventDate(): string {
            global $post;

            $eventDate = get_post_meta($post->ID, 'event_date')[0];
            $date = new DateTime($eventDate);
            $fmt = new IntlDateFormatter(
                'fr_FR',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'America/Los_Angeles',
                IntlDateFormatter::GREGORIAN,
                'EEEE d LLLL r'
                // 'MM/dd/yyyy'
            );
            // dd($fmt->format($date));
            return $fmt->format($date);
            // dd(get_post_meta($post->ID, 'event_date'));
    }
}
