// JavaScript Document
(function($){

   var defaults  = {
   	  listitem:{},
   	  btnCart:".btn-cart",
   	  showcartrightaway:true
   } 

   var jmquickview = function(container,options){
      this.options = $.extend({}, defaults, options);
      this.options.container = container;
  	  this.initialize(); 
   }


   jmquickview.prototype = {
   	   bindcartEvents:function(){
   							$("#cart-sidebar .remove").attr('onclick','');
                            $("#cart-sidebar .remove").unbind("click");
							//bind cart's buttons on the header
                            $("#cart-sidebar .remove").bind("click",$.proxy(function(e){
                            	e.stopPropagation();
								e.preventDefault();
								question = $(e.currentTarget).data("confirm");
							    if(confirm(question)){
					    	    	$(".jmajaxcartLoading").show();
					    	    	urldelete = $(e.currentTarget).attr("href");
					    	    	urldelete = urldelete.replace("checkout/cart/delete","quickview/index/deletecartsidebar");
									this.toggleloading();
					    	    	$.ajax({
					                    url:urldelete,
					                    dataType:'json',
					                    success:$.proxy(function(data) {
					                    	//$(".jmajaxcartLoading").hide();
					                        if(data.status == 'ERROR'){
											    this.toggleloading();
												alert(data.message);
											}else{
												this.addComplete();
											}
					                    },this)

					    	    	});

					    	    }
						    },this));

                            $(".jmquickview_cart_form button.btn-update,.jmquickview_cart_form button.btn-empty").unbind("click");
	                        $(".jmquickview_cart_form button.btn-update,.jmquickview_cart_form button.btn-empty").bind("click",$.proxy(function(e){
                               
						       e.preventDefault();
						       //loading();
						       form = $(e.target).parents(".jmquickview_cart_form");
						       urlcart = form.attr('action');
							   urlcart = urlcart.replace("checkout/cart","quickview/index");
							   var datacart = form.serialize();
							   datacart = datacart + "&update_cart_action=" + $(e.target).attr("value");
							   urlcart = urlcart+"?"+datacart
							   this.toggleloading();
							   $.post(urlcart,$.proxy(function(data){
						       	    this.addComplete();
							   },this));
							  

						    },this));



   	   },
       addComplete:function(){

               $.post(baseurl+"quickview/links/index",$.proxy(function(data){
								  if($(".top-link-cart")) $(".top-link-cart").html(data);
								  	  $.post(baseurl+"quickview/links/sumjson",$.proxy(function(data){
									  var result = eval("(" + data + ")");								 
									  str= $("#jm-mycart .btn-toggle a").html();
									  str= str.replace(/\d/,result.sum_items);
									  $("#jm-mycart .btn-toggle a").html(str);
									  
								  },this));
								  $.post(baseurl+"quickview/links/updatecart",$.proxy(function(datacart){
									if($("#jm-mycart .inner-toggle")) {
									    $("#jm-mycart .inner-toggle").html(datacart);
										$("#jm-mycart .btn-toggle").removeClass("active");
									    $("#jm-mycart").trigger("afterupdatecart");
									     this.toggleloading();
									     this.bindcartEvents();
									 }   
								  },this));
			    },this));      
                      
       },
       ajaxaddtocart:function(url){
       	    $.ajax({
			            url:url,
			            dataType:'json',
			            success:$.proxy(function(data) {
			            	
			                if(data.status == 'ERROR'){
								alert(data.message);
							}else{
					                this.addComplete();
			        		}
			            },this)

		    });
       },
       toggleloading:function(){
           if($(".jmajaxloading").css("display") == "none"){
           	  $(".jmajaxloading").show();
           }else{
           	  $(".jmajaxloading").hide();
           }
       },
   	   initialize:function(){
             
            $("#jm-mycart").data("mycartobj",this);
		    this.bindcartEvents();
		    options = this.options;
            $(options.container).find(options.btnCart).each($.proxy(function(index,bcart){
			    productlink = $(bcart).siblings(".add-to-links").find(".link-compare").attr("href");
			    bcartparent = $(bcart).parent();
			    if((productlink != null) && (productlink != undefined) && (product = productlink.match(/product\/\d+/)) && !bcartparent.children("#quickviewbox"+product[0].replace("product/","")).length ){
				      productid = product[0].replace("product/","");
					  quickviewtag = $("<a/>",{
							"rel":"quickviewbox",
							"href":"quickview/index",
							"id":"quickviewbox"+productid,
							"title":"Быстрый просмотр"
					  });
					  quickviewtag.attr("href",baseurl+"quickview/index/index/id/"+productid+"");
					  quickviewtag.append(' <button class="form-button jmquickview"><span>Посмотреть</span></button>');
					  $(bcart).after(quickviewtag);
				      quickviewtag.colorbox({current: "Product {current} of {total}",onComplete:$.proxy(function(){
				             
				              // add product to wishlist 
							  $("a.link-wishlist").bind("click",function(e){
								    e.preventDefault();
									if(!productAddToCartForm.submitLight(this,$(this).attr("href"))) return false;
									ulrwishlist = $(this).attr("href");
									ulrwishlist = ulrwishlist.replace("wishlist/index/add","quickview/wishlist/addwishlist");
									var data = $('.product_addtocart_form').serialize();
									$("#cboxLoadingGraphic").show();
									$.ajax( {
										url : ulrwishlist,
										dataType : 'json',
										type : 'post',
										data : data,
										success : function(data) {
											$("#cboxLoadingGraphic").hide();
											if(data.status == 'ERROR'){
												alert(data.message);
											}else{
												alert(data.message);
												if($('.block-wishlist').length){
													$('.block-wishlist').replaceWith(data.sidebar);
												}else{
													if($('.col-right').length){
														$('.col-right').prepend(data.sidebar);
													}
												}
												if($('.header .links').length){
													$('.header .links').replaceWith(data.toplink);
												}
											}
										}
									});
							  });
							  
							  // add product to compare 
							  $("a.link-compare").bind("click",function(e){
								    e.preventDefault();
									urlcompare = $(this).attr("href");
									urlcompare = urlcompare.replace("catalog/product_compare/add","quickview/index/compare");
									$("#cboxLoadingGraphic").show();
									$.ajax({
										 url:urlcompare,
										 dataType:'json',
										 success : function(data) {
											    $("#cboxLoadingGraphic").hide();
												if(data.status == 'ERROR'){
													alert(data.message);
												}else{
													alert(data.message);
													if($('.block-compare').length){
														$('.block-compare').replaceWith(data.sidebar);
													}else{
														if($('.col-right').length){
															$('.col-right').prepend(data.sidebar);
														}
													}
												}
										 }
									})
							  });
							  
							   $(".optionsboxadd").bind("click",$.proxy(function(e){
                                    
							   	    if(!productAddToCartForm.submit($(this).children("button")[0])) return false;
                                   
			                        e.preventDefault();
			                        urladdcart = $(".product_addtocart_form").attr("action");
			                        urladdcart = urladdcart.replace("checkout/cart","quickview/index"); // New Code
			                        var data = $('.product_addtocart_form').serialize();
									data += '&isAjax=1';
									urladdcart = urladdcart+"?"+data;
			                         if(this.options.showcartrightaway){
			                         	
				                         $.colorbox({href:urladdcart,onComplete:$.proxy(function(){


				                         	 this.addComplete();  
								         },this)});
			                         }else{

			                           urladdcart = urladdcart + "&onlyadd=1";	
			                           this.ajaxaddtocart(urladdcart)	  
							           $("#cboxClose").trigger("click");
							           this.toggleloading();
							         }                  

							  },this));

							  															 
					   
					   },this)});
				 
		         }
	        },this));								 
   	   }
   }

   $.fn.jmquickview = function(options){
   	     	new jmquickview(this,options);
		
   };

   
})(jQuery)


jQuery(window).load(function($) {
    jQuery("ul.products-grid").jmquickview({});
    jQuery("div.products-grid").jmquickview({});
    jQuery("table.products-grid").jmquickview({});
    jQuery("ol.products-list").jmquickview({});
 });  