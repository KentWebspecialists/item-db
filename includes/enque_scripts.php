<?php

function itemdb_enqueue_frontend_scripts() {
    wp_enqueue_style('itemdb-style', plugin_dir_url(__FILE__) . 'includes/styles.css', array(), '1.0.0');
    wp_enqueue_script('itemdb-script', plugin_dir_url(__FILE__) . 'includes/itemdb.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'itemdb_enqueue_frontend_scripts');

function itemdb_enqueue_scripts() {
    wp_enqueue_media();
    wp_enqueue_script('itemdb-admin-script', plugin_dir_url(__FILE__) . 'admin-script.js', array('jquery'), '1.0.0', true);
    wp_enqueue_style( 'itemdb-admin-styles', plugins_url( '/assets/css/admin-style.css', __FILE__ ) );
    wp_enqueue_script('itemdb-script', plugin_dir_url(__FILE__) . 'js/itemdb-script.js', array('jquery'));
}
add_action('admin_enqueue_scripts', 'itemdb_enqueue_scripts');