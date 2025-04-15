jQuery(function ($) {
    "use strict";
  
    $(".jialiufl-favorite-and-like-button").on("click", function () {
        // Get current element
        let el = $(this);
    
        // User action
        let user_action = el.data("action");

        // Get wrapper
        let wrapper = $(this).closest(".jialiufl-favorite-and-like");

        // Get post ID
        let post_id = wrapper.data("post-id");  
  
        $.ajaxSetup({ cache: false });
    
        $.ajax({
            type: "POST",
            url: jialiufl_ajax.ajaxurl,
            data: {
                nonce: jialiufl_ajax.nonce,
                user_action: user_action,
                post_id: post_id,
                action: 'jialiufl_favorite_and_like_toggle_ajax',
            },
            beforeSend: function () {
                // Something before send;
            },
            error: function (xhr) {
                Notiflix.Notify.failure(xhr.responseJSON.message);
            },
            success: function (response) {
                if( response.user_action_exist === true ) {
                    el.removeClass("jialiufl-favorite-and-like-button-active");
                    el.find(".jialiufl-icon").removeClass("fa-solid").addClass("fa-regular");
                }
                else {
                    el.addClass("jialiufl-favorite-and-like-button-active");
                    el.find(".jialiufl-icon").removeClass("fa-regular").addClass("fa-solid");
                }
                el.find(`.jialiufl-${user_action}-count`).html( response.user_action_count > 0 ? response.user_action_count : '' );
            },
            complete: function () {
                // Something after complete;
            },
        });
    });

  });
