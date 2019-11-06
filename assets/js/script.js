$(document).ready(() => {
    $('[data-toggle="tooltip"]').tooltip();

    $('#post_form').on('shown.bs.modal', () => {
        $('#post_form_content').focus();
    });

    $('#submit_profile_post').click(() => {
        $.ajax({
            type: "POST",
            url: "includes/handlers/ajax_submit_profile_post.php",
            data: $('form.profile_post').serialize(),
            success: msg => {
                $("#post_form").modal('hide');
                location.reload();
            },
            error: () => {
                alert('ERROR: Post could not be submitted');
            }
        });
    });

    $('#search_text_input').focus(function() {
        if (window.matchMedia( "(min-width: 800px)" ).matches) {
            $(this).animate({ width: '250px' }, 500);
        }
    });

    $('.button_holder').on('click', () => {
        document.search_form.submit();
    })

    $("#post_text").emojioneArea({ pickerPosition: "bottom" });
    $("#post_form_content").emojioneArea({ pickerPosition: "right" });
    $("#message_textarea").emojioneArea();
});

$(document).click(e => {
    if (e.target.class != "search_results" && e.target.id != "search_text_input") {
        $(".search_results").html("");
        $('.search_results_footer').html("");
        $('.search_results_footer').toggleClass("search_results_footer_empty");
        $('.search_results_footer').toggleClass("search_results_footer");
    }

    if (e.target.class != "dropdown_data_window") {
        $(".dropdown_data_window").html("");
        $(".dropdown_data_window").css({"padding": "0", "height": "0"});
    }
})

function getUsers(value, user) {
    $.post("includes/handlers/ajax_friend_search.php", { query: value, userLoggedIn: user }, function(data) {
        $(".results").html(data);
    })
}

function getDropdownData(user, type) {
    if($(".dropdown_data_window").css("height") == "0px" || $(".dropdown_data_window").css("height") == "2px") {
        let pageName;
        if (type == 'notification') {
            pageName = "ajax_load_notifications.php";
            $("span").remove("#unread_notification");
        }
        else if (type == 'message') {
            pageName = "ajax_load_messages.php";
            $("span").remove("#unread_message");
        }
        let ajaxreq = $.ajax({
            url: "includes/handlers/" + pageName,
            type: "POST",
            data: "page=1&userLoggedIn=" + user,
            cache: false,
            success: function(response) {
                $(".dropdown_data_window").html(response);
                $(".dropdown_data_window").css({"padding": "0px", "height": "280px", "border": "1px solid #DADADA"});
                $("#dropdown_data_type").val(type);
            }
        })
    } else {
        $(".dropdown_data_window").html("");
        $(".dropdown_data_window").css({"padding": "0px", "height": "0px", "border": "none"});
    }
}

function getLiveSearchUsers(value, user) {
    $.post("includes/handlers/ajax_search.php", { query: value, userLoggedIn: user }, function(data) {
        if ($(".search_results_footer_empty")[0]) {
            $(".search_results_footer_empty").toggleClass("search_results_footer");
            $(".search_results_footer_empty").toggleClass("search_results_footer_empty");
        }

        $('.search_results').html(data);
        // $('.search_results_footer').html("<a href='search.php?=" + value + "'>See All Results</a>");
        $('.search_results_footer').html("<p>Hit Enter/Return to see all results</p>").css({"color": "#fff"});

        if (data == "") {
            $('.search_results_footer').html("");
            $('.search_results_footer').toggleClass("search_results_footer_empty");
            $('.search_results_footer').toggleClass("search_results_footer");
        }
    });
}