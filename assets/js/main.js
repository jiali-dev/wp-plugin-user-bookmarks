jQuery(function ($) {
  "use strict";

  $(document).on("click", ".jialiub-bookmark-button", function (e) {
    e.preventDefault();

    const el = $(this);
    const wrapper = $(this).closest(".jialiub-bookmark");
    const post_id = wrapper.data("post-id");

    if (!post_id) {
      console.warn("No post ID found for bookmark button.");
      return;
    }

    const countEl = el.find(".jialiub-bookmark-count");
    let count = parseInt(countEl.text().replace(/\D/g, "")) || 0;
    const oldCount = count;
    const isActive = el.hasClass("jialiub-bookmark-button-active");

    // ===== OPTIMISTIC UPDATE =====
    el.toggleClass("jialiub-bookmark-button-active", !isActive);
    el.find(".jialiub-icon")
      .toggleClass("fa-solid", !isActive)
      .toggleClass("fa-regular", isActive);
    count = isActive ? Math.max(0, count - 1) : count + 1;
    countEl.html(count > 0 ? `(${count})` : "");

    // Disable button during AJAX
    el.prop("disabled", true);

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

        // Revert optimistic update
        el.toggleClass("jialiub-bookmark-button-active", isActive);
        el.find(".jialiub-icon")
          .toggleClass("fa-solid", isActive)
          .toggleClass("fa-regular", !isActive);
        countEl.html(oldCount > 0 ? `(${oldCount})` : "");
      },
      success: function (response) {
        const { bookmark_exist, bookmarks_count, bookmarks_label } = response;

        // Correct UI based on server response
        el.toggleClass("jialiub-bookmark-button-active", !!bookmark_exist);
        el.find(".jialiub-icon")
          .toggleClass("fa-solid", !!bookmark_exist)
          .toggleClass("fa-regular", !bookmark_exist);
        countEl.html(bookmarks_count > 0 ? `(${bookmarks_count})` : "");
        el.find(".jialiub-bookmark-label").html(bookmarks_label || "");
      },
      complete: function () {
        // Re-enable button
        el.prop("disabled", false);
      },
    });
  });
});
