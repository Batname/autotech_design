// JavaScript Document
function showOptions(id){
   
   jQuery("#optionsbox"+id).trigger("click");
}
document.observe("dom:loaded", function() {
										
	var productmap = [];
	var showprev = true,shownext = true;
	jQuery(".jmquickview").each(function(i,v){
		productmap[i] =  jQuery(v).attr("id");
		jQuery(v).data("order",i);
	});
	
	jQuery(".jmquickview").bind("click",function(){
        loading = jQuery(this).siblings(".jmloading");												 
		loading.show();
		corder = jQuery(this).data("order");
		porder = corder - 1;
		norder = corder + 1;
		showprev = (porder < 0 )?false:true;
		shownext = (norder >= productmap.length)?false:true;
	    pid = jQuery(this).attr("rel");
	    
		jQuery.post('jmquickview/index',{id:pid}, function(data) {
														   
			    if(jQuery("#jmquickviewpopup").length <= 0){
					  jmquickviewpopup = jQuery('<div id="jmquickviewpopup" class="jmquickviewpopup"><div id="jmquickview-wrapper"><div id="jmquickview-inner"></div></div></div>'); 
					  jmquickviewpopup.appendTo('body');
					}	
					loading.hide();
					jQuery("#jmquickview-inner").html('');
					jQuery("#jmquickview-inner").append(data);
					jQuery("#jmquickview-inner").height("");
					var next = jQuery(".jmquickview-navigation .next");
	                var prev = jQuery(".jmquickview-navigation .prev");
					next.hide();
					prev.hide();
					jQuery(".navigateloading").hide();
					if(shownext){
						next.show();
						jQuery(".next").bind("click",function(){
								productid = productmap[norder];
								jQuery(".navigateloading").show();
								jQuery("#"+productid).trigger("click");
						});
					}
					if(showprev){
						prev.show();
						jQuery(".prev").bind("click",function(){
								productid = productmap[porder];
								jQuery(".navigateloading").show();
								jQuery("#"+productid).trigger("click");
						});
						
					}
					wrapperleft = (jQuery(window).width() - jQuery("#jmquickview-wrapper").width())/2;
					wrappertop = (jQuery(window).height() - jQuery("#jmquickview-wrapper").height())/2;
					jQuery("#jmquickview-wrapper").css({"left":wrapperleft,"top":wrappertop});
					
					jQuery("div.jmclose,button.jmcontinue").bind("click",function(){
							jQuery("#jmquickviewpopup").remove();
							window.quickviewIScrol.destroy();
							window.quickviewIScrol = null;
					});
					
					jQuery(".jmgocart").bind("click",function(){
				  	   location.href = "checkout/cart/";									
					});
					
					jQuery("#jmquickview-inner").height(jQuery("#jmquickview-inner").height()+60);
					if(window.quickviewIScrol == null ){
						  setTimeout(function(){
												window.quickviewIScrol = new iScroll('jmquickview-wrapper',{vScrollbar: true, useTransform: true,hScrollbar: false})},500);
					}else{
						  window.quickviewIScrol.refresh();
					} 
					/*jmquickviewpopup.bind("click",function(){
							this.remove();
					});*/
        });
	});
}); 