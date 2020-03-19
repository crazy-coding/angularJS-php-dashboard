$(window).on("load", function() {
	// hide pos skeleton
	var $skeleton = $("#skeleton");
  	$skeleton.fadeOut(100);	
});

$(document).ready(function() {

	var $pos = $(".pos-content-wrapper");
	$pos.fadeOut(100).fadeIn(500);

	var topBar = 40;

	var adjustLayout = function () {

		var windowHeight 	=  $(window).outerHeight();

		var searchbox 		= $("#searchbox").outerHeight();
		var totalAmountArea = $("#total-amount").outerHeight();
		var customerArea 	= $("#customer-area").outerHeight();
		var itemHead 	 	= $("#invoice-item-head").outerHeight();
		var totalCalc 		= $("#invoice-calculation").outerHeight();
		var payButton 		= $("#pay-button").outerHeight();

		$("#item-list").css({height:windowHeight - (searchbox + totalAmountArea + topBar)});
		$("#invoice-item-list").css({height:windowHeight - (customerArea + itemHead + totalCalc + payButton+topBar) + 2});
	};

	$(window).resize(function () {
		adjustLayout();
	});

	$("#item-list, #invoice-item-list, #customer-dropdown").perfectScrollbar();

	// Adjust POS Layout
	adjustLayout();

	// Show live clock at topbar
	window.liveDateTime('live_datetime');

	$("#minicart").on("click", function() {
		var rightPanel = $("#right-panel");

		if (rightPanel.hasClass("visible")) {
			rightPanel.removeClass("visible");
		} else {
			rightPanel.addClass("visible");
		}
	});
});