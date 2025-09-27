jQuery(function ($) {
  "use strict";

  let table = new DataTable(".jialiub-bookmarks-table", {
    processing: true,
    serverSide: true,
    ajax: {
        url: jialiub_ajax.ajaxurl,
        type: 'POST',
        data: function(d){
            d.action = 'jialiub_get_user_bookmarks_ajax';
            d.nonce = jialiub_ajax.nonce;
        }
    },
    columns: [
        { title: "Title" },
        { title: "Author" },
    ],
    pageLength: 10,
    lengthChange: false,
    responsive: true,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "All"],
    ], // dropdown for rows
    paging: true, // enable pagination
    searching: true, // optional: enable search
    ordering: true, // optional: enable sorting
  });
});
