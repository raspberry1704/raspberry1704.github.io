$(document).ready(function(){
	/* Nombre caractère text area */
	var nbCaracMax = 500;

	$("#descriptionInput").focus( function() {
			var elem = document.createElement("div");
			elem.id = "nbCarac";
			var text = document.createElement("p");
			text.innerHTML= $(this).val().length + "/" + nbCaracMax;	
			elem.append(text);
			$(this).parent().append(elem);
	});

	$("#descriptionInput").focusout( function() { $("#nbCarac").remove(); });


	$("#descriptionInput").keyup( function() {
		var result = $(this).val().length + "/" + nbCaracMax;
		$("#nbCarac").find("> p").text(result);
	});

	
	/* Messages alertes/confirmations boutons <supprimer> */
	$('.actionSuppression').click ( function () {
		if(window.confirm('La suppression de ce livre est définitive\n.Voulez-vous vraiment supprimer le livre "' + $(this).parent().parent().parent().find("h2").text() +  '" ?\n\n\n')) {
			return true;
		} else {
			return false;
		}
	});

	$('.actionSuppressionCompte').click ( function () {
		if(window.confirm('La suppression de votre compte sera irréversible.\nTous vos livres seront supprimmés définitivement.\nConfirmez-vous la suppression de vos livres et de votre compte utilisateur?\n\n\n')) {
			return true;
		} else {
			return false;
		}
	});


	/* Création et configuration de l'ancre */
	if( $("body").height() > 50) {
		/* Création élément */
		var backtotop = document.createElement("div");
		backtotop.id = "backtotop";		
		$("main").append( backtotop );

		/* Configuration Evenement */
		$("#backtotop").addClass("enabled");
		$("#backtotop").click( function() {
			if( $(this).hasClass('enabled') ) {
				$(this).removeClass("enabled");
				$('html, body').animate({
				    scrollTop:$("body").offset().top
				}, 2000, function() { 
					$("#backtotop").addClass("enabled");
				});	
			}
		});
	}

	/* Configuration apparition de l'ancre back to top */
	$(window).scroll(function () { 
		var userScreen = jQuery(window).height();
		var pageHeight = $("body").height();
		
		if( $(window).scrollTop().valueOf() > (userScreen/3) ) {
			$("#backtotop").show();
		}else {
			$("#backtotop").hide();
		}
	});
});

