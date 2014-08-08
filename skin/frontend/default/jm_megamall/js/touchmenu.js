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

!function($){
	var isTouch = 'ontouchstart' in window && !(/hp-tablet/gi).test(navigator.appVersion);
	if(isTouch){

		$.fn.touchmenu = function(){
			if(!$(document).data('touchmenu')){
				$(document).data('touchmenu', 1).data('touchitems', $()).on('click hidesub', function(){
					$(document).removeClass('hoverable')
						.data('touchitems').data('noclick', 0).removeClass('open');
				});

				if (navigator.userAgent.match(/(iPad|iPhone);.*CPU.*OS 6_\d/i)){ 
					$(document.body).children(':not(.nav)').on('click', function(){
						$(document).trigger('hidesub');
					});
				}
			}
			
			return this.each(function(){
                
				var	itemsel = $(this).has('.mega').length ? 'li.mega' : 'li.parent',
           		jitems = $(this).find(itemsel),
					reset = function(){
						$(this).data('noclick', 0);
					},
					onTouch = function(e){
						e.stopPropagation();
						
						$(document.body).addClass('hoverable');
                       
						var jitem = $(this),
							val = !jitem.data('noclick');
                     	if(val){
							var jchild = jitem.children('.childcontent'),
								hasopen = jitem.hasClass('open'),
								style = jchild.prop('style'),
								left = style ? style['left'] : 'auto';
                               
							if(jchild.length && (jchild.css('left', '-999em').css('left') !== 'auto')){ //normal or hide when collapse
								
								jchild.css('left', left);
                              
								//at initial state, test if it is display: none !important, 
								//if true, we will open this link (val = 0)
								if(!hasopen){	
									//add open class, 
									//iphone seem have buggy when we modify display property
									//it does not trigger hover CSS
									$(document).data('touchitems').removeClass('open');
									jitem.addClass('open').parentsUntil('.nav').filter(itemsel).addClass('open');

									val = jchild.css('display') != 'none';
								}

							} else { //always show
								val = 0;
							}

							jchild.css('left', left);
						}
                         
						// reset all
						jitems.data('noclick', 0);
						jitem.data('noclick', val);

						if(val){
							$(this) //reset, sometime the mouseenter does not refire, so we reset to enable click
								.data('rsid', setTimeout($.proxy(reset, this), 500))
								.parent().parentsUntil('.nav').filter(itemsel).addClass('open');							
						}
					},
					onClick = function(e){

						e.stopPropagation();
                        
						if($(this).data('noclick')){
							e.preventDefault();
							jitems.removeClass('open');
							$(this).addClass('open').parentsUntil('.nav').filter(itemsel).addClass('open');
						} else {
							var href = $(this).children('a').attr('href');
							if(href){
								window.location.href = href;
							}
						}
					};
				
				jitems.on('mouseenter', onTouch).data('noclick', 0);
				$(this).find('li').on('click', onClick);

				$(document).data('touchitems', $(document).data('touchitems').add(jitems));
			});
		};
	}

	$('html').addClass(isTouch ? 'touch' : 'no-touch');

	$(document).ready(function(){

		if(isTouch){
			$('ul.megamenu').has('.haschild').touchmenu();
		} 
	});
}(jQuery);