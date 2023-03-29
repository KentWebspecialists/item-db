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

    //Add Pagination
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // Get pagination setting value
    $enable_pagination = get_option('itemdb_enable_pagination', 0);

    $args = array(
        'post_type' => 'item-db',
        'orderby' => 'title',
        'order' => 'ASC',
    );

    if ($enable_pagination) {
        $args['posts_per_page'] = 10;
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args['paged'] = $paged;
    } else {
        $args['posts_per_page'] = -1;
    }

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
    
    $items = new WP_Query($args);

    $output = '<div class="itemdb-wrapper">';
        $output .= '<div class="itemdb-search-container">';
        $output .= '<input type="text" id="itemdb-search" placeholder="Search items...">';
        $output .= '</div>';
    $output .= '<div class="itemdb-grid flex-grid u-card-grid">';

    if ($items->have_posts()) {
        while ($items->have_posts()) {
            $items->the_post();
    
            // Get all custom fields for the current post
            $custom_fields = get_post_custom(get_the_ID());
    
            $output .= '<div class="itemdb-item u-card">';
    
            // Get thumbnail URL
            $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            if (!empty($thumb_url)) {
                $output .= '<div class="itemdb-thumbnail" style="background-image: url(' . esc_url($thumb_url) . ')"></div>';
            }
            $output .= '<div class="u-content">';
            $output .= '<h3>' . get_the_title() . '</h3>';
                
            if (!empty($custom_fields)) {
                $output .= '<ul class="itemdb-custom-fields">';
                foreach ($custom_fields as $label => $value) {
                    // Skip the meta keys that don't start with 'itemdb_'
                    if (substr($label, 0, strlen('itemdb_')) !== 'itemdb_') {
                        continue;
                    }
                    // Remove the prefix from the label before displaying it
                    $display_label = substr($label, strlen('itemdb_'));

                    // Check if the value is not empty
                    if (!empty(implode(', ', $value))) {
                        $output .= '<li><strong>' . esc_html($display_label) . ':</strong> ' . esc_html(implode(', ', $value)) . '</li>';
                    }
                }
                $output .= '</ul>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

    $output .= '</div>'; // Close the itemdb-grid div
    $output .= '</div>'; // Close the itemdb-wrapper div

    $big = 999999999; // This is just an arbitrary number for replacing it later.
    $output .= '<div class="itemdb-pagination">';
    $output .= paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $items->max_num_pages,
    ));
    $output .= '</div>';
    wp_reset_postdata();

    return $output;
    }
}