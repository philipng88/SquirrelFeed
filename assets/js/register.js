$(document).ready(() => {
    $("#signup").click(() => {
        $("#login_form").slideUp("slow", () => {
            $("#register_form").slideDown("slow")
        })
    })

    $("#signin").click(() => {
        $("#register_form").slideUp("slow", () => {
            $("#login_form").slideDown("slow")
        })
    })
});