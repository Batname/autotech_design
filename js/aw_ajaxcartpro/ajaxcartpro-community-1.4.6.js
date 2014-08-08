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
			 aw_cartDivClass =
				AW_ACP.theme == 'blank' ?
					'.block-cart' :
					'.mini-cart';

			if(!$$(aw_cartDivClass).length || !$$(aw_cartDivClass)[0].tagName){
				 aw_cartDivClass =  '.block-cart'
			}
		}
		if(typeof aw_topLinkCartClass == 'undefined'){
			 aw_topLinkCartClass = '.top-link-cart';
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
			 aw_wishlistClass = '.my-wishlist';
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

					return;
				}
				hideProgressAnimation();
                __onACPRender()
                updateCartView(resp, '');
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

	var tmpParent = __cartObj.parentNode;
	tmpParent.replaceChild(tmpDiv.firstChild, __cartObj);

	/* Details popup support */

	var __cartObj = $$(aw_cartDivClass)[0];
	var newHeight = __cartObj.offsetHeight;

    addEffectACP(__cartObj, aw_ajaxcartpro_cartanim);
	updateDeleteLinks();
	updateTopLinks(resp);
}

function updateWishlist(resp)
{
	var wishlistObj = $$(aw_wishlistClass)[0];

    if(wishlistObj){
        var tmpDiv = win.document.createElement('div');
        tmpDiv.innerHTML = resp.wishlist;

        var tmpParent = wishlistObj.parentNode;
        tmpParent.replaceChild(tmpDiv.firstChild, wishlistObj);
    }
}

