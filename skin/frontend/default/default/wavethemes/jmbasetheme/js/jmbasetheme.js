// JavaScript Document
(function($){

   var defaults  = {
       istable:0,
       ismobile:0,
   } 

   var jmbasetheme = function(options){
      this.options = $.extend({}, defaults, options);
      this.initialize(); 
   }
   jmbasetheme.prototype = {

      initialize:function(){
         
        
            if($("#checkout_multishipping_form").length){
               this.checkout = $("#checkout_multishipping_form"); 
            } 
            else if($("div.multiple-checkout").length){
              this.checkout = $("div.multiple-checkout"); 
            }else if($("form#form-validate").length){
              this.checkout = $("form.form-validate");
            }
            if(this.checkout){
              this.parentcheckout =  this.checkout.parent();
              this.movecheckouttoactive();
              $(window).resize($.proxy(function(){
                  this.movecheckouttoactive();
              },this));
            }

            if($("div.category-products ul.products-grid") && !$("div.jm-product-list")){
               $(window).resize($.proxy(function(){
                  this.resetproductgridlayout();
              },this));
            }else if("div.category-products .products-list"){
               $(window).resize($.proxy(function(){
                  this.resetproductlistlayout();
              },this));
            }
              
       
      },
      movecheckouttoactive:function(){
            if($(window).width() < 361){
                  
                  if($("#checkout-progress-state").length){
                        activestate =  $("#checkout-progress-state li.active"); 
                        this.checkout.appendTo(activestate);
                  }
            }else{
                   this.parentcheckout.append(this.checkout);
            }
      },

      resetproductgridlayout:function(){
          //for tablet portrait 
           items = $("ul.products-grid li.item");
           if($(window).width() < 985 && $(window).width() > 720){
              items.css("width",100/this.options.productgridnumbercolumntabletportrait+"%");
              items.find("p.product-image img").css({width:this.options.productgridimagewidthtabletportrait,height:this.options.productgridimagewidthtabletportrait})
           //for mobile portrait    
           }else if($(window).width() < 480){
              items.css("width",100/this.options.productgridnumbercolumnmobileportrait+"%");
              items.find("p.product-image img").css({width:this.options.productgridimagewidthmobileportrait,height:this.options.productgridimageheightmobileportrait})

           }else{
              items.css("width",100/this.options.productgridnumbercolumn+"%");
              items.find("p.product-image img").css({width:this.options.productgridimagewidthportrait,height:this.options.productgridimageheightportrait})
           }
      },
      resetproductlistlayout:function(){
          //for tablet portrait 
           items = $("ul.products-list li.item");
           if($(window).width() < 985 && $(window).width() > 720){
              items.find("p.product-image img").css({width:this.options.productlistimagewidthtabletportrait,height:this.options.productlistimagewidthtabletportrait})
           //for mobile portrait    
           }else if($(window).width() < 480){
              items.find("p.product-image img").css({width:this.options.productlistimagewidthmobileportrait,height:this.options.productlistimageheightmobileportrait})

           }else{
              items.find("p.product-image img").css({width:this.options.productlistimagewidthportrait,height:this.options.productlistimageheightportrait})
           }
      }

   }

   $.fn.jmbasetheme = function(options){
      opotions = $.extend({},options);
      new jmbasetheme(options);
    
   };

})(jQuery);

