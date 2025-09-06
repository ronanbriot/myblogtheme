<?php

/**
 * Theme setup.
 */

namespace App;

use function Roots\bundle;

/**
 * Register the theme assets.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    $bundle = bundle('app');
    // Add version parameter to force cache refresh
    $bundle->enqueue(['version' => time()]);
}, 100);

/**
 * Register the theme assets with the block editor.
 *
 * @return void
 */
add_action('enqueue_block_editor_assets', function () {
    bundle('editor')->enqueue();
}, 100);

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary',
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer',
    ] + $config);
});

add_action('after_setup_theme', function () {
    load_theme_textdomain('sage', get_template_directory().'/resources/lang');
});

// Replace Posts label as Articles in Admin Panel
add_action('init', function () {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Évènements';
    $labels->singular_name = 'Évènement';
    $labels->add_new = 'Ajouter';
    $labels->add_new_item = 'Ajouter un nouvel évènement';
    $labels->edit_item = 'Editer évènement';
    $labels->new_item = 'Nouvel évènement';
    $labels->view_item = 'Voir évènement';
    $labels->search_items = 'Rechercher évènement';
    $labels->not_found = 'Aucun évènement trouvé';
    $labels->not_found_in_trash = 'Aucun évènement trouvé dans la corbeille';
    $labels->name_admin_bar = 'Ajouter un nouvel évènement';
});

add_action('admin_menu', function () {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Évènements';
    $submenu['edit.php'][5][0] = 'Évènements';
    $submenu['edit.php'][10][0] = 'Ajouter un nouvel évènement';
    echo '';
});

add_action('init', function () {
    $post_type_object = get_post_type_object('post');
    $post_type_object->template = [
        ['core/gallery'],
    ];
});

/**
 * Create login page on theme activation.
 */
add_action('after_switch_theme', function () {
    $login_page = get_page_by_path('login');
    if (! $login_page) {
        $page_id = wp_insert_post([
            'post_title' => 'Connexion',
            'post_name' => 'login',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
        ]);

        if ($page_id) {
            update_post_meta($page_id, '_wp_page_template', 'page-login.blade.php');
        }
    }
});

/**
 * Customize logout URL to redirect to custom login page.
 */
add_filter('logout_url', function ($logout_url, $redirect) {
    $login_page = get_page_by_path('login');
    if ($login_page) {
        $custom_redirect = $redirect ? $redirect : get_permalink($login_page->ID);
        // Build logout URL manually to avoid infinite loops
        $logout_url = home_url('/wp/wp-login.php?action=logout&redirect_to='.urlencode($custom_redirect).'&_wpnonce='.wp_create_nonce('log-out'));
    }

    return $logout_url;
}, 10, 2);

/**
 * Customize admin bar logout URL to redirect to custom login page.
 */
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;

    $login_page = get_page_by_path('login');
    if ($login_page) {
        // Remove default logout menu
        $wp_admin_bar->remove_menu('logout');

        // Add custom logout menu under user menu
        $wp_admin_bar->add_menu([
            'id' => 'logout',
            'parent' => 'user-actions',
            'title' => __('Se déconnecter'),
            'href' => wp_logout_url(),
            'meta' => [
                'title' => __('Se déconnecter'),
            ],
        ]);
    }
});

/**
 * Handle login form submission on custom login page.
 */
add_action('template_redirect', function () {
    // Only process on login page and if form is submitted
    if (! is_page('login') || ! isset($_POST['wp-submit'])) {
        return;
    }

    // Process login
    $user_login = sanitize_user($_POST['log']);
    $user_password = $_POST['pwd'];
    $remember = isset($_POST['rememberme']);
    $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : home_url('/');

    // Attempt login
    $user = wp_authenticate($user_login, $user_password);

    if (is_wp_error($user)) {
        // Login failed, redirect back with error
        wp_redirect(add_query_arg('login', 'failed', get_permalink(get_page_by_path('login')->ID)));
        exit;
    } else {
        // Login successful
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, $remember);

        // Redirect to intended page
        wp_redirect($redirect_to);
        exit;
    }
});

/**
 * Force login for all pages except login page and admin.
 * Redirect non-logged-in users to login page.
 */
add_action('template_redirect', function () {
    // Skip if user is logged in
    if (is_user_logged_in()) {
        return;
    }

    // Skip if we're on the login page or admin
    if (is_admin() || is_page('login') || strpos($_SERVER['REQUEST_URI'], '/wp-login.php') !== false) {
        return;
    }

    // Skip for AJAX requests
    if (wp_doing_ajax()) {
        return;
    }

    // Skip for REST API requests
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }

    // Skip for asset requests (CSS, JS, images, fonts, etc.)
    $request_uri = $_SERVER['REQUEST_URI'];
    $asset_extensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.woff', '.woff2', '.ttf', '.eot', '.ico'];
    foreach ($asset_extensions as $ext) {
        if (strpos($request_uri, $ext) !== false) {
            return;
        }
    }

    // Skip for theme assets
    if (strpos($request_uri, '/app/themes/') !== false || strpos($request_uri, '/wp-content/themes/') !== false) {
        return;
    }

    // Skip for uploads
    if (strpos($request_uri, '/wp-content/uploads/') !== false) {
        return;
    }

    // Redirect to custom login page
    $login_page = get_page_by_path('login');
    if ($login_page) {
        wp_redirect(get_permalink($login_page->ID));
    } else {
        wp_redirect(wp_login_url(home_url($_SERVER['REQUEST_URI'])));
    }
    exit;
});

// Activate WordPress Maintenance Mode
add_action('init', function () {
    if (
        $_SERVER['REQUEST_URI'] !== '/login/'
        && ! str_contains($_SERVER['REQUEST_URI'], '/wp/wp-login.php')
        && ! current_user_can('administrator') // rule to allow admins
        && WP_ENV === 'development'
        && $_SERVER['HTTP_USER_AGENT'] !== 'PostmanRuntime/7.42.0' // postman
        && $_SERVER['HTTP_USER_AGENT'] !== 'Shotstack-Webhook/1.0' // shotstack
        && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' // wp-cli
    ) {
        wp_die(
            __('Briefly unavailable for scheduled maintenance. Check back in a minute.', 'sage'),
            __('Maintenance'),
            503
        );
    }
});
