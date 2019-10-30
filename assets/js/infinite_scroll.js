$(() => {
    let userLoggedIn = "<?php echo $userLoggedIn; ?>";
    let inProgress = false;

    loadPosts = () => {
        if (inProgress) {
            return;
        }

        inProgress = true;
        $("#loading").show();

        let page =
            $(".posts_area")
                .find(".nextPage")
                .val() || 1;

        $.ajax({
            url: "includes/handlers/ajax_load_posts.php",
            type: "POST",
            data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
            cache: false,

            success: response => {
                $(".posts_area")
                    .find(".nextPage")
                    .remove();
                $(".posts_area")
                    .find(".noMorePosts")
                    .remove();
                $(".posts_area")
                    .find(".noMorePostsText")
                    .remove();

                $("#loading").hide();
                $(".posts_area").append(response);

                inProgress = false;
            }
        });
    };

    loadPosts();

    $(window).scroll(() => {
        let bottomElement = $(".status_post").last();
        let noMorePosts = $(".posts_area")
            .find(".noMorePosts")
            .val();

        if (isElementInView(bottomElement[0]) && noMorePosts == "false") {
            loadPosts();
        }
    });

    isElementInView = el => {
        let rect = el.getBoundingClientRect();

        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <=
                (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
            rect.right <=
                (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
        );
    };
});
