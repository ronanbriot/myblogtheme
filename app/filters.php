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
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

// Add mp4 extension for attachment come from shotstack
add_filter("image_sideload_extensions", function ($allowed_extensions, $file) {
    if (strpos($file, 'https://shotstack-api-stage-output.s3-ap-southeast-2.amazonaws.com') !== false) {
        $allowed_extensions[] = 'mp4';
    }
    return $allowed_extensions;
}, 10, 2);