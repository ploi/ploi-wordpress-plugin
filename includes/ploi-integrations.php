<?php

//Wordpress - Save & Publish
// Define the function to clear Ploi caches
function clear_ploi_caches_on_post_save($post_id) {
    // Get the post object
    $post = get_post($post_id);

    // Check if the post object exists and it is of 'post' type
    if (!$post || $post->post_type !== 'post') {
        return;
    }
    // Check if the post status is 'publish'
    if ($post->post_status !== 'publish') {
        return;
    }

    clear_ploi_caches();
}
add_action('save_post', 'clear_ploi_caches_on_post_save');

// Cache clearing hook integration for various plugins
$cache_clear_actions = [
    'elementor/editor/after_save',        // Elementor
    'after_rocket_clean_domain',          // WP-Rocket
    'wp_cache_cleared',                   // WP Super Cache
    'wpfc_delete_cache',                  // WP Fastest Cache
    'flying_press_purge_url:after',       // FlyingPress - URL
    'flying_press_purge_pages:after',     // FlyingPress - Pages
    'flying_press_purge_everything:after' // FlyingPress - Everything
];

foreach ($cache_clear_actions as $action) {
    add_action($action, 'clear_ploi_caches');
}