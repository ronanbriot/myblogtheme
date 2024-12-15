<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function (): string {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

/**
 * Add mp4 extension for attachment come from shotstack.
 *
 * @return array
 */
add_filter("image_sideload_extensions", function ($allowed_extensions, $file): array {
    if (strpos($file, 'shotstack-api-stage-output') !== false) {
        $allowed_extensions[] = 'mp4';
    }
    return $allowed_extensions;
}, 10, 2);

/**
 * Remove test on background updates.
 *
 * @return array
 */
add_filter('site_status_tests', function ($tests): array {
    unset($tests['async']['background_updates']);
    return $tests;
});