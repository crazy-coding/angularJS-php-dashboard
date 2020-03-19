var langCode = 'en';
switch(lang) {
	case "arabic":
		langCode = "ar";
		break;
	case "bangla":
		langCode = "bn";
		break;
	case "french":
		langCode = "fr";
		break;
	case "germany":
		langCode = "de";
		break;
	case "hindi":
		langCode = "hi";
		break;
	case "spanish":
		langCode = "es";
		break;
	default:
      langCode = 'en';
      break;
}
var storeApp = (function ($) {
	"use strict";
	return {
	 	datePicker: function() {
      		$("input[type=\"date\"]").each(function() {
      			$(this).attr("type", "text");
      			$(this).datepicker({
      				language: langCode,
      				format: "yyyy-mm-dd",
      				autoclose:true,
      				todayHighlight: true
      			});
      		});
		}
		,timePicker: function() {
      		$(".showtimepicker").timepicker();
		}
		,select2: function() {
			$("select").select2({
			  tags: true,
			  "width": "100%",
			  "height": "50px",
			});
			$("select").on("select2:select", function (e) {
			  // var data = e.params.data;
			});
		}
		,modalAnimation: function() {
			$(".modal").on("show.bs.modal", function (e) {
			      $(".modal .modal-dialog").attr("class", "modal-dialog  flipInX  animated"); //bounceIn, pulse, lightSpeedIn,bounceInRight
			});
			$(".modal").on("hide.bs.modal", function (e) {
			      $(".modal .modal-dialog").attr("class", "modal-dialog  flipOutX  animated");
			});
		}
		,generateCardNo: function(x) {
		    if(!x) { x = 16; }
		    var chars = "1234567890";
		    var no = "";
		    for (var i=0; i<x; i++) {
		       var rnum = Math.floor(Math.random() * chars.length);
		       no += chars.substring(rnum,rnum+1);
		   }
		   return no;
		}
		,playSound: function(name, path) {
			path = path ? path : window.baseUrl + '/assets/itsolution24/mp3/' + name;
		  	var audioElement = document.createElement('audio');
		  	audioElement.setAttribute('src', path);
	  		if(typeof audioElement.play === 'function') {
		  		audioElement.play();
		  	}
		}
		,getBase64FromImageUrl: function(url, callback) {
		    var img = new Image();
				img.crossOrigin = "anonymous";
		    img.onload = function () {
		        var canvas = document.createElement("canvas");
		        canvas.width =this.width;
		        canvas.height =this.height;
		        var ctx = canvas.getContext("2d");
		        ctx.drawImage(this, 0, 0);
		        var dataURL = canvas.toDataURL("image/png");
		        var o = dataURL.replace(/^data:image\/(png|jpg);base64,/, "");
		        callback(o);
		    };
		    img.src = url;
		}
		,bootBooxHeightAdjustment: function() {
			$(document).find(".bootboox-container").css({"height":$(window).height()-150});
		}
      	,init: function () {

      		// Showing live datetime at topbar
      		if ($("#live_datetime").length) {
      			window.liveDateTime('live_datetime');
      		}
			
			// initiate date picker
      		this.datePicker();

      		// initiate time picker
      		this.timePicker();

      		// inititate select2
      		this.select2();

      		// initiate beautiful bootstrap modal animation
      		this.modalAnimation();

	      	//fixed main sidebar according to last element position
	      	var sidebar = $(".main-sidebar");
			var fixSidebarTop = 0;
	      	setTimeout(function() {
	      		if ($(window).scrollTop() > 0) {
		      		$(window).trigger("scroll");
		      	}
		     }, 500);
		    $(window).scroll(function () {
		    	var winHeight = parseInt($(window).height());
		    	var winScrollTop = parseInt($(window).scrollTop());
		    	var totalDistance = parseInt((winHeight + winScrollTop) - 95);
		    	if ($("#sidebar-bottom").length) {
		    	var sidebarBottomPos = parseInt($("#sidebar-bottom").offset().top);
			        if (sidebarBottomPos <= totalDistance) {
			            if (!fixSidebarTop) {
			            	fixSidebarTop = winScrollTop;
			            }
			            sidebar.addClass("fixed-sidebar").css({"top":"-" + fixSidebarTop + "px"});
			        }
			        if (winScrollTop <= fixSidebarTop) {
			        	sidebar.removeClass("fixed-sidebar").css({"top": 0});
			        }
			        if (winScrollTop && winScrollTop  < fixSidebarTop) {
			        	sidebar.addClass("fixed-sidebar").css({"top":"-" + 370 + "px"});
			        }
			        if (winScrollTop && winScrollTop  > fixSidebarTop) {
			        	sidebar.addClass("fixed-sidebar").css({"top":"-" + 370 + "px"});
			        }
			        if (winScrollTop && winScrollTop == fixSidebarTop) {
			        	sidebar.addClass("fixed-sidebar").css({"top":"-" + 370 + "px"});
			        }
			    }
		    });

			// Scrollbar
			$("#side-panel, .dashboard-widget, .scrolling-list, .dropdown-menu").perfectScrollbar();
			var t = setInterval(function() {
		        if ($(".scrolling-list").length) {
		            $(".scrolling-list").perfectScrollbar();
		            clearInterval(t);
		        }
		    }, 500);

			//Notification options
			window.toastr.options = {
			  "closeButton": true,
			  "debug": false,
			  "newestOnTop": false,
			  "progressBar": false,
			  "positionClass": "toast-bottom-left",
			  "preventDuplicates": true,
			  "onclick": null,
			  "showDuration": "300",
			  "hideDuration": "1000",
			  "timeOut": "5000",
			  "extendedTimeOut": "1000",
			  "showEasing": "swing",
			  "hideEasing": "linear",
			  "showMethod": "fadeIn",
			  "hideMethod": "fadeOut"
			};

			// Expand collapse supplier stock products
			$(".supplier_title").on("click", function () {
				$(this).hasClass("active") ? $(this).removeClass("active") : $(this).addClass("active");
			    var panel = $(this).data("panel");
			    $("#"+panel).toggle("fast");
			});

			// Generate random number
		  	$(".random_num").click(function(){
		    	$(this).parent(".input-group").children("input").val(storeApp.generateCardNo(8));
		  	});

		  	// Generate random card no
		  	$(".random_card_no").click(function(){
		    	$(this).parent(".input-group").children("input").val(storeApp.generateCardNo(16));
		  	});
		  	if ($(".random_card_no").length > 0) {
			  	setTimeout(function() {
				    $(".random_card_no").trigger("click");
				}, 1000);
		  	}

		  	// Filter box
		  	$("#show-filter-box").on("click", function(e) {
		        e.preventDefault();
		        $("#filter-box").slideDown("fast");
		        $("body").toggleClass("overlay");
		    });

		    $("#close-filter-box").on("click", function(e) {
		        e.preventDefault();
		        $("#filter-box").slideUp('fast');
		        $("body").toggleClass("overlay");
		    });

		    // Generate gift card no.
		    $('#genNo').click(function(){
		        var no = generateCardNo();
		        $(this).parent().parent('.input-group').children('input').val(no);
		        return false;
		    });
		}
   };
}(window.jQuery));

window.jQuery(window).on("load", function () {
	window.jQuery.fn.extend({
	  	flash: function (color, time) {
	       var ele = this;
		    window.jQuery("html, body").animate({
		        scrollTop: ele.offset().top - 100
		    }, 500);
		    var originalColor = ele.css("background");
		    ele.css("background", color);
		    setTimeout(function () {
		      ele.css("background", originalColor);
		    }, time);
	   	},
	});

	// initiate storeApp
	storeApp.init();
});

// Toggling browser full screen
function toggleFullScreenMode () {
    if ((document.fullScreenElement && document.fullScreenElement !== null) ||
            (!document.mozFullScreen && !document.webkitIsFullScreen)) {
        if (document.documentElement.requestFullScreen) {
            document.documentElement.requestFullScreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullScreen) {
            document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
}