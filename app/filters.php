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
add_filter('image_sideload_extensions', function ($allowed_extensions, $file): array {
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

/**
 * Enhance gallery blocks for GLightbox integration.
 *
 * @param  string  $block_content  The block content.
 * @param  array  $block  The full block.
 * @return string Modified block content.
 */
add_filter('render_block', function (string $block_content, array $block): string {
    // Only process gallery and image blocks
    if (! in_array($block['blockName'], ['core/gallery', 'core/image'], true)) {
        return $block_content;
    }

    if ($block['blockName'] === 'core/gallery') {
        // Process gallery blocks - add data-glightbox attributes to images
        $gallery_id = 'gallery-'.uniqid();

        // Find all img tags and wrap them with lightbox attributes
        $block_content = preg_replace_callback(
            '/<img([^>]*?)src=["\']([^"\']*?)["\']([^>]*?)>/i',
            function ($matches) use ($gallery_id) {
                $img_tag = $matches[0];
                $src = $matches[2];

                // Extract alt text if present
                $alt = '';
                if (preg_match('/alt=["\']([^"\']*?)["\']/', $img_tag, $alt_matches)) {
                    $alt = $alt_matches[1];
                }

                // Get attachment ID and full size URL
                $attachment_id = attachment_url_to_postid($src);
                $full_src = $src;
                if ($attachment_id) {
                    $full_image = wp_get_attachment_image_src($attachment_id, 'full');
                    if ($full_image) {
                        $full_src = $full_image[0];
                    }
                }

                // Build data-glightbox attribute
                $glightbox_attr = 'data-glightbox="';
                if (! empty($alt)) {
                    $glightbox_attr .= 'title: '.esc_attr($alt).'; ';
                }
                $glightbox_attr .= 'gallery: '.$gallery_id.'"';

                // If image is not already in a link, wrap it
                return '<a href="'.esc_url($full_src).'" '.$glightbox_attr.'>'.$img_tag.'</a>';
            },
            $block_content
        );

        // Remove any existing links around images and replace with our lightbox links
        $block_content = preg_replace(
            '/<a[^>]*href=[^>]*>(<a href="[^"]*" data-glightbox="[^"]*"><img[^>]*><\/a>)<\/a>/',
            '$1',
            $block_content
        );

    } elseif ($block['blockName'] === 'core/image') {
        // Process single image blocks
        $block_content = preg_replace_callback(
            '/<a([^>]*?)href=["\']([^"\']*?\.(?:jpg|jpeg|png|gif|webp))["\']([^>]*?)>/i',
            function ($matches) {
                $link_attrs = $matches[1].$matches[3];
                $href = $matches[2];

                // Add data-glightbox attribute if not present
                if (strpos($link_attrs, 'data-glightbox') === false) {
                    return '<a'.$link_attrs.' href="'.$href.'" data-glightbox>';
                }

                return $matches[0];
            },
            $block_content
        );
    }

    return $block_content;
}, 10, 2);

/**
 * Add gallery counter and description support to galleries.
 *
 * @param  string  $block_content  The block content.
 * @param  array  $block  The full block.
 * @return string Modified block content.
 */
add_filter('render_block_core/gallery', function (string $block_content, array $block): string {
    // Add a unique gallery ID for grouping images
    $gallery_id = 'gallery-'.uniqid();
    $block_content = str_replace('data-gallery="gallery-', 'data-gallery="'.$gallery_id, $block_content);

    return $block_content;
}, 10, 2);
