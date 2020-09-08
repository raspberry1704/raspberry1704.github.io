$(document).ready(function() {
    $(".navigation").hide();

    $("#bouton_RD").click( function(){
        if( $(".navigation").hasClass("drop-down") ) {
            $(".navigation").hide();
            $(".navigation").addClass("up");
            $(".navigation").removeClass("drop-down");
        } else {
            $(".navigation").show();
            $(".navigation").addClass("drop-down");
            $(".navigation").removeClass("up");
        }
    });
});
