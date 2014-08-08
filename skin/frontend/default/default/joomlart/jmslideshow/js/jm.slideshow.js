/*! Copyright (c) 2011 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.0.6
 * 
 * Requires: 1.2.2+
 */

(function($) {

var types = ['DOMMouseScroll', 'mousewheel'];

if ($.event.fixHooks) {
    for ( var i=types.length; i; ) {
        $.event.fixHooks[ types[--i] ] = $.event.mouseHooks;
    }
}

$.event.special.mousewheel = {
    setup: function() {
        if ( this.addEventListener ) {
            for ( var i=types.length; i; ) {
                this.addEventListener( types[--i], handler, false );
            }
        } else {
            this.onmousewheel = handler;
        }
    },
    
    teardown: function() {
        if ( this.removeEventListener ) {
            for ( var i=types.length; i; ) {
                this.removeEventListener( types[--i], handler, false );
            }
        } else {
            this.onmousewheel = null;
        }
    }
};

$.fn.extend({
    mousewheel: function(fn) {
        return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
    },
    
    unmousewheel: function(fn) {
        return this.unbind("mousewheel", fn);
    }
});


function handler(event) {
    var orgEvent = event || window.event, args = [].slice.call( arguments, 1 ), delta = 0, returnValue = true, deltaX = 0, deltaY = 0;
    event = $.event.fix(orgEvent);
    event.type = "mousewheel";
    
    // Old school scrollwheel delta
    if ( orgEvent.wheelDelta ) { delta = orgEvent.wheelDelta/120; }
    if ( orgEvent.detail     ) { delta = -orgEvent.detail/3; }
    
    // New school multidimensional scroll (touchpads) deltas
    deltaY = delta;
    
    // Gecko
    if ( orgEvent.axis !== undefined && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
        deltaY = 0;
        deltaX = -1*delta;
    }
    
    // Webkit
    if ( orgEvent.wheelDeltaY !== undefined ) { deltaY = orgEvent.wheelDeltaY/120; }
    if ( orgEvent.wheelDeltaX !== undefined ) { deltaX = -1*orgEvent.wheelDeltaX/120; }
    
    // Add event and delta to the front of the arguments
    args.unshift(event, delta, deltaX, deltaY);
    
    return ($.event.dispatch || $.event.handle).apply(this, args);
}

})(jQuery);

/**
 * ------------------------------------------------------------------------
 * JM Siotis Theme
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
 
(function($){
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
    var IEVersion = getIEVersion();
	
	var defaults = {
		effects: [
			'slice-down-right',			//animate height and opacity
			'slice-down-left',
			'slice-up-right',
			'slice-up-left',
			
			
			'slice-down-right-offset', 			//have an offset for top or bottom, no animate height
			'slice-down-left-offset',
			'slice-up-right-offset',
			'slice-up-left-offset',
			
			'slice-updown-right',				//slide up alternate column
			'slice-updown-left',
			
			'slice-down-center-offset',
			'slice-up-center-offset',
			
			'slice-down-right-inv',				//look like above, slide from an offset, but use the current image instead of the new image
			'slice-down-left-inv',
			'slice-down-center-inv',
			'slice-up-right-inv',
			'slice-up-left-inv',
			'slice-up-center-inv',
			
			'slice-down-random',			//slide and offset fade
			'slice-up-random',
			
			'slice-down-left-wider', 		//slice, wider === fold
			'slice-down-right-wider',
			
			'slide-in-left',
			'slide-in-right',
			'slide-in-up',
			'slide-in-down',
			'slide-in-left-inv',
			'slide-in-right-inv',
			'slide-in-up-inv',
			'slide-in-down-inv',
			
			'fade',			
			
			'box-sort-random', //box, offset from random other position, and animate fade to it position, fadein
			'box-random',
			'box-rain-normal',
			'box-rain-reverse',
			'box-rain-normal-grow',
			'box-rain-reverse-grow',
			'box-rain-normal-jelly',
			'box-rain-reverse-jelly',
			
			'circle-out',
			'circle-in'//,
			//'circle-rotate'
		],
		
		animation: 'fade', 							//[fade, vrtslide, hrzslide, fullslide, slice], slide and fade for old compactible, fullslide is horizontal only
		direction: 'horizontal', 					//depend on [animation=slide] - [horizontal, vertical] - slide direction of main item for move animation
		
		mainWidth: 600,								//width of main item
		mainHeight: 320,							//height of main item
		
		//private:
		fullsize: false,							//depend on [animation=slide] - full size mean we will set the slideshow block to 100%
		fbanim: 'fade',								//fallback animation 										- const
		slices: 13,									//depend on [animation=slice] - number of vertical slices 	- const
		boxCols: 8,									//depend on [animation=slice] - number of box columns		- const
		boxRows: 5,									//depend on [animation=slice] - number of box columns		- const
		
		duration: 500,								//duration - time for animation
		
		//private:
		repeat: true,								//animation repeat or not
		autoPlay: false,							//auto play
		interval: 5000,								//interval - time for between animation	
		
		rtl: null,									//rtl - for future
		
		//private:
		startItem: 0,								//start item will be show - const
		
		thumbItems: 4,								//number of thumb item will be show
		thumbType: 'thumb', 						//false - no thumb, other [number, thumb], thumb will animate
		thumbWidth: 60,								//width of thumbnail item
		thumbHeight: 60,							//width of thumbnail item
		thumbSpaces: [3, 3, 3, 3],					//space between thumbnails
		thumbOpacity: 0.8,							//thumb opacity - set to the mask
		thumbTrigger: 'click',						//thumb trigger event, [click, mouseenter]
		thumbDirection: 'horizontal',				//thumb orientation
		thumbPosition: 'br',						//default is bottom right
		
		showDesc: true,								//show description or not
		descTrigger: 'always',						//[always, mouseover]
		maskWidth: 360,								//mask - a div over the the main item - used to hold descriptions
		maskHeight: 300,							//mask height
		maskOpacity: 0.8,							//mask opacity
		maskPosition: 'bl',							//default is bottom left
		maskAnim: 'hrzslidefade',					//mask transition style [none, fade, vrtslide, hrzslide, vrtslidefade, hrzslidefade]
		
		//private:
		maskAlign: 'left',
			
		controlBox: true,							//show navigation controller [next, prev, play, playback] - JM does not have a config
		controlPosition: 'tl',						//show navigation controller [next, prev, play, playback] - JM does not have a config
		
		navButtons: false,							//[next, prev] buttons of other position, control by css, even overwrite html structure, everywhere but not in main block
		
		showProgress: true,							//show the progress bar
		
		urls: false, 								// [] array of url of main items
		targets: false 								// [] same as urls, an array of target value such as, '_blank', 'parent', '' - default
	};
	
	var jaslider = function(elm, options){
		this.element = $(elm);
		this.options = $.extend({}, defaults, options);
		this.initialize();
	};
	
	jaslider.prototype = {
	
		initialize: function () {
			var slider = this.element,
				options = this.options,
				mainWrap = slider.find('.jm-slide-main-wrap'),
				mainFrame = slider.find('.jm-slide-main'),
				mainItems = slider.find('.jm-slide-item');
				
			if(!mainItems.length){
				return false;
			}
			
			var imgItems = mainItems.find('img');
			if(mainItems.length != imgItems.length && options.animation == 'slice'){
				options.animation = options.fbanim;
			}
			
			if(options.animation == 'vrtslide'){
				options.animation = 'slide';
				options.direction = 'vertical';
			} else if (options.animation == 'hrzslide'){
				options.animation = 'slide';
				options.direction = 'horizontal';
			} else if (options.animation == 'fullslide'){
				options.animation = 'slide';
				options.direction = 'horizontal';
				options.fullsize = true;
			}
			
			if (options.animation !== 'slide'){
				options.fullsize = false;
			}
			
			if(!options.thumbSpaces || options.thumbSpaces.length < 4){
				options.thumbSpaces = [0, 0, 0, 0];
			}

			mainWrap.css({
				'width': options.fullsize ? '100%' : options.mainWidth,
				'height': options.mainHeight
			});
			
			mainItems.css({
				'width': options.mainWidth,
				'height': options.mainHeight
			});
			
			var mainItemSpace = 0,
			isHorz = (options.direction == 'horizontal');
			
			if (options.animation == 'slide') {	//full size
				mainItemSpace = 10;
				mainItems.css(isHorz ? 'margin-right' : 'margin-bottom',  mainItemSpace);
			}
			
			var mainItem = mainItems.eq(0),
				mainItemSize = isHorz ? mainItem.outerWidth(true) : mainItem.outerHeight(true),
				rearSize = Math.ceil(((isHorz ? mainWrap.innerWidth() : mainWrap.innerHeight()) - mainItemSize) / 2),
				
				vars = {
					slider: slider,
					mainWrap: mainWrap,
					mainFrame: mainFrame,
					mainItems: mainItems,
					
					size: mainItemSize,
					rearSize: rearSize,
					offset: options.fullsize ? (rearSize - mainItemSize + mainItemSpace / 2) : 0,
					mainItemSpace: mainItemSpace,
					
					total: mainItems.length,
					curIdx: Math.min(options.startItem, mainItems.length - 1),
					nextIdx: -1,
					curImg: '',
					
					running: 0,
					stop: 0,
					timer: 0,
					
					sliceTime: Math.round(Math.max(70, options.duration / options.slices)),
					boxTime: Math.round(Math.max(50, options.duration / Math.max(options.boxCols, options.boxRows))),
					
					modes: (isHorz ? (options.rtl == 'rtl' ? ['right', 'width'] : ['left', 'width']) : ['top', 'height']),
					
					finished: $.proxy(this.animFinished, this)
				};
			
			this.vars = vars;
			
			//Description
			this.initMasker();
			
			//Get initial images
			if(options.animation == 'slice'){
				mainItems.css('display', 'none');
				vars.mainItems = imgItems;
				vars.curImg = imgItems[vars.curIdx];
				
				var ofsParent = mainFrame.offsetParent() || mainWrap;
				
				//Set first background
				mainFrame.css({
					position: 'relative',
					left: (ofsParent.width() - options.mainWidth) / 2,
					top: (ofsParent.height() - options.mainHeight) / 2,
					overflow: 'hidden',
					display: 'block',
					width: options.mainWidth,
					height: options.mainHeight,
					background: 'url("'+ vars.curImg.src +'") no-repeat'
				});
			} 

			if(options.animation == 'slide'){
				vars.offset -= parseInt(mainFrame.css(isHorz ? 'margin-left' : 'margin-top'));
				
				mainFrame
					.css(vars.modes[1], vars.size * (vars.total + 2))
					.css(vars.modes[0], -vars.curIdx * vars.size + vars.offset);
			} 
			if(options.fullsize){
				mainItems.eq(0).clone().appendTo(mainFrame);
				mainItems.eq(vars.total - 1).clone().prependTo(mainFrame, 'top');
			}
			if(options.animation == 'fade'){
				mainItems.css({
					position: 'absolute',
					top: 0,
					opacity: 0,
					visibility: 'visible'
				}).eq(vars.curIdx).css('opacity', 1);
			}
		
			this.initMainItemAction();
			this.initMainCtrlButton();
			this.initThumbAction();
			this.initControlAction();
			this.initHoverBehavior();
			this.initProgressBar();
			this.initLoader();
			
			vars.direct = 'next';
			slider.css('visibility', 'visible');
			
			this.prepare(false, vars.curIdx);
			this.animFinished();
		},
		
		stop: function(){
			clearInterval(this.vars.timer);
			this.vars.stop = 1;
			
			if(this.options.showProgress){			//stop the progress bar
				this.vars.progress.stop().css('width', 0);
			}
			
			return false;
		},
	
		prev: function(force){
			var vars = this.vars;
			if(vars.running && !force){
				return false;
			}
			this.prepare(force, vars.curIdx -1);
			
			return false;
		},
		
		next: function(force){
			var vars = this.vars;
			if(vars.running && !force){
				return false;
			}
			this.prepare(force, vars.curIdx +1);
			
			return false;
		},
		
		playback: function(force){
			this.vars.direct = 'prev';
			this.vars.stop = 0;
			this.prev(force);
			
			return false;
		},
		
		play: function(force){
			this.vars.direct = 'next';
			this.vars.stop = 0;
			this.next(force);
			
			return false;
		},
		
		start: function(){
			var vars = this.vars;
			
			clearTimeout(vars.timer);
			vars.timer = setTimeout($.proxy(this[this.vars.direct], this), this.options.interval)
		},
		
		loadimg: function(cimg, idx){
			var img = new Image();
			img.onload = $.proxy(this.load, this, cimg, idx);
			img.src = cimg.attr('src');
		},
		
		load: function(img, idx){
			img.data('loaded', 1);
			
			var vars = this.vars;
		
			if(vars.nextIdx == idx){
				if(vars.loader){
					vars.loader.stop().fadeTo(500, 0);
				}
				
				this.run(false, idx);
			} else if(vars.nextIdx == -1 && vars.loader){
				vars.loader.stop().fadeTo(500, 0);
			}
		},
		
		prepare: function(force, idx){
			var vars = this.vars,
				options = this.options;
				
			if(options.animation === 'slice' && vars.running){
				return false;
			}
			
			if(idx >= vars.total){ 
				idx = 0;
			}
			
			if(idx < 0){
				idx = vars.total - 1;
			}
			
			var	curImg = vars.mainItems.eq(idx);
			if(curImg[0].tagName.toLowerCase() != 'img'){
				curImg = curImg.find('img');
			}
			
			if(!curImg.length){
				return this.run(force, idx);
			}
			
			vars.nextIdx = idx;
			
			clearTimeout(vars.timer);
			
			if(curImg.data('loaded')){
				if(idx == vars.curIdx){
					return false;
				}
			
				this.run(force, idx);
			}
			
			else{
				
				if(vars.loader){
					vars.loader.css('display', 'block').stop().animate({opacity: 0.3});
				}
				
				this.loadimg(curImg, idx);
			}
			
			return false;
		},
		
		run: function(force, idx){
			var vars = this.vars,
				options = this.options;
				
			if(vars.curIdx == idx){
				return false;
			}			
				
			if(this[options.animation]){
				this[options.animation](force, idx);
			}else{
				this.fade(force, idx);
			}
			
			if (vars.thumbMask) {
				if (idx <= vars.thumbStartIdx || idx >= vars.thumbStartIdx + options.thumbItems - 1) {
					vars.thumbStartIdx = Math.max(0, Math.min(idx - options.thumbItems + 2, vars.total - options.thumbItems));
					
					var pos = {};
					pos[vars.thumbAnimStyle] = -vars.thumbStartIdx * vars.thumbStep;
					vars.thumbBox.stop().animate(pos);
					vars.handleBox.stop().animate(pos);
				}
				
				var mpos = {};
				mpos[vars.thumbAnimStyle] = (idx - vars.thumbStartIdx) * vars.thumbStep - 2000 + vars.stepOffs;
				vars.thumbMask.animate(mpos);
				vars.thumbItems.removeClass('active').eq(idx).addClass('active');
				vars.handleItems.removeClass('active').eq(idx).addClass('active');
			}
			
			if (options.descTrigger === 'always' && options.showDesc) {
				this.hideDescription();
			}
			
			if(options.showProgress){
				vars.progress.stop().css('width', 0);
			}
		},
		
		slide: function(force, idx){
			var vars = this.vars,
				aobj = {};
				
			vars.curIdx = idx;
			aobj[vars.modes[0]] = -idx * vars.size + vars.offset;
			vars.mainFrame.animate(aobj, vars.finished);
		},
		
		fade: function(force, idx){
			var options = this.options,
				vars = this.vars;
				
			if(idx != vars.curIdx){
				vars.mainItems.eq(vars.curIdx).stop().fadeTo(options.duration, 0);
				vars.mainItems.eq(idx).stop().fadeTo(options.duration + 200, 1, vars.finished);
			}
			
			vars.curIdx = idx;
		},
		
		slice: function(force, idx){
		
			var options = this.options,
				vars = this.vars,
				container = vars.mainFrame,
				oldImg = vars.curImg;
			
			//Set vars.curImg
			vars.curIdx = idx;
			vars.curImg = vars.mainItems[vars.curIdx];
			
			// Remove any slices & boxs from last transition
			container.children('.jm-slice').remove();
			container.children('.jm-box').remove();
			
			//Generate random effect
			var	effect = options.effects[Math.floor(Math.random() * (options.effects.length))];
			if(effect == undefined){
				effect = 'fading';
			}
			
			//Run effects
			var effects = effect.split('-'),
				callfun = 'anim' + effects[0];
			
			if(this[callfun]){
			
				vars.running = true;
				this[callfun](effects, oldImg, vars.curImg);
			}
		},
		
		animFinished: function(){ 
			var options = this.options,
				vars = this.vars;
				
			vars.running = false;
			
			//Trigger the afterChange callback
			if (options.showDesc) {
				this.swapDescription();
				
				if (options.descTrigger === 'always') {
					this.showDescription();
				}
			}
			
			if (!vars.stop && (options.autoPlay && (vars.curIdx < vars.total -1 || options.repeat))) {
				this.start();
				
				if(options.showProgress){
					vars.progress.stop().animate({ width: vars.progressWidth },  options.interval - options.duration);
				}
			}
		},
		
		createSlice: function(img){
			var options = this.options,
				vars = this.vars,
				container = vars.mainFrame;
				
			return $('<div class="jm-slice"></div>').css({
				display: 'block',
				position: 'absolute',
				left: 0,
				width: options.mainWidth,
				height: options.mainHeight, 
				opacity: 0,
				background: 'url("'+ img.src +'") no-repeat 0% 0%',
				zIndex: 10
			}).appendTo(container);
		},

		createSlices: function(img, height, opacity){
			var options = this.options,
				vars = this.vars,
				container = vars.mainFrame,
				width = Math.round(options.mainWidth / options.slices),
				slices = [];
				
			for(var i = 0; i < options.slices; i++){
				var sliceWidth = i == options.slices - 1 ? (options.mainWidth - width * i) : width;
					
				slices.push($('<div class="jm-slice"></div>').css({
					position: 'absolute',
					left: i * width,
					width: sliceWidth,
					height: height, 
					opacity: opacity,
					background: 'url("'+ img.src +'") no-repeat -'+ (i * width) +'px 0%',
					zIndex: 10
				})[0]);
			}
			
			container.append(slices);
			
			return slices;
		},
		
		createBoxes: function(img, opacity){
			var options = this.options,
				vars = this.vars,
				container = vars.mainFrame,
				width = Math.round(options.mainWidth / options.boxCols),
				height = Math.round(options.mainHeight / options.boxRows),
				bwidth,
				bheight,
				boxes = [];
				
			for(var rows = 0; rows < options.boxRows; rows++){
				bheight = rows == options.boxRows - 1 ? options.mainHeight - height * rows : height;
				
				for(var cols = 0; cols < options.boxCols; cols++){
					bwidth = cols == options.boxCols - 1 ? options.mainWidth - width * cols : width;
					
					boxes.push($('<div class="jm-box"></div>').css({
						position: 'absolute',
						opacity: opacity,
						left: width * cols, 
						top: height * rows,
						width: bwidth,
						height: bheight,
						background: 'url("'+ img.src +'") no-repeat -'+ (width * cols) +'px -'+ (height * rows) +'px',
						zIndex: 10
					})[0]);
				}
			}
			
			container.append(boxes);
			
			return boxes;
		},
		
		createCircles: function(img, opacity){
			var options = this.options,
				vars = this.vars,
				container = vars.mainFrame,
				size = 100,
				radius = Math.ceil(Math.sqrt(Math.pow((options.mainWidth), 2) + Math.pow((options.mainHeight), 2))),
				total = Math.ceil(radius / 100),
				left, top, elm,
				circles = [];
				
			for(var i = 0; i < total; i++){
				left = Math.round((options.mainWidth - size) / 2);
				top = Math.round((options.mainHeight - size) / 2);
			
				elm = $('<div class="jm-box"></div>').css({
					position: 'absolute',
					opacity: opacity,
					left: left, 
					top: top,
					width: size,
					height: size,
					background: 'url("'+ img.src +'") no-repeat '+ (0 - left) +'px '+ (0 - top) +'px',
					zIndex: 10
				}).css3({
					'border-radius': radius + 'px'
				})[0];
				
				circles.push(elm);
				
				size += 100;
			}
			
			container.append(circles);
			
			return circles;
		},
		
		animslice: function(effects, oldImg, curImg){
			var options = this.options,
				vars = this.vars,			
				img = curImg,
				height = 0,
				opacity = 0;
				
			if(effects[3] == 'inv'){
				img = oldImg;
				height = options.mainHeight;
				opacity = 1;
			}
			
			//set the background
			vars.mainFrame.css('background','url("'+ (effects[3] == 'inv' ? curImg.src : oldImg.src) +'") no-repeat');
			
			var slices = this.createSlices(img, height, opacity),
				styleOn = { height: options.mainHeight - height, opacity: 1 - opacity / 2},
				last = slices.length -1,
				timeBuff = 100;
			
			// by default, animate is sequence from left to right
			if(effects[2] == 'left'){		// reverse the direction, so animation is sequence from right to left
				slices = slices.reverse();
			} else if(effects[2] == 'random'){	// so randomly
				this.shuffle(slices);
			}
			
			if(effects[3] == 'offset'){										//have offset style - we will not animate height, so set it to fullheight, we animate 'top' or 'bottom' property
				var property = effects[1] == 'up' ? 'top' : 'bottom';
				
				delete styleOn.height;
				styleOn[property] = 0;
				
				$(slices).css(property, '250px').css('height', options.mainHeight);
			}
			
			else if(effects[1] == 'updown'){
				for(var k = 0, kl = slices.length; k < kl; k++){
					$(slices[k]).css((k & 1) == 0 ? 'top' : 'bottom', '0px');
				}
			}
			
			else if(effects[1] == 'down'){
				$(slices).css('top', '0px');
			}

			else if(effects[1] == 'up'){
				$(slices).css('bottom', '0px');
			}
			
			if(effects[3] == 'wider'){
				
				$.each(slices, function(i, slice){
					var orgWidth = $(slice).innerWidth();
						
					$(slice).css({
						'width': 0,
						'height': options.mainHeight
					});
					
					setTimeout(function(){
						$(slice).animate({
							width: orgWidth,
							opacity: 1
						}, options.duration, i == last ? vars.finished : null);
					}, timeBuff);
					
					timeBuff += vars.sliceTime;
				});
			}
			else if(effects[2] == 'center'){
				var center = last / 2;
				$.each(slices, function(i, slice){
					var delay = Math.abs(center - i) * 100;
					
					setTimeout(function(){
						$(slice).animate(styleOn, options.duration, i == last ? vars.finished : null);
					}, delay);
					
				});
			} else {
				$.each(slices, function(i, slice){
					
					setTimeout(function(){
						$(slice).animate(styleOn, options.duration, i == last ? vars.finished : null);
					}, timeBuff);
					
					timeBuff += vars.sliceTime;
				});
			}
		},
		
		animbox: function(effects, oldImg, curImg){
			var options = this.options,
				vars = this.vars,
				img = vars.curImg,
				height = 0,
				opacity = 0;
				
			if(effects[3] == 'jelly'){
				img = oldImg;
				opacity = 1;
			}
			
			vars.mainFrame.css('background','url("'+ (effects[3] == 'jelly' ? curImg.src : oldImg.src) +'") no-repeat');
			
			var boxes = this.createBoxes(img, opacity),
				last = options.boxCols * options.boxRows -1,
				boxTime = vars.boxTime,
				i = 0,
				timeBuff = 100;
			
			if(effects[1] == 'sort'){
				var width = Math.round(options.mainWidth / options.boxCols),
					height = Math.round(options.mainHeight / options.boxRows),
					boxTime = boxTime / 3;

				$.each(this.shuffle(boxes), function(idx, box){
					var jbox = $(box),
						styleOn = {
							top: jbox.css('top'),
							left: jbox.css('left')
						};
					
					jbox.css({
						top: Math.round(Math.random() * options.boxRows / 2) * height,
						left: Math.round(Math.random() * options.boxCols / 2) * width
					});
					
					styleOn['opacity'] = 1;
				
					setTimeout(function(){
						jbox.animate(styleOn, options.duration, idx == last ? vars.finished : null);
					}, timeBuff);
				
					timeBuff += boxTime;
				});
			}
			
			else if(effects[1] == 'random'){
				boxTime = boxTime / 3;
				
				$.each(this.shuffle(boxes), function(idx, box){
					setTimeout(function(){
						$(box).animate({ opacity: 1 }, options.duration, idx == last ? vars.finished : null);
					}, timeBuff);
				
					timeBuff += boxTime;
				});
			}
			else if(effects[1] == 'rain'){
				var rowIndex = 0,
					colIndex = 0,
					arr2d = [];
				
				// Split boxes into 2D array
				arr2d[rowIndex] = [];
				
				if(effects[2] == 'reverse'){
					boxes = boxes.reverse();
				}
				
				$.each(boxes, function(idx, box){
					arr2d[rowIndex][colIndex] = box;
					colIndex++;
					if(colIndex == options.boxCols){
						rowIndex++;
						colIndex = 0;
						arr2d[rowIndex] = [];
					}
				});
				
				// Run animation
				for(var cols = 0; cols < (options.boxCols * 2); cols++){
					var prevCol = cols;
					for(var rows = 0; rows < options.boxRows; rows++){
						if(prevCol >= 0 && prevCol < options.boxCols){
							
							(function(row, col, time, i) {
								var jbox = $(arr2d[row][col]),
									w = jbox.innerWidth(),
									h = jbox.innerHeight();
								
								if(effects[3] == 'grow'){
									jbox.css({
										width: 0,
										height: 0
									});
								}
								
								else if(effects[3] == 'jelly'){
									w = 0;
									h = 0;
								}
								
								setTimeout(function(){
									jbox.animate({ opacity: 1 - opacity, width: w, height: h }, options.duration, i == last ? vars.finished : null);
								}, time);
							
							})(rows, prevCol, timeBuff, i);
							
							i++;
						}
						prevCol--;
					}
					timeBuff += boxTime;
				}
			}
		},
		
		animslide: function(effects, oldImg, curImg){
			
			var options = this.options,
				vars = this.vars,
				img = curImg;
			
			if(effects[3] == 'inv'){
				img = oldImg;
			}
			
			vars.mainFrame.css('background','url("'+ (effects[3] == 'inv' ? curImg.src : oldImg.src) +'") no-repeat');
			
			var slice = this.createSlice(img),
				mapOn = { left: 'left', right: 'right', up: 'top', down: 'bottom' },
				mapOff = { left: 'right', right: 'left', up: 'bottom', down: 'top' },
				value = (effects[2]  == 'left' || effects[2] == 'right') ? options.mainWidth : options.mainHeight,
				styleOn = { opacity: 1},
				styleOff = { opacity: 0.5 };
				
			styleOff[mapOn[effects[2]]] = -value;
			styleOff[mapOff[effects[2]]] = '';
			
			styleOn[mapOn[effects[2]]] = 0;
			
			if(effects[3] == 'inv'){
				styleOn.opacity = 0.5;
				styleOn[mapOn[effects[2]]] = - value;
				
				styleOff.opacity = 1;
				styleOff[mapOn[effects[2]]] = 0;
				styleOff[mapOff[effects[2]]] = '';
			}
			
			slice.css(styleOff);
				
			$(slice).animate(styleOn, options.duration, vars.finished);
		},
		
		animcircle: function(effects, oldImg, curImg){
			
			var options = this.options,
				vars = this.vars,
				img = curImg,
				opacity = 0;
			
			if(effects[1] == 'in'){
				img = oldImg;
				opacity = 1;
			}
			
			vars.mainFrame.css('background','url("'+ (effects[1] == 'in' ? curImg.src : oldImg.src) +'") no-repeat');
			
			var circles = this.createCircles(img, opacity),
				timeBuff = 100,
				last = circles.length -1;
				
			if(effects[1] == 'in'){
				circles = circles.reverse();
			}
			
			$.each(circles, function(i, circle){
				
				setTimeout(function(){
					$(circle).animate({opacity: 1 - opacity}, options.duration, i == last ? vars.finished : null);
				}, timeBuff);
				
				timeBuff += vars.boxTime;
			});
		},
		
		animfade: function(effects, oldImg, curImg){
			
			var vars = this.vars,
				options = this.options,
				slice = this.createSlice(curImg),
				styleOn = {
					opacity: 1
				};
				
			vars.mainFrame.css('background','url("'+ oldImg.src +'") no-repeat');
			
			slice.animate(styleOn, options.duration, vars.finished);
		},
		
		shuffle: function(arr){
			for(var j, x, i = arr.length; i; j = parseInt(Math.random() * i), x = arr[--i], arr[i] = arr[j], arr[j] = x);
			return arr;
		},
		
		docking: function(cont, elm, dock){
			var vars = this.vars,
				options = this.options;
				
			switch(dock){
				case 'top':
				case 'bottom':
					elm.css(dock, 0).css('left', Math.max(0, (cont.width() - elm.outerWidth(true)) / 2));
					break;
					
				case 'left':
				case 'right':
					elm.css(dock, 0).css('top', Math.max(0, (cont.height() - elm.outerHeight(true)) / 2));					
					break;
					
				case 'tl':
					elm.css({top: 0, left: 0});
					break;
					
				case 'tr':
					elm.css({top: 0, right: 0});
					break;
					
				case 'bl':
					elm.css({bottom: 0, left: 0});
					break;
					
				case 'br':
					elm.css({bottom: 0, right: 0});
					break;
			}
		},
		
		showDescription: function(){
			this.vars.maskDesc.stop().animate(this.vars.maskOn, this.vars.iecallback);
		},
		
		hideDescription: function(){
			this.vars.maskDesc.stop().animate(this.vars.maskOff);
		},
		
		swapDescription: function(){
			var vars = this.vars;
				
			vars.maskDesc.find('.jm-slide-desc').detach();
			vars.maskDesc.append(vars.descs[vars.curIdx]);
		},
		
		initMasker: function(){
			var options = this.options,
				vars = this.vars,
				slider = vars.slider,
				maskDesc = slider.find('.jm-mask-desc');
				
			if(!maskDesc.length){
				return;
			}
			
			if (options.showDesc) {
				var mask = slider.find('.jm-slide-mask');
				
				maskDesc.add(mask).css({
					'display': 'block',
					'position': 'absolute',
					'width': options.maskWidth,
					'height': options.maskHeight
				});
				
				mask.css('opacity', options.maskOpacity);
				
				this.docking(vars.slider, maskDesc, options.maskPosition);
				
				if($.inArray(options.maskPosition, ['top', 'right', 'bottom', 'left']) != -1){
					options.maskAlign = options.maskPosition;
				} else {
					if(options.maskAnim.indexOf('vrt') != -1){
						options.maskAlign = options.maskPosition.indexOf('t') != -1 ? 'top' : 'bottom';
					} else if (options.maskAnim.indexOf('hrz') != -1){
						options.maskAlign = options.maskPosition.indexOf('l') != -1 ? 'left' : 'right';
					}
				}
				
				maskDesc.addClass('jm-mask-pos-' + options.maskPosition);
				
				var descs = slider.find('.jm-slide-descs .jm-slide-desc'),
					maskOn = {},
					maskOff = {};
					
				if(options.maskAnim.indexOf('slide') != -1){
					maskOn[options.maskAlign] = 0;
					maskOff[options.maskAlign] = (options.maskAlign == 'top' || options.maskAlign == 'bottom' ? -options.maskHeight : -options.maskWidth);
				}
				
				if(options.maskAnim.indexOf('fade') != -1){
					maskOn['opacity'] = 1;
					maskOff['opacity'] = 0.01;
				}
				
				if (options.descTrigger === 'mouseenter') {
					maskDesc.add(vars.mainWrap)
						.bind('mouseenter', $.proxy(this.showDescription, this))
						.bind('mouseleave', $.proxy(this.hideDescription, this));
						
				}
				
				$.extend(vars, {
					maskOn: maskOn,
					maskOff: maskOff,
					maskDesc: maskDesc,
					descs: descs,
					iecallback: ((IEVersion>0 && IEVersion <= 8 && options.maskAnim.indexOf('fade') != -1) ? function(){ vars.maskDesc.css('filter', ''); } : undefined)
				});
				
			} else {
				maskDesc.css('display', 'none');
			}
		},
		
		initThumbAction: function () {
			var options = this.options,
				vars = this.vars;
			
			var thumbWrap = vars.slider.find('.jm-slide-thumbs-wrap');
			if(!thumbWrap.length){
				return false;
			}
			
			if (options.thumbType) {
				var thumbMask = thumbWrap.find('.jm-slide-thumbs-mask'),
					thumbBox = thumbWrap.find('.jm-slide-thumbs'),
					thumbItems = thumbBox.find('.jm-slide-thumb'),
					handleBox = thumbWrap.find('.jm-slide-thumbs-handles'),
					handleItems = handleBox.children(),
					spaces = options.thumbSpaces,
					stepOffs = isHorz ? spaces[3] : spaces[0],
					isHorz = (options.thumbDirection == 'horizontal'),
					thumbAnimStyle = isHorz ? 'left' : 'top',
					thumbStep = isHorz ? options.thumbWidth + spaces[1] : options.thumbHeight + spaces[2],
					thumbStartIdx = Math.max(0, Math.min(vars.curIdx - options.thumbItems + 2, vars.total - options.thumbItems));
				
				thumbBox
					.add(handleBox)
					.add(thumbMask)
					.css(isHorz ? 'height' : 'width', isHorz ? options.thumbWidth + spaces[1] + spaces[3] : options.thumbWidth + spaces[1] + spaces[3]);
				
				var itemStyle = {
					'width': options.thumbWidth,
					'height': options.thumbHeight,
					'margin-right': spaces[1],
					'margin-bottom': spaces[2]
				};
				
				if(isHorz){
					itemStyle['margin-top'] = spaces[0];
					handleItems.eq(0).add(thumbItems.eq(0)).css('margin-left', spaces[3]);
				} else {
					itemStyle['margin-left'] = spaces[3];
					handleItems.eq(0).add(thumbItems.eq(0)).css('margin-top', spaces[0]);
				}
				
				$.each([handleItems, thumbItems], function(){
					$(this).css(itemStyle).removeClass('active').eq(vars.curIdx).addClass('active');
				});
				
				if(vars.slider.hasClass('jm-articles')){
					handleItems.css({
						'opacity':'0.001',
						'background':'#FFF'
					});
				}
				
				thumbMask
					.css(isHorz ? 'width' : 'height', 5000)
					.css(thumbAnimStyle, (vars.curIdx - thumbStartIdx) * thumbStep - 2000 + stepOffs);
				
				thumbBox.add(handleBox).css(thumbAnimStyle, -thumbStartIdx * thumbStep);
				
				thumbWrap
					.css(isHorz ? { 
						'width': thumbStep * options.thumbItems + spaces[3] * 2 - spaces[1],
						'height': options.thumbHeight + spaces[2] + spaces[0]
					} : { 
						'width': options.thumbWidth + spaces[3] + spaces[1],
						'height': thumbStep * options.thumbItems + spaces[0] * 2 - spaces[2]
					});
					
				this.docking(vars.mainWrap, thumbWrap, options.thumbPosition);
				
				thumbWrap.addClass('jm-thumbs-' + options.thumbPosition);
				
				thumbWrap.find('.jm-slide-thumbs-mask-left, .jm-slide-thumbs-mask-right').css({
					'width': isHorz ? 2000 : options.thumbWidth + spaces[1] + spaces[3],
					'height': isHorz ? options.thumbHeight + spaces[0] + spaces[2] : 2000,
					'opacity': options.thumbOpacity
				});
				
				thumbWrap.find('.jm-slide-thumbs-mask-center').css({
					'width': options.thumbWidth,
					'height': options.thumbHeight,
					'opacity': options.thumbOpacity
				});
				
				for (var i = 0; i < handleItems.length; i++) {
					handleItems.eq(i).bind(options.thumbTrigger, $.proxy(this.prepare, this, true, i));
				}
					
				handleBox.bind('mousewheel', $.proxy(function (e, delta) {
					
					if (delta < 0) {
						this.next(true);
					} else {
						this.prev(true);
					}
					
					return false;
				}, this));
				
				$.extend(vars, {
					thumbStartIdx: thumbStartIdx,
					thumbAnimStyle: thumbAnimStyle,
					thumbStep: thumbStep,
					stepOffs: stepOffs,
					thumbMask: thumbMask,
					thumbBox: thumbBox,
					handleBox: handleBox,
					thumbItems: thumbItems,
					handleItems: handleItems
				});
				
			} else {
				thumbWrap.css('display', 'none');
			}
		},

		initControlAction: function () {
			var options = this.options,
				vars = this.vars,
				slider = this.vars.slider,
				controls = ['prev', 'play', 'stop', 'playback', 'next'],
				btnarr;
				
			for (var j = 0, jl = controls.length; j < jl; j++) {
				if(this[controls[j]]){
					btnarr = slider.find('.jm-slide-' + controls[j]);
					
					for (var i = 0, il = btnarr.length; i < il; i++) {
						btnarr.eq(i).bind('click', $.proxy(this[controls[j]], this, true));
					}
				}
			}
			
			var jcontrols = $('.jm-slide-controls');
			if(options.controlBox){
				this.docking(vars.mainWrap, jcontrols, options.controlPosition);
			} else {
				jcontrols.css('display', 'none');
			}
		},
		
		initMainCtrlButton: function(){
			var options = this.options,
				vars = this.vars,
				navBtns = vars.mainWrap.find('.jm-slide-prev, .jm-slide-next');
				
			if(options.navButtons){
				
				if(options.fullsize){
					navBtns.css({
						width: (options.direction == 'horizontal' ? Math.max(vars.rearSize - vars.mainItemSpace / 2, 0) : options.mainWidth),
						height: (options.direction == 'horizontal' ? options.mainHeight : Math.max(0, vars.rearSize - vars.mainItemSpace / 2))
					});
				}
				
			} else {
				navBtns.css('display', 'none');
			}
		},
		
		initMainItemAction: function(){
			var options = this.options;

			if (options.urls) {
				var vars = this.vars,
					handle = function(e){
						var index = vars.mainItems.index(this);
							
						if(index == -1){
							index = vars.curIdx;
						}
						
						var url = options.urls[index],
							target = options.targets[index];
						
						if (url) {
							e.preventDefault();
							
							if (target == '_blank'){
								window.open(url, 'JAWindow');
							} else {
								window.location.href = url;
							}
						}
					};
				
				$(vars.mainFrame).add(vars.maskDesc).add(vars.mainItems).bind('click', handle);
			}
		},
		
		initHoverBehavior: function(){	
			var vars = this.vars,
				slider = vars.slider,
				controls = ['prev', 'play', 'stop', 'playback', 'next'],
				buttons = $();
				
			for (var j = 0, jl = controls.length; j < jl; j++) {
				buttons = buttons.add(slider.find('.jm-slide-' + controls[j]));
			}
			
			buttons = buttons.add(vars.handleItems);
			
			buttons.bind('mouseenter', function () {
				$(this).addClass('hover');
			}).bind('mouseleave', function () {
				$(this).removeClass('hover');
			});
		},
		
		initProgressBar: function(){
			var options = this.options,
				vars = this.vars,
				progress = vars.slider.find('.jm-slide-progress');
				
			if(!progress.length){
				options.showProgress = false;
				
				return false;
			}
			
			if(options.showProgress){
				$.extend(vars, {
					progress: progress,
					progressWidth: options.mainWidth
				});
			} else {
				progress.css('display', 'none');
			}
		},
		
		initLoader: function(){
			var vars = this.vars,
				loader = vars.slider.find('.jm-slide-loader');
				
			if(!loader){
				return false;
			}
			
			$.extend(vars, {
				loader: loader
			});
		}
	};
	
	$.fn.css3 = function(props) {
		var css = {},
			prefixes = ['moz', 'ms', 'o', 'webkit'];
		
		for(var prop in props) {
			// Add the vendor specific versions
			for(var i=0; i<prefixes.length; i++){
				css['-'+prefixes[i]+'-'+prop] = props[prop];
			}
			
			// Add the actual version	
			css[prop] = props[prop];
		}
		
		return this.css(css);
	};
	
	$.fn.jaslider = function(options){
		return this.each(function(){
			var cslider = new jaslider(this, options);
			$(document).ready(function() {
			   var slider = cslider.element,
			   vars = cslider.vars;
			   mainWrap = slider.find('.jm-slide-main-wrap'),
			   mainFrame = slider.find('.jm-slide-main'),
			   mainItems = slider.find('.jm-slide-item'); 
			   slidewraper = slider.parents("#ja-mass-top-sticky-wrapper");
			   maskdesc = slider.find(".jm-mask-desc");
			   mainImages = slider.find('.jm-slide-item img');
			   mainImages.css({
					'width':slider.width() 
				});
			    maskwidth = maskdesc.width();
				maskheight = maskdesc.height();
				masktop = maskdesc.css("margin-top");
				maskleft = maskdesc.css("left");
				$(window).resize(function() {
				 				   
				   ratio = Math.min(options.mainWidth, slider.width()) / options.mainWidth;
				  
				   
				   var newheight = options.mainHeight*ratio;
				   slidewraper.css({
					  'height':newheight		   
				   });
				   maskdesc.css({
					  'width':parseInt(maskwidth)*ratio,
					  'height':parseInt(maskheight)*ratio,
					  "margin-top":parseInt(masktop)*ratio,
				   });
				   mainWrap.css({
					'height': newheight,
					'width':slider.width()
				   });
				   mainItems.css({
					'height': newheight,
					'width':slider.width() 
				   });
				   mainImages.css({
					'height': newheight,
					'width':slider.width() 
				   });
				  
				   isHorz = (cslider.options.direction == 'horizontal');
				   idx = vars.curIdx;
			       var mainItem = mainItems.eq(0),
				       newitemsize = isHorz ? mainItem.outerWidth(true) : mainItem.outerHeight(true);
				   cslider.vars.size = newitemsize; 	   
				   if(parseInt(mainFrame.css(vars.modes[0])) < 0){
					  mainFrame.css(vars.modes[0],-idx * newitemsize + vars.offset)
				   }	   
				});

				$(window).trigger("resize");
           });	
		});
	};
	
})(jQuery);