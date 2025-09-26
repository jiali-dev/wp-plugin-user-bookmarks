jQuery(function ($) {
  "use strict";

  let table = new DataTable(".jialiub-bookmarks-table", {
    responsive: true,
    pageLength: 10, // how many rows per page
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "All"],
    ], // dropdown for rows
    paging: false, // enable pagination
    searching: true, // optional: enable search
    ordering: true, // optional: enable sorting
  });
});
