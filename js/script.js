/**
 * Created by DarkMark on 11/13/2016.
 */


$(document).ready(function() {
    $("#logo-slider > div:gt(0)").hide();

    setInterval(function() {
        $('#logo-slider > div:first')
            .fadeOut(1500)
            .next()
            .fadeIn(1500)
            .end()
            .appendTo('#logo-slider');
    }, 3000);
});