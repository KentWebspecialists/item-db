jQuery(document).ready(function ($) {
  // Add new field button click
  $("#itemdb-add-field").on("click", function (e) {
    e.preventDefault();

    var newField = `
            <div class="itemdb-custom-field">
                <input type="text" name="itemdb_custom_fields[][label]" placeholder="Label" value="">
                <input type="text" name="itemdb_custom_fields[][value]" placeholder="Value" value="">
                <a href="#" class="itemdb-remove-field button">Remove</a>
            </div>
        `;

    $("#itemdb-custom-fields-container").append(newField);
  });

  // Remove field button click
  $("body").on("click", ".itemdb-remove-field", function (e) {
    e.preventDefault();
    $(this).closest(".itemdb-custom-field").remove();
  });
});
