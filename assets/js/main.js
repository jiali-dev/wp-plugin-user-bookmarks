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

    if (!post_id) {
      console.warn("No post ID found for bookmark button.");
      return;
    }

    $.ajax({
      type: "POST",
      url: jialiub_ajax.ajaxurl,
      data: {
        nonce: jialiub_ajax.nonce,
        post_id,
        action: "jialiub_bookmark_toggle_ajax",
      },
      error: function (xhr) {
        const msg =
          xhr?.responseJSON?.message ||
          jialiub_translate_handler.error_occurred;
        Notiflix.Notify.failure(msg);
      },
      success: function (response) {
        const { bookmark_exist, bookmarks_count, bookmarks_label } = response;

        el.toggleClass("jialiub-bookmark-button-active", !!bookmark_exist);
        el.find(".jialiub-icon")
          .toggleClass("fa-solid", !!bookmark_exist)
          .toggleClass("fa-regular", !bookmark_exist);

        el.find(".jialiub-bookmark-count").html(
          bookmarks_count > 0 ? `(${bookmarks_count})` : ""
        );

        el.find(".jialiub-bookmark-label").html(bookmarks_label || "");
      },
    });
  });
});
