<?php
function itemdb_display_items($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
        'fields' => ''
    ), $atts);

    $google_api_key = get_option('itemdb_google_api_key');
    $services_api_key = get_option('itemdb_services_api_key');
    
    // Get filter value from URL parameter
    $letter_filter = isset($_GET['letter']) ? $_GET['letter'] : '';

    $args = array(
        'post_type' => 'item-db',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );

    if ($letter_filter) {
        $args['title_like'] = $letter_filter . '%';
    }

    if (isset($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'item_category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ),
        );
    }

    // Add custom filter for title starting with a specific letter
    function itemdb_title_filter($where, $wp_query) {
        global $wpdb;

        if ($title_like = $wp_query->get('title_like')) {
            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . $title_like . '\'';
        }

        return $where;
    }
    add_filter('posts_where', 'itemdb_title_filter', 10, 2);

    $items = new WP_Query($args);

    // Remove custom filter
    remove_filter('posts_where', 'itemdb_title_filter', 10, 2);

    // Generate A-Z filter links
    $filter_links = '';
    $current_url = get_permalink();

    for ($i = 65; $i <= 90; $i++) {
        $letter = chr($i);
        $filter_links .= '<a href="' . esc_url(add_query_arg('letter', $letter, $current_url)) . '">' . $letter . '</a> ';
    }
    $filter_links .= '<a href="' . esc_url(remove_query_arg('letter', $current_url)) . '">All</a>';


    $output = '<div class="itemdb-wrapper">';
        $output .= '<div class="itemdb-search-container">';
        $output .= '<input type="text" id="itemdb-search" placeholder="Search items...">';
        $output .= '</div>';
    // $output .= '<div class="itemdb-filter">' . $filter_links . '</div>';
    $output .= '<div class="itemdb-grid flex-grid u-card-grid">';

    if ($items->have_posts()) {
        while ($items->have_posts()) {
            $items->the_post();
    
            // Get custom field data
            $custom_fields = get_post_meta(get_the_ID(), 'itemdb_custom_fields', true);
            $item_data = array();
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $field) {
                    $item_data[$field['label']] = $field['value'];
                }
            }
    
            // Get thumbnail URL
            $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
    
            $output .= '<div class="itemdb-item u-card">';
            if (!empty($thumb_url)) {
                $output .= '<div class="itemdb-thumbnail" style="background-image: url(' . esc_url($thumb_url) . ')"></div>';
            }
            $output .= '<h3>' . get_the_title() . '</h3>';
            if (!empty($item_data)) {
                $output .= '<ul class="itemdb-custom-fields">';
                foreach ($item_data as $label => $value) {
                    $output .= '<li><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</li>';
                }
                $output .= '</ul>';
            }
            $output .= '</div>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<p>No items found.</p>';
    }

    $output .= '</div>'; // Close the itemdb-grid div
    $output .= '</div>'; // Close the itemdb-wrapper div

    return $output;
}
