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
});