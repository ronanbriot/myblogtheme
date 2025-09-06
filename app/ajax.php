<?php

namespace App;

add_action('wp_ajax_load_more_events', 'App\loadMoreEvents');
add_action('wp_ajax_nopriv_load_more_events', 'App\loadMoreEvents');

function loadMoreEvents()
{
    // Vérification du nonce
    if (! check_ajax_referer('load_more_events', 'nonce', false)) {
        wp_send_json_error('Nonce invalide');

        return;
    }

    // Log pour le débogage
    error_log('Requête AJAX reçue pour load_more_events');
    error_log('Page demandée: '.$_POST['page']);

    $composer = new \App\View\Composers\TimelineComposer;
    $composer->loadMore();
}
