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
        'components.timeline',
    ];

    public function with()
    {
        $eventsData = $this->events();
        $data = [
            'events' => $eventsData['events'],
            'current_page' => $eventsData['current_page'],
            'max_pages' => $eventsData['max_pages'],
        ];

        return $data;
    }

    public function events(): array
    {
        $page = $_GET['page'] ?? 1;
        $posts_per_page = 10;

        $args = [
            'post_type' => 'post',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => $posts_per_page,
            'paged' => $page,
            'ignore_sticky_posts' => true,
        ];
        $query = new WP_Query($args);

        $events = [];
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
            $post->post_thumbnail = get_the_post_thumbnail($post->ID);

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

        return [
            'events' => $events,
            'max_pages' => $query->max_num_pages,
            'current_page' => (int) $page,
        ];
    }

    public function loadMore()
    {
        $page = $_POST['page'] ?? 1;
        $posts_per_page = 10;

        // Récupérer les IDs à exclure depuis la requête
        $exclude_ids = [];
        if (! empty($_POST['exclude_ids'])) {
            $exclude_ids = array_map('intval', explode(',', $_POST['exclude_ids']));
        }

        // Calculer l'offset basé sur la page demandée
        // Page 1 = offset 0, Page 2 = offset 10, etc.
        $offset = ($page - 1) * $posts_per_page;

        $args = [
            'post_type' => 'post',
            'post_status' => 'publish', // Exclure les brouillons
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => $posts_per_page,
            // Ne plus utiliser offset, mais plutôt post__not_in pour éviter les doublons
            'post__not_in' => $exclude_ids,
            'ignore_sticky_posts' => true,
            'no_found_rows' => false, // On a besoin du total pour calculer max_pages
        ];

        $query = new WP_Query($args);

        // Calculer max_pages basé sur le nombre d'événements retournés
        // Si on a moins de posts_per_page, c'est qu'on a atteint la fin
        $total_posts = $query->found_posts;
        $returned_posts = count($query->posts);

        // Si on a reçu moins d'événements que demandé, c'est qu'il n'y en a plus
        $has_more = $returned_posts >= $posts_per_page;
        $max_pages = $has_more ? 999 : $page; // 999 = valeur arbitraire élevée pour continuer

        $events = [];
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
            $post->post_thumbnail = get_the_post_thumbnail($post->ID);

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

        $response_data = [
            'events' => $events,
            'max_pages' => $max_pages, // Utiliser notre calcul manuel
            'current_page' => (int) $page,
        ];

        // En mode test, retourner les données au lieu d'envoyer du JSON
        if (defined('WP_ENV') && WP_ENV === 'testing') {
            return $response_data;
        }

        wp_send_json_success($response_data);
    }
}
