<?php
function itemdb_display_items($atts) {
    $args = array(
        'post_type' => 'item-db',
        'posts_per_page' => -1,
    );

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
    $output .= '<input type="text" id="itemdb-search" placeholder="Search items...">';
    $output .= '<div class="itemdb-grid flex-grid u-card-grid">';

    if ($items->have_posts()) {
        while ($items->have_posts()) {
            $items->the_post();
            $categories = get_the_terms(get_the_ID(), 'item_category');
            $category_list = array();
            if ($categories) {
                foreach ($categories as $category) {
                    $category_list[] = $category->name;
                }
            }

            $output .= '<div class="itemdb-item u-card">';
            $output .= '<h3>' . get_the_title() . '</h3>';
            if (!empty($category_list)) {
                $output .= '<p>Categories: ' . implode(', ', $category_list) . '</p>';
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
