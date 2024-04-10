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

//Elementor
add_action( 'elementor/editor/after_save', 'clear_ploi_caches' );

//WP-Rocket
add_action( 'after_rocket_clean_domain', 'clear_ploi_caches' );

//WP Super cache
add_action( 'wp_cache_cleared', 'clear_ploi_caches' );

//WP Fastest Cache
add_action( 'wpfc_delete_cache', 'clear_ploi_caches' );
