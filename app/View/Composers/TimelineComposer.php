<?php

namespace App\View\Composers;

use DateTime;
use IntlDateFormatter;
use Roots\Acorn\View\Composer;
use WP_Query;

class TimelineComposer extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var string[]
     */
    protected static $views = [
        'components.timeline'
    ];

    public function with()
    {
        $data = [
            'events' => $this->events(),
        ];
        return $data;
    }

    public function events(): array
    {
        $args = array(
            'post_type' => 'post',
            'orderby' => 'post_date',
            'order' => 'DESC'
        );
        $query = new WP_Query($args);

        foreach ($query->posts as $key => $post) {
            $eventDate = $post->post_date;
            $eventDateFr = new DateTime($eventDate);
            $fmt = datefmt_create(
                'fr-FR',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'Europe/Paris',
                IntlDateFormatter::GREGORIAN,
                "EEEE d MMMM y 'à' H'h'mm"
            );
            $post->event_date = datefmt_format($fmt, $eventDateFr);

            foreach (wp_get_post_categories($post->ID) as $category) {
                switch ($category) {
                    case 3:
                        $post->event_icon = 'icon-[tabler--cake]';
                        break;
                    case 4:
                        $post->event_icon = 'icon-[tabler--christmas-tree]';
                        break;
                    
                    default:
                        $post->event_icon = 'icon-[tabler--heart]';
                        break;
                }
            }
            $events[] = $post;
        }
        return $events;
    }
}
