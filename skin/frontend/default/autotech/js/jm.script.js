if (window.jQuery && jQuery.noConflict && (typeof $('body') == 'object')) {
	jQuery.noConflict();
/**
	 * ja tabs plugin
	 */
	(function($) { 
				$.fn.jaContentTabs = function (_options){
					return this.each( function() {		
						var container = $( this );
						new $.jaContentTabs().setup( this, container );
					} );
				}
				 $.jaContentTabs = function() { 
					var self = this;
					this.lastTab = null;
					this.nextTab=null;
					this.setup=function( obj, o ){
						var contentTabs = o.children( 'div.ja-tab-content' );
						var nav = o.children( 'ul.ja-tab-navigator' );
						contentTabs.children('div').hide();
						nav.children('li:first').addClass('first').addClass( 'active' );	
						contentTabs.children('div:first').show();
						
						nav.children('li').children('a').click( function() {
							self.lastTab = 	nav.children('li.active').children('a').attr('href');										
							nav.children('li.active').removeClass('active')											
							$(this).parent().addClass('active');
							var currentTab = $(this).attr('href'); 
							self.tabActive( contentTabs, currentTab );		
							return false;
						});	
					};
					this.tabActive=function( contentTabs,  currentTab ){
						if(  this.lastTab != currentTab ){
							contentTabs.children( this.lastTab ).hide();	
						}
			
						contentTabs.children( currentTab ).show();
					};
				};
			})(jQuery);
}
function switchFontSize (ckname,val){
	var bd = $$('body')[0];
	switch (val) {
		case 'inc':
		if (CurrentFontSize+1 < 7) {
			bd.removeClassName('fs'+CurrentFontSize);
			CurrentFontSize++;
			bd.addClassName('fs'+CurrentFontSize);
		}
		break;
		case 'dec':
		if (CurrentFontSize-1 > 0) {
			bd.removeClassName('fs'+CurrentFontSize);
			CurrentFontSize--;
			bd.addClassName('fs'+CurrentFontSize);
		}
		break;
		default:
		bd.removeClassName('fs'+CurrentFontSize);
		CurrentFontSize = val;
		bd.addClassName('fs'+CurrentFontSize);
	}
	createCookie(ckname, CurrentFontSize,365);
}

function switchTool (ckname, val) {
	createCookie(ckname, val, 365);
	window.location.reload();
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, ""); };

function menuFistLastItem () {
	if ((menu = $('nav')) && (children = menu.childElements()) && (children.length)) {
		children[0].addClassName ('first');
		children[children.length-1].addClassName ('last');
	}
}

//Add span to page title
jQuery.fn.addSpan = function() {
	jQuery(this).each(function(){
		jQuery(this).html('<span>'+jQuery (this).text()+'</span>');
	});
};

document.observe("dom:loaded", function() {   
	
	backtotop = jQuery('<a href="#Top" class="btn-btt" title="Back to Top" id="button-btt" style="display: inline;"><i class="fa fa-caret-up"></i>Top</a>');
	backtotop.appendTo('body');
	// hide #back-top first
	backtotop.hide();
	
	// fade in #back-top
	jQuery(function () {
		jQuery(window).scroll(function () {
			if (jQuery(this).scrollTop() > 100) {
				backtotop.fadeIn();
			} else {
				backtotop.fadeOut();
			}
			
			if(jQuery(this).scrollTop() == 0){
				jQuery("#jm-head").removeClass("headscroll");
				jQuery("#jm-quickaccess").before(jQuery("#jm-mycart"));
				jQuery("#jm-search").css("margin-right",187);
			}else if(!jQuery("#jm-head").hasClass("headscroll")){
				if(jQuery(window).width() > 984 ){
                    jQuery("#jm-search").before(jQuery("#jm-mycart"));
                    jQuery("#jm-search").css("margin-right",0);
				} 
                jQuery("#jm-head").addClass("headscroll");
			}
			
		});
        jQuery(window).resize(function(){
				if(jQuery(window).width() < 984){
					jQuery("#jm-quickaccess").before(jQuery("#jm-mycart"));
				}else{
					jQuery("#jm-search").before(jQuery("#jm-mycart"));
                    jQuery("#jm-search").css("margin-right",0);
				}
		    });
	    jQuery(document).ready(function(){

	    	if(jQuery(window).width() > 984 && jQuery(this).scrollTop() > 0 ){

                jQuery("#jm-search").before(jQuery("#jm-mycart"));
                jQuery("#jm-search").css("margin-right",0);
	    	}
	    	jQuery("#jm-mycart").css("display","block");
	    	
	    });
		// scroll body to 0px on click
		backtotop.click(function () {
			jQuery('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
	
	
	// initially hide all containers for tab content $$('div.tabcontent').invoke('hide'); 
	menuFistLastItem();
	navMouseHover();
	//jQuery ('.page-title h1').addSpan();
	jQuery(".block-content .currently ol").children().last().addClass("last");
}); 

//Add hover event for li - hack for IE6
function navMouseHover () {
	var lis = $$('#nav li');
	if (lis && lis.length) {
		lis.each (function(li){
			li.onMouseover = toggleMenu (li, 1);
			li.onMouseout = toggleMenu (li, 0);
		});
	}
}

toggleMenu = function (el, over) {
	  var iS = false;
    if (Element.childElements(el) && Element.childElements(el).length > 1) {
	    var uL = Element.childElements(el)[1];
			iS = true;
		}
    if (over) {
        Element.addClassName(el, 'over');
				Element.addClassName (el.down('a'), 'over');
        if(iS){ uL.addClassName('shown-sub')};
    }
    else {
        Element.removeClassName(el, 'over');
				Element.removeClassName (el.down('a'), 'over');
        if(iS){ uL.removeClassName('shown-sub')};
    }
}
