jQuery(function ($) {
  "use strict";

  let user_bookmarks_table = new DataTable(".jialiub-user-bookmarks-table", {
    processing: true,
    serverSide: true,
    ajax: {
      url: jialiub_ajax.ajaxurl,
      type: "POST",
      data: function (d) {
        d.action = "jialiub_get_user_bookmarks_ajax";
        d.nonce = jialiub_ajax.nonce;
      },
    },
    columns: [{ title: "Title" }, { title: "Author" }],
    pageLength: 10,
    lengthChange: false,
    responsive: true,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "All"],
    ], // dropdown for rows
    paging: true, // enable pagination
    searching: false, // optional: enable search
    ordering: false, // optional: enable sorting
  });

  let top_bookmarks = new DataTable(".jialiub-top-bookmarks-table", {
    processing: true,
    serverSide: true,
    ajax: {
      url: jialiub_ajax.ajaxurl,
      type: "POST",
      data: function (d) {
        d.action = "jialiub_get_top_bookmarks_ajax";
        d.nonce = jialiub_ajax.nonce;
      },
    },
    columns: [{ title: "Title" }, { title: "Author" }, { title: "Count" }],
    pageLength: 10,
    lengthChange: false,
    responsive: true,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "All"],
    ], // dropdown for rows
    paging: false, // enable pagination
    searching: false, // optional: enable search
    ordering: false, // optional: enable sorting
  });
});
