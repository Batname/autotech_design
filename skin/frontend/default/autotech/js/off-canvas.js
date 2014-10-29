/** 
 *------------------------------------------------------------------------------
 * @package       T3 Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2013 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github 
 *                & Google group to become co-author)
 * @Google group: https://groups.google.com/forum/#!forum/t3fw
 * @Link:         http://t3-framework.org 
 *------------------------------------------------------------------------------
 */

// JavaScript Document

var $ja = jQuery.noConflict(); 

!function($){
	function getIEVersion() {
        var rv = -1; // Return value assumes failure.
        if (navigator.appName == 'Microsoft Internet Explorer') {
            var ua = navigator.userAgent;
            var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
            if (re.test(ua) != null)
                rv = parseFloat( RegExp.$1 );
        }
        return rv;
    }
	
	var ver = getIEVersion();

	if (ver<1 || ver >= 9) {


		$(document).ready(function(){
            
             jQuery(window).resize (function() {
             	//  hideNav();
             	//  hideNav("right");
             })
           
			var $btn = $('#jm-mainnav .btn-toggle'),
				$nav = null,
				$fixeditems = null;
           
           
			if (!$btn.length) return;

			$nav = $('<div class="jm-mainnav" />').appendTo($('<div id="off-canvas-nav"></div>').appendTo($("#jm-wrapper")));
            
            $searchbarHtml = $("#search_mini_form").parent().html();
            $searchbarHtml = $searchbarHtml.replace('search_mini_form', 'search_mini_form_offcanvas');
            $searchbarHtml = $searchbarHtml.replace('id="search"','id="search_offcanvas"').replace('search_autocomplete','search_autocomplete_offcanvas');
            $nav.html($searchbarHtml);
			$("#jm-megamenu ul.level0").clone().appendTo($nav);
	
			$('html').addClass ('off-canvas');

			$btn.click (function(e){
             	if ($(this).data('off-canvas') == 'show') {
					hideNav();
				} else {
					showNav();
				}

				return false;
			});

			posNav = function () {
				var t = $(window).scrollTop();
				if (t < $nav.position().top) $nav.css('top', t);
			};
            posNavRight = function () {
				var t = $(window).scrollTop();
				if (t < $cart.position().top) $cart.css('top', t);
			};

			bdHideNav = function (e) {
               
				if($(e.target).attr("id") == "cboxClose"){
                    return false; 
				}
               
				e.preventDefault();
				hideNav();
				return false;
			};

			showNav = function (side) {

                
                if(jQuery("#jmoffcanvasdim").length <= 0 && jQuery(window).width() < 984){
					jmoverlay = jQuery('<div id="jmoffcanvasdim" class="jmoffcanvasdim"></div>'); 
					jmoverlay.appendTo('#ja-wrapper');
					jmoverlay.bind("click",function(e){
						bdHideNav(e);
					})
			    }	               


				if(side == "right"){

					mainwidth = $("#ja-wrapper").width();
					mycartwidth = $("#off-canvas-right .jm-mycart").width();
					transformwidth = mainwidth + mycartwidth;
					transform = "translateX("+transformwidth+"px)";

					$("#off-canvas-right .jm-mycart").css({"transform":transform,"-webkit-transform":transform,"-moz-transform":transform,"-o-transform":transform});
					$cart.css('top', $(window).scrollTop());
					wpfix(1);
					
					setTimeout (function(){
						$btncart.data('off-canvasright', 'show');
						$('html').addClass ('off-canvasright-enabled');
						$(window).bind('scroll touchmove', posNavRight);

						// hide when click on off-canvas-right
						$('#off-canvas-right').bind ('click', function (e) {
							e.stopPropagation();
						});
						
						//$('#off-canvas-right a').bind ('click', hideNav);
						$('body').bind ('click', bdHideNav);
					}, 50);

					setTimeout (function(){
						wpfix(2);
					}, 1000);
			    }else{
			    	$nav.css('top', $(window).scrollTop());
					wpfix(1);
					
					setTimeout (function(){
						$btn.data('off-canvas', 'show');
						$('html').addClass ('off-canvas-enabled');
						$(window).bind('scroll touchmove', posNav);

						// hide when click on off-canvas-nav
						$('#off-canvas-nav').bind ('click', function (e) {
							e.stopPropagation();
						});
						
						$('#off-canvas-nav a').bind ('click', hideNav);
						$('body').bind ('click', bdHideNav);
					}, 50);

					setTimeout (function(){
						wpfix(2);
					}, 1000);
			    }
			};

			hideNav = function (side) {		
                 
                if(jQuery("#jmoffcanvasdim").length > 0){
                   jQuery("#jmoffcanvasdim").remove();
                } 
				$(window).unbind('scroll touchmove', posNav);
				$('body').unbind ('click', bdHideNav);
				
				$('html').removeClass ('off-canvas-enabled');
				$btn.data('off-canvas', 'hide');
             	setTimeout (function(){
				}, 1000);

			};

			wpfix = function (step) {
				// check if need fixed
				if ($fixeditems == -1) return ;// no need to fix
				if (!$fixeditems) {
					$fixeditems = $('body').children().filter(function(){ return $(this).css('position') === 'fixed' });
					if (!$fixeditems.length) {
						$fixeditems = -1;
						return ;
					}
				}

				if (step==1) {
					$fixeditems.css({'position': 'absolute', 'top': $(window).scrollTop()+'px'});
				} else {
					$fixeditems.css({'position': '', 'top': ''});
				}
			}

		})
	}
}(jQuery);