$(document).ready(function() {
    $(".dropdown-menu").hide();
    $(window).resize(function() {
        // This will fire each time the window is resized:
        if($(window).width() >= 801) {
            // if larger or equal
            $(".dropdown-menu").show();
        } else {
            // if smaller
            $(".dropdown-menu").hide();
        }
    }).resize();
});
