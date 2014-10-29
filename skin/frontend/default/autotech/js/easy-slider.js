/*easy-slider custom from jCarouselLite*/


(function($) {                                          // Compliant with jquery.noConflict()
$.fn.easySlider = function(o) {
    o = $.extend({
    	mainImg: null,
        btnPrev: null,
        btnNext: null,
        btnGo: null,
        
        animate: false,
        speed: 300,
        loop: false,

        start: 0,
        scroll: 1,

        beforeStart: null,
        afterEnd: null
    }, o || {});

    return this.each(function() {                           // Returns the element collection. Chainable.

        var running = false, animCss=o.vertical?"top":"left", sizeCss=o.vertical?"height":"width";
        var div = $(this), ul = $("ul", div), tLi = $("li", ul), tl = tLi.size();

        var li = $("li", ul), itemLength = li.size(), curr = o.start,active = curr;
        div.css("visibility", "visible");

        li.eq(curr).addClass("active");   
        if (o.mainImg)
			changeMainImage($("a", li.eq(active)).attr('href'));
        
        li.css({overflow: "hidden", float: o.vertical ? "none" : "left"});
        ul.css({margin: "0", padding: "0", position: "relative", "list-style-type": "none", "z-index": "1"});
        div.css({overflow: "hidden", position: "relative", "z-index": "2", left: "0px"});

        var liSize = o.vertical ? height(li) : width(li);   // Full li size(incl margin)-Used for animation
        var ulSize = liSize * itemLength;                   // size of full ul(total length, not just for the visible items)

        li.css({width: li.width(), height: li.height()});
        ul.css(sizeCss, ulSize+"px").css(animCss, -(curr*liSize));

        
        if(o.btnPrev)
            $(o.btnPrev).click(function() {
            	if (active > 0){
            		active--;
            		changeActiveLi();
            	}else if (o.loop){
            		active = itemLength-1;
            		changeActiveLi();
            	}
                return false;
            });

        if(o.btnNext)
            $(o.btnNext).click(function() {
            	if (active < itemLength-1){
            		active++;
            		changeActiveLi();
            	}else if (o.loop){
            		active=0;
            		changeActiveLi();
            	}
                return false;
            });

        li.click(function(){
        	active= $(this).index();
        	changeActiveLi();
        });
        
        function changeMainImage(url){
        	$(o.mainImg).attr('src',url);
        }
        
        function changeActiveLi(){
        	ul.find("li.active").removeClass("active");
        	li.eq(active).addClass("active");   
        	if (o.mainImg)
    			changeMainImage($("a", li.eq(active)).attr('href'));
        	adjust();
        }

        function adjust(){
        	//check left position
        	liLeft= Math.abs(li.eq(active).position().left);
    		ulLeft= Math.abs(ul.position().left);
    		if (liLeft<ulLeft){
    			if (o.animate)
    				ul.stop(true).animate({"left":-liLeft},o.speed);
    			else
    				ul.css("left",-liLeft);
    		}else{
    			//check right position
            	a= liLeft + li.eq(active).outerWidth( true );
            	b= div.width()+ulLeft;
            	if (a>b){
            		ulLeft+=a-b;
            		if (o.animate)
        				ul.stop(true).animate({"left":-ulLeft},o.speed);
        			else
        				ul.css("left",-ulLeft);
            	}
    		}
        }
    });
};

function css(el, prop) {
    return parseInt($.css(el[0], prop)) || 0;
};
function width(el) {
    return  el[0].offsetWidth + css(el, 'marginLeft') + css(el, 'marginRight');
};
function height(el) {
    return el[0].offsetHeight + css(el, 'marginTop') + css(el, 'marginBottom');
};

})(jQuery);