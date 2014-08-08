/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Ajaxcartpro
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */
 

Prototype.Browser.IE6 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 6;
Prototype.Browser.IE7 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 7;
Prototype.Browser.IE8 = Prototype.Browser.IE && !Prototype.Browser.IE6 && !Prototype.Browser.IE7;

window.ACPTop = 200;
 
if(!Prototype.Browser.IE6){

    setLocation = function(url){
        if(window.location.href.match('https://') && !url.match('https://')){
            url = url.replace('http://','https://')
        }
         if(AW_ACP.isCartPage && ((url.search('/add') != -1 ) || (url.search('/remove') != -1 )) ){
            ajaxcartsend(url+'awacp/1/is_checkout/1', 'url', '', '');
        }else if (url.search('checkout/cart/add') != -1){
            ajaxcartsend(url+'awacp/1', 'url', '', '');
        }else if (url.search('wishlist/index/cart') != -1){
            ajaxcartsendwishlist(url+'awwishl/1/awacp/1', 'url', '', '');
        }else{
            window.location.href = url;
        }
    }
}


if(!Prototype.Browser.IE6){

	var cnt1 = 20;
	__intId = setInterval(
		/* Hangs event listener for @ADD TO CART@ links*/
		function(){
			cnt1--;
			if(typeof productAddToCartForm != 'undefined'){
				try {
					// This fix is applied to magento <1.3.1
                    $$('#product_addtocart_form '+aw_addToCartButtonClass).each(function(el){
                        el.setAttribute('type', 'button')
                    })
				}catch(err){
					
				}
				productAddToCartForm.submit = function(url){
					if(this.validator && this.validator.validate()){					
						ajaxcartsend('?awacp=1', 'form', this, '');
					}
					return false;
				}

                productAddToCartForm.form.onsubmit = function() {
                    productAddToCartForm.submit();
                    return false;
                };
                
				clearInterval(__intId);
			}
			if(!cnt1) clearInterval(__intId);
		},
		500
	);



	var cnt2 = 20;
	__intId2 = setInterval(
		/* This hangs event listener on @DELETE@ items from cart*/
		function(){	
			cnt2--;
			if(typeof aw_cartDivClass!= 'undefined' && $$(aw_cartDivClass).length || ((typeof AW_ACP !== 'undefined') && AW_ACP.isCartPage)){
                updateDeleteLinks();
				clearInterval(__intId2);
			}
			if(!cnt2) clearInterval(__intId);
		},
		500
	);
}





function setPLocation(url, setFocus){
    if (url.search('checkout/cart/add') != -1){ //CART ADD
        window.opener.focus();

        if (url[url.length-1] == '/') delim = '';
        else delim = '/';

        if (window.opener.location.pathname.search('checkout/cart') == -1)
            window.opener.ajaxcartsend(url+delim+'awacp/1', 'url', '');
        else
            window.opener.ajaxcartsend(url+delim+'awacp/1/is_checkout/1', 'url', '');
	}
	else{
		if(setFocus) {
			window.opener.focus();
		}
        window.opener.location.href = url;
	}
}

function ajaxcartsendwishlist(url, type, obj){
    url = getCommonUrl(url);
    showProgressAnimation();
    new Ajax.Request(url, {
          onSuccess: function(resp){
                try{
                    if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
				}catch(e){
					win.location.href=url;
					hideProgressAnimation();
					return;
				}
                hideProgressAnimation();
				if (resp.r != 'success'){
                    win.location.href=url;
                }
				else{
					if(AW_ACP.useConfirmation){	
						showConfirmDialog();	
					}
					__onACPRender();
					updateCartView(resp);
                    updateTopLinks(resp);
                    updateWishlist(resp);
                    updateWishlistTopLinks(resp)
				}
			}
        });
}

function ajaxcartsend(url, type, obj){
    url = getCommonUrl(url)
	
	showProgressAnimation();
	if (type == 'form'){		
		$('product_addtocart_form').action += url;		

        $('product_addtocart_form').request({
            onComplete:  function(resp){

                if (typeof(resp.responseText) == 'string'){
					try{
						eval('resp = ' + resp.responseText);
					}catch(e){
						return obj.form.submit();
					}
				}
				hideProgressAnimation();
				if (resp.r != 'success'){
					obj.form.submit();
				}
				else{
					__onACPRender();
                    if(AW_ACP.useConfirmation && (url.search('is_checkout/1') != 1)){
						showConfirmDialog();
					}
					updateCartView(resp);
				}
			}
        })

	}
	if (type == 'url'){
		new Ajax.Request(url, {
          onSuccess: function(resp){
				try{
					if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
				}catch(e){
					win.location.href=url;
					hideProgressAnimation();
					return;
				}
				hideProgressAnimation();
                if (resp.r != 'success'){
					win.location.href=url;
				}
				else{		
                    if(AW_ACP.useConfirmation && (url.search('is_checkout/1') == -1)){
						showConfirmDialog();	
					}
					__onACPRender();
					updateCartView(resp);
				}
			}
        });

	}
}

function __onACPRender(){
    if(AW_ACP.onRender && AW_ACP.onRender.length){
	$A(AW_ACP.onRender).each(function(h){h(AW_ACP)})
    }
}

function addEffectACP(obj, effect)
{
    if (effect == 'opacity'){
        $(obj).hide();
        new Effect.Appear(obj);

	}
	if (effect == 'grow'){
        $(obj).hide();
        new Effect.BlindDown(obj);
	}
	if (effect == 'blink'){
        new Effect.Pulsate(obj);
	}
}


function updateDeleteLinks(){
	var tmpLinks = document.links;
	for (i=0; i<tmpLinks.length; i++){
		if (tmpLinks[i].href.search('checkout/cart/delete') != -1){
			url = tmpLinks[i].href.replace(/\/uenc\/.+,/g, "");
			var del = url.match(/delete\/id\/\d+\//g);
			var id = del[0].match(/\d+/g);
			if (window.location.protocol == 'https:'){
				aw_base_url = aw_base_url.replace("http:", "https:");
			}	
			if(!AW_ACP.isCartPage){
				tmpLinks[i].href = 'javascript:ajaxcartprodelete("' + aw_base_url + 'ajaxcartpro/cart/remove/id/' + id +'")';
			}else{
				tmpLinks[i].href = 'javascript:ajaxcartprodelete("' + aw_base_url + 'ajaxcartpro/cart/remove/id/' + id +'/is_checkout/1")';
			}
		}
	}
}

function updateTopLinks(resp){
    if($$(aw_topLinkCartClass).length){
        $$(aw_topLinkCartClass)[0].title = $$(aw_topLinkCartClass)[0].innerHTML = resp.links;
    }
}

function updateWishlistTopLinks(resp){
    if($$(aw_topWishlistLinkCartClass).length){
        $$(aw_topWishlistLinkCartClass)[0].innerHTML = resp.wishlist_links;
    }
}

window.updateBigCartView = function (resp){
	
    $$(aw_bigCartClass)[0].innerHTML = resp.cart
	if($('shopping-cart-table')){
		decorateTable('shopping-cart-table')
	}

    updateDeleteLinks();
	updateTopLinks(resp);
	updateAddLinks();
	
	
	var scripts = resp.cart.match(/<script[^>]*>([^<]+)<\/script>/gim);
    if (scripts)
    {
        for(var i=0; i<scripts.length; i++){
            var code = scripts[i].match(/<script[^>]*>([^<]+)<\/script>/im)[1].replace(/var\s+/g, '');

            try{
                eval(code)
            }catch(e){
            }
        }
    }
	
}

function showProgressAnimation(){
	var pW = 260;
	var pH = 50;
	var p = $$('.ajaxcartpro_progress')[0];
	
	p.style.width = pW + 'px';		
	p.style.height = pH + 'px';
	if (Prototype.Browser.IE && !navigator.appVersion.match("8")){
		p.style.position = 'absolute';
		window.ACPTop = 200;
	}
	if (aw_ajaxcartpro_proganim == 'center'){		
		if (!(Prototype.Browser.IE && !navigator.appVersion.match("8"))){
			p.style.top = (screen.height/2) - (pH) + 'px';
		}else{
		    window.ACPTop = 200;
		}
	}
	if (aw_ajaxcartpro_proganim == 'top'){		
		if (!(Prototype.Browser.IE && !navigator.appVersion.match("8"))){
		    p.style.top = '0px';
		}else{
		     // IE7-
		    window.ACPTop = 0;
		}
	}
	if (aw_ajaxcartpro_proganim == 'bottom'){
		
		p.style.bottom = '0px';
	}
	if (aw_ajaxcartpro_proganim != 'none'){
		p.style.display = 'block';	
	}
	
}

var beginCounter;
Event.observe(window, 'load',
      function() 
      {
        if(typeof $$('#ACPcountdown')[0] != 'undefined')
            beginCounter = parseInt($$('#ACPcountdown')[0].innerHTML);
      }
    );


function showConfirmDialog(){
    var pW = 260;
	var pH = 104;
	var p = $$('.ajaxcartpro_confirm')[0];
	p.style.width = pW + 'px';		
	p.style.height = pH + 'px';
	
    if (Prototype.Browser.IE && !navigator.appVersion.match("8")){
		p.style.position = 'absolute';
	}else{
		p.style.position = 'fixed';
		if (aw_ajaxcartpro_proganim == 'center'){		
			p.style.top = (screen.height/2) - (pH) + 'px';
		}
		if (aw_ajaxcartpro_proganim == 'top'){		
			p.style.top = '0px';
		}
		if (aw_ajaxcartpro_proganim == 'bottom'){
			p.style.bottom = '0px';		
		}
        if (aw_ajaxcartpro_proganim == 'none'){
			p.style.top = (screen.height/2) - (pH) + 'px';
		}
	}
	p.style.display = 'block';

    var ACPcountdown = $$('#ACPcountdown')[0];
    if(typeof ACPcountdown != 'undefined')
    {
        ACPcountdown.innerHTML = beginCounter;
        if (typeof __intId3 != 'undefined') clearInterval(__intId3);
        __intId3 = setInterval(
            function(){
                if ( parseInt(ACPcountdown.innerHTML) ){
                    ACPcountdown.innerHTML = parseInt(ACPcountdown.innerHTML)-1;
                }
                else
                { 
                    clearInterval(__intId3);
                    p.style.display = "none";
                    ACPcountdown.innerHTML = beginCounter;
                }

            },
            1000
        );
    }
}

function hideProgressAnimation(){

	$$('.ajaxcartpro_progress')[0].style.display = 'none';
}

if(!Prototype.Browser.IE6){
	window.onload = function(){
		updateAddLinks()
		
		// Some other onclicks
		$('aw_acp_continue').onclick = function(e){
			e = e||event;
			if(e.preventDefault)
				e.preventDefault()
			$$('.ajaxcartpro_confirm')[0].style.display='none';return false;
		}
		
		$('aw_acp_checkout').onclick = function(e){
			$$('.ajaxcartpro_confirm')[0].style.display='none';return true;
		}	
		
		// Test for minicart
		
		if((typeof aw_cartDivClass != 'undefined') && ($$(aw_cartDivClass).length || ((typeof AW_ACP !== 'undefined') && AW_ACP.isCartPage))){
			updateDeleteLinks();
		}
		
	} 
}

function updateAddLinks(){
	var ats = document.links;
	for (i=ats.length-1; i>=0; i--){
		if (ats[i].href.search('checkout/cart/add') != -1){
			ats[i].onclick = function(link){
				return function(){
					setLocation(link)
				}
			}(ats[i].href);
			ats[i].href="javascript:void(0)";
		}
	}
}

function getCommonUrl(url){
	if(window.location.href.match('www.') && url.match('http://') && !url.match('www.')){
		url = url.replace('http://', 'http://www.');
	}else if(!window.location.href.match('www.') && url.match('http://') && url.match('www.')){
		url = url.replace('www.', '');
	}
	return url;
}
