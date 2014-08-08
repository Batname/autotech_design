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
 
window.intPrevious = setInterval(function(){
	if(typeof AW_ACP != 'undefined' && document.body){
		if(typeof aw_cartDivClass == 'undefined'){
			 if (AW_ACP.theme == 'blank') aw_cartDivClass = '.topCartContent';
             else aw_cartDivClass = '.top-cart';
               
            if(!$$(aw_cartDivClass).length || !$$(aw_cartDivClass)[0].tagName){
				 aw_cartDivClass =  '.block-cart'
			}
		}
		if(typeof aw_topLinkCartClass == 'undefined'){
			 aw_topLinkCartClass = '#cartHeader';
		}
		if(typeof aw_addToCartButtonClass == 'undefined'){
			 aw_addToCartButtonClass = '.form-button';
		}
		if(typeof aw_bigCartClass == 'undefined'){
			 aw_bigCartClass = 
				AW_ACP.theme == 'modern' ?
					'.layout-1column':
					'.col-main';
		}
        if(typeof aw_wishlistClass == 'undefined'){
			 aw_wishlistClass = '.my-account';
		}

        if(typeof aw_topWishlistLinkCartClass == 'undefined'){
            aw_topWishlistLinkCartClass = '.top-link-wishlist';
        }
 

		if (window.location.toString().search('/product_compare/') != -1){
			win = window.opener;
		}
		else{
			win = window;
		}
		clearInterval(intPrevious)
	}
}, 500);
 
function ajaxcartprodelete(url){	
	showProgressAnimation();
	url = getCommonUrl(url)

    new Ajax.Request(url, {
          onSuccess: function(resp){
                try{
					if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
                    
				}catch(e){

				}
                __onACPRender();
                updateCartView(resp, '');
                
                if (url.search("/is_checkout/1") != -1)
                {
                    url = url.replace("/is_checkout/1", "");
                    new Ajax.Request(url, {
                          onSuccess: function(resp){
                                try{
                                    if (typeof(resp.responseText) == 'string') eval('resp = ' + resp.responseText);
                                 }catch(e){
                                    window.location.href=url;
                                    hideProgressAnimation();
                                    return;
                                }
                                hideProgressAnimation();

                                tmp = AW_ACP.isCartPage;
                                AW_ACP.isCartPage = false;
                                if (resp.r != 'success'){
                                    window.location.href=url;
                                }
                                else{
                                    __onACPRender();
                                    updateCartView(resp);
                                }
                                AW_ACP.isCartPage = tmp;
                            }
                    });
                }
                else
                { hideProgressAnimation(); }
			}
        });
}

function updateCartView(resp){
    if (AW_ACP.isCartPage) return updateBigCartView(resp);

    var __cartObj = $$(aw_cartDivClass)[0];
	
	if(!__cartObj) return false;
	
	if (typeof(__cartObj.length) == 'number') __cartObj = __cartObj[0];
	var oldHeight = __cartObj.offsetHeight;
	
    var tmpDiv = win.document.createElement('div');
	tmpDiv.innerHTML = resp.cart;
	
	tmpDiv.cleanWhitespace();
    __cartObj.replace(tmpDiv.firstChild);
	
    var __cartObj = $$(aw_cartDivClass)[0];
	
	var newHeight = __cartObj.offsetHeight;
	
	addEffectACP(__cartObj, aw_ajaxcartpro_cartanim);

    updateDeleteLinks();
}

function updateWishlist(resp)
{
	var wishlistObj = $$(aw_wishlistClass)[0];

    if(wishlistObj){
        var tmpDiv = win.document.createElement('div');
        tmpDiv.innerHTML = resp.wishlist;

        var tmpParent = wishlistObj.parentNode;
        wishlistObj.innerHTML = tmpDiv.innerHTML;
    }
}
