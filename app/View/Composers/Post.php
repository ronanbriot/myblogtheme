<?php

namespace App\View\Composers;

use DateTime;
use IntlDateFormatter;
use Roots\Acorn\View\Composer;

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
        $attachmentSlideshow = $this->getAttachmentSlideshow();
        $eventSlideshowUrl = !is_null($attachmentSlideshow) ? $attachmentSlideshow : 'placeholder';

        return $eventSlideshowUrl;
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
