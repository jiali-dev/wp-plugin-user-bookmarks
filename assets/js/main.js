jQuery(function ($) {
  "use strict";

  $(document).on("click", ".jialiub-bookmark-button", function (e) {

    e.preventDefault();

    // Get current element
    let el = $(this);

    // Get wrapper
    let wrapper = $(this).closest(".jialiub-bookmark");

    // Get post ID
    let post_id = wrapper.data("post-id");

    $.ajaxSetup({ cache: false });

    $.ajax({
      type: "POST",
      url: jialiub_ajax.ajaxurl,
      data: {
        nonce: jialiub_ajax.nonce,
        post_id: post_id,
        action: "jialiub_bookmark_toggle_ajax",
      },
      beforeSend: function () {
        // Something before send;
      },
      error: function (xhr) {
        if( xhr?.responseJSON?.message ) {
          Notiflix.Notify.failure(xhr?.responseJSON?.message);
        } else {
          Notiflix.Notify.failure(jialiub_translate_handler.error_occurred);
        }
      },
      success: function (response) {
        if (response.bookmark_exist !== true) {
          el.removeClass("jialiub-bookmark-button-active");
          el.find(".jialiub-icon")
            .removeClass("fa-solid")
            .addClass("fa-regular");
        } else {
          el.addClass("jialiub-bookmark-button-active");
          el.find(".jialiub-icon")
            .removeClass("fa-regular")
            .addClass("fa-solid");
        }
        el.find('.jialiub-bookmark-count').html(
          response.bookmarks_count > 0 ? `(${response.bookmarks_count})` : ""
        );
        el?.find('.jialiub-bookmark-label').html(
         `${response.bookmarks_label}`
        );
      },
      complete: function () {
        // Something after complete;
      },
    });
  });
});
