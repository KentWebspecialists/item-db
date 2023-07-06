jQuery(document).ready(function ($) {
  var custom_uploader;

  $("#itemdb-upload-button").click(function (e) {
    e.preventDefault();

    // Extend the wp.media object
    custom_uploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Images",
      button: {
        text: "Choose Images",
      },
      multiple: true, // Set this to true to allow multiple files to be selected
    });

    // When a file is selected, grab the URL and set it as the text field's value
    custom_uploader.on("select", function () {
      var selection = custom_uploader.state().get("selection");
      var image_urls = [];
      selection.map(function (attachment) {
        attachment = attachment.toJSON();
        image_urls.push(attachment.url);
      });
      $("#itemdb-image-field").val(image_urls.join(","));
    });

    // Open the uploader dialog
    custom_uploader.open();
  });
});
