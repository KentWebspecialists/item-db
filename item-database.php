<?php
/*
Plugin Name: Item Database
Plugin URI: https://www.kentwebspecialists.co.uk/
Description: Add items to a grid like table via shortcode entered into the item database page with custom optional fields
Version: 1.0.0
Author: Bradly Spicer
Author URI: https://www.bradlyspicer.co.uk
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: item-db
Domain Path: /languages
*/


// Post Type Registration //
require_once plugin_dir_path(__FILE__) . 'settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/itemdb-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta_fields.php';
require_once plugin_dir_path(__FILE__) . 'includes/media_uploader.php';
require_once plugin_dir_path(__FILE__) . 'includes/category_fields.php';
require_once plugin_dir_path(__FILE__) . 'includes/csv.php';

//enqueue the files
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

function itemdb_save_postdata($post_id) {
    if (array_key_exists('itemdb_main_text_field', $_POST)) {
        update_post_meta(
            $post_id,
            'itemdb_main_text',
            $_POST['itemdb_main_text_field']
        );
    }
    
    if (array_key_exists('itemdb_images_field', $_POST)) {
        update_post_meta(
            $post_id,
            'itemdb_images',
            $_POST['itemdb_images_field']
        );
    }
}
add_action('save_post', 'itemdb_save_postdata');

function itemdb_post_link( $post_link, $id = 0 ){
    $post = get_post($id);  
    if ( is_object( $post ) ){
        $terms = wp_get_object_terms( $post->ID, 'item_category' );
        if( $terms ){
            return str_replace( '%item_category%' , $terms[0]->slug , $post_link );
        }
    }
    return $post_link;  
}
add_filter( 'post_type_link', 'itemdb_post_link', 1, 3 );


function itemdb_post_type() {
    register_post_type( 'db',
        array(
            'labels' => array(
                'name' => __( 'Items' ),
                'singular_name' => __( 'Item DB' )
            ),
            'public' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'thumbnail', 'custom-fields'),
            'has_archive' => true,
            'rewrite' => array( 'slug' => 'db/%item_category%', 'with_front' => true ),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-layout',
        )
    );
}
add_action( 'init', 'itemdb_post_type' );

function itemdb_create_categories() {
    $labels = array(
        'name' => _x('Item Categories', 'taxonomy general name'),
        'singular_name' => _x('Item Category', 'taxonomy singular name'),
        'search_items' => __('Search Item Categories'),
        'all_items' => __('All Item Categories'),
        'parent_item' => __('Parent Item Category'),
        'parent_item_colon' => __('Parent Item Category:'),
        'edit_item' => __('Edit Item Category'),
        'update_item' => __('Update Item Category'),
        'add_new_item' => __('Add New Item Category'),
        'new_item_name' => __('New Item Category Name'),
        'menu_name' => __('Item Categories'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'item-category'),
    );

    register_taxonomy('item_category', array('db'), $args);
}
add_action('init', 'itemdb_create_categories');
add_shortcode('ItemDB', 'itemdb_display_items');

function itemdb_enable_pagination_render() {
    ?>
    <label class="switch" for="checkbox">
        <input type="checkbox" name="itemdb_enable_pagination" id="checkbox" value="1" <?php checked(1, get_option('itemdb_enable_pagination', 0)); ?>>
        <div class="slider round"></div>
        <?php esc_html_e('', 'itemdb'); ?>
    </label>
    <?php
}