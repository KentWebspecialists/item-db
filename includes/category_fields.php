<?php

// Add the fields to the "Add New Item Category" screen
function mytheme_add_item_category_image($taxonomy) {
    wp_enqueue_media();

    ?>
    <div class="form-field term-group">
        <label for="item-category-image-id"><?php _e('Image', 'mytheme'); ?></label>
        <input type="hidden" id="item-category-image-id" name="item-category-image-id" class="custom_media_url" value="">
        <div id="item-category-image-wrapper"></div>
        <p>
            <input type="button" class="button button-secondary mytheme_tax_media_button" id="mytheme_tax_media_button" name="mytheme_tax_media_button" value="<?php _e('Add Image', 'mytheme'); ?>" />
            <input type="button" class="button button-secondary mytheme_tax_media_remove" id="mytheme_tax_media_remove" name="mytheme_tax_media_remove" value="<?php _e('Remove Image', 'mytheme'); ?>" />
        </p>
    </div>
    <?php
}
add_action('item_category_add_form_fields', 'mytheme_add_item_category_image', 10, 2);

// Save the fields when we create a new item category
function mytheme_save_item_category_image($term_id, $tt_id) {
    if (isset($_POST['item-category-image-id']) && '' !== $_POST['item-category-image-id']) {
        $image = $_POST['item-category-image-id'];
        add_term_meta($term_id, 'item-category-image-id', $image, true);
    }
}
add_action('created_item_category', 'mytheme_save_item_category_image', 10, 2);

// Add the fields to the "Edit Item Category" screen
function mytheme_edit_item_category_image($term, $taxonomy) {
    wp_enqueue_media();
    
    // getting term ID
    $term_id = $term->term_id;

    // retrieve the existing value(s) for this meta field, this returns an array
    $image = get_term_meta($term_id, 'item-category-image-id', true);

    echo '<tr class="form-field term-group-wrap">
            <th scope="row"><label for="item-category-image-id">' . __('Image', 'mytheme') . '</label></th>
            <td>
                <input type="hidden" id="item-category-image-id" name="item-category-image-id" value="' . $image . '">
                <div id="item-category-image-wrapper">';
                if ($image) {
                    echo '<img class="custom_media_image" src="' . wp_get_attachment_url($image) . '" style="margin:0;padding:0;max-height:100px;float:none;">';
                }
                echo '</div>
                <p>
                    <input type="button" class="button button-secondary mytheme_tax_media_button" id="mytheme_tax_media_button" name="mytheme_tax_media_button" value="' . __('Add Image', 'mytheme') . '" />
                    <input type="button" class="button button-secondary mytheme_tax_media_remove" id="mytheme_tax_media_remove" name="mytheme_tax_media_remove" value="' . __('Remove Image', 'mytheme') . '" />
                </p>
            </td>
        </tr>';
}
add_action('item_category_edit_form_fields', 'mytheme_edit_item_category_image', 10, 2);

// Save the fields when we edit an existing item category
function mytheme_update_item_category_image($term_id, $tt_id) {
    if (isset($_POST['item-category-image-id']) && '' !== $_POST['item-category-image-id']) {
        $image = $_POST['item-category-image-id'];
        update_term_meta($term_id, 'item-category-image-id', $image);
    } else {
        delete_term_meta($term_id, 'item-category-image-id');
    }
}
add_action('edited_item_category', 'mytheme_update_item_category_image', 10, 2);
