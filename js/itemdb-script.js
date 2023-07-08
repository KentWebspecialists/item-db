jQuery(document).ready(function ($) {
  var mediaUploader;

  $("#itemdb-upload-button").click(function (e) {
    e.preventDefault();

    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Image",
      button: {
        text: "Choose Image",
      },
      multiple: true,
    });

    mediaUploader.on("select", function () {
      var attachments = mediaUploader.state().get("selection").toJSON();
      var newImageUrls = attachments.map(function (attachment) {
        return attachment.url;
      });

      var oldImageUrls = $("#itemdb-image-field")
        .val()
        .split(",")
        .map(function (url) {
          return url.trim();
        });

      var allImageUrls = oldImageUrls.concat(newImageUrls);

      $("#itemdb-image-field").val(allImageUrls.join(","));
      $(".itemdb-gallery-container").empty();
      allImageUrls.forEach(function (url) {
        $(".itemdb-gallery-container").append(
          '<div class="itemdb-image"><img src="' +
            url +
            '" style="max-width: 200px; max-height: 200px;"></div>'
        );
      });
    });

    mediaUploader.open();
  });
});
