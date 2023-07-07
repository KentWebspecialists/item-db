jQuery(document).ready(function ($) {
  function ct_media_upload(button_class) {
    var _custom_media = true,
      _orig_send_attachment = wp.media.editor.send.attachment;

    $("body").on("click", button_class, function (e) {
      var button_id = "#" + $(this).attr("id");
      var send_attachment_bkp = wp.media.editor.send.attachment;
      var button = $(button_id);
      _custom_media = true;
      wp.media.editor.send.attachment = function (props, attachment) {
        if (_custom_media) {
          $("#item-category-image-id").val(attachment.id);
          $("#item-category-image-wrapper").html(
            '<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />'
          );
          $("#item-category-image-wrapper .custom_media_image")
            .attr("src", attachment.url)
            .css("display", "block");
        } else {
          return _orig_send_attachment.apply(button_id, [props, attachment]);
        }
      };
      wp.media.editor.open(button);
      return false;
    });
  }
  ct_media_upload(".mytheme_tax_media_button.button");

  $("body").on("click", ".mytheme_tax_media_remove", function () {
    $("#item-category-image-id").val("");
    $("#item-category-image-wrapper").html(
      '<p>No image selected <a href="#">Add Image</a></p>'
    );
    return false;
  });
});
