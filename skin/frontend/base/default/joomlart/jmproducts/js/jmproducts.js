// JavaScript Document
(function($){

   var defaults  = {
   	   productsgrid:{},
   	   istable:0,
   	   qtytable:10,
   	   qtytableportrait:10,
       qtymobile:4,
       qtymobileportrait:4,
   	   ismobile:0,
   	   qtymobile:4,
   	   qtymobileportrait:4
   } 

   var jmproduct = function(options){
      this.elm = options.productsgrid;
      this.options = $.extend({}, defaults, options);
  	  this.initialize(); 
   }
   jmproduct.prototype = {

      initialize:function(){
        
         if(this.options.istable){   
             $(window).resize($.proxy(function(){
              
              if($(window).width() < 780 && this.options.qtytableportrait < this.elm.children("li.item").length){
                  diff = this.options.qtytableportrait - 1;
                  this.elm.children("li.item:gt("+diff+")").css("display","none");
              }else if($(window).width() > 780 && this.options.qtytable < this.elm.children("li.item").length){
                  diff = this.options.qtytable - 1;
                  this.elm.children("li.item:gt("+diff+")").css("display","none");
              }else{
              	  this.elm.children("li.item").css("display","block");
              }

             },this));

         }

         if(this.options.ismobile){
                
            $(window).resize($.proxy(function(){
              if($(window).width() < 361 && this.options.qtymobileportrait < this.elm.children("li.item").length){
                 diff = this.options.qtymobileportrait - 1;
                 this.elm.children("li.item:gt("+diff+")").css("display","none"); 
              }else if($(window).width() >= 361 && this.options.qtymobile < this.elm.children("li.item").length){
                  diff = this.options.qtymobile - 1;
                  this.elm.children("li.item:gt("+diff+")").css("display","none");
              }else{
                  this.elm.children("li.item").css("display","block"); 
              }

            },this))
         }
         this.reloadtab(); 
      },

      reloadtab:function(){
         if($("ul.jm-tabs-title li.active")){
            $("ul.jm-tabs-title li.active").trigger("click");
         }
      }


   }
   $.fn.jmproduct = function(options){
     
      options.productsgrid = $(this);
   	  opotions = $.extend({},options);
    	new jmproduct(options);
		
   };

})(jQuery);

