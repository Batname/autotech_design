/*
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
 * @package    AW_Clickcheckout
 * @copyright  Copyright (c) 2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */
function repos(){
    $('aw_clickcheckout_popup').style.top = Math.abs((document.viewport.getHeight()/2-$('aw_clickcheckout_popup').getHeight()/2))+ 'px';
    setTimeout('repos()',1);
}
AwclickPopup = Class.create(
    {
        initialize:function (response) {
            try{
            this.data = response.evalJSON();
            this.popup = $('aw_clickcheckout_popup');
            this.totals = $('aw_clickcheckout_totals_block');
            this.items = $('aw_clickcheckout_items_block');
            this.shipping = $('aw_clickcheckout_shipping_block');
            this.paymentm = $('aw_clickcheckout_paymentm_block');
            this.agreements = $('aw_clickcheckout_agreements_block');
            this.awbackground = $('aw_background');
            this.points = $('aw_clickcheckout_points');
            }catch(e){}

        },
        fadeIn:function(){
            new Effect.Appear(this.popup,{duration:0.5, from:0, to:1.0});

        },
        displayPopup:function () {

            this.awbackground.style.position = 'fixed';
            this.awbackground.style.zIndex = '9990';
            //this.awbackground.style.opacity = '0.5';

            this.awbackground.style.display = 'block';
            this.awbackground.style.left = '0px';
            this.awbackground.style.top = '0px';
            this.awbackground.style.height = document.viewport.getHeight() + 'px';
            this.awbackground.style.width = document.viewport.getWidth() + 'px';

            $('aw_background').onclick = function () {
                new Effect.Fade($('aw_clickcheckout_popup'), { duration:0.5,from:1, to:0});
                new Effect.Fade($('aw_background'), { duration:0.5,from:0.5, to:0});
            };

            if (Prototype.Browser.IE && !Prototype.Browser.IE8) {
               this.popup.style.position = 'fixed';
            }
            this.popup.onresize = function(){
                $('aw_clickcheckout_popup').style.top = Math.abs((document.viewport.getHeight()/2-$('aw_clickcheckout_popup').getHeight()/2))+ 'px';
            }

            repos();
            try{
            this.popup.style.left = document.viewport.getWidth() / 2 - 200 + 'px';
            this.totals.update(this.data.subtotals);
            this.items.update(this.data.items);
            this.paymentm.update(this.data.paymentm);
            payment.init();
            this.shipping.update(this.data.shipping);
            this.agreements.update(this.data.agreements);
            }catch(e){};
            try{
                if($('aw_oneclick_default_method')!=NaN)
            $('p_method_'+$('aw_oneclick_default_method').value).click();
            }catch(e){}
            try{
            this.popup.style.top = Math.abs((document.viewport.getHeight()/2-this.popup.getHeight()/2))+ 'px';
            this.fadeIn();
            decorateTable('checkout-review-table');
            truncateOptions();
            $('aw_clickcheckout_checkout_button').disabled = false;
            oneClickCheckout.recalculateTotals(validcont,placecont);
            }catch(e){}
            try{
                this.points.update(this.data.points);
            }catch(e){}
        }
    }
);

AwclickCheckout = Class.create(
    {
        initialize:function () {
            this.block = $('aw_clickcheckout_progress');
        },
        showProgress:function () {
            if (Prototype.Browser.IE && !Prototype.Browser.IE8) {
                this.block.style.position = 'fixed';
            }
            this.block.style.zIndex = 999999;
            this.block.style.display = 'block';
            this.block.style.left = document.viewport.getWidth() / 2 - 150 + 'px';
            this.block.style.top = document.viewport.getHeight() / 2  - 20 + 'px';
        },
        useACP:function (resp) {
            try{
            var response = resp.evalJSON()
            updateCartView(response);
            updateTopLinks(response);
            }catch(e){}
        },
        callPopup:function (url){
            var aForm = $('oneclick_cart');
            if($('billing_address_id')!=null){
                if($('billing_address_id').value=='0'){
                    aForm.action = this.cont;
                    aForm.submit();
                    return;
                }
            }
            if($('shipping_address_id')!=null){
                if($('shipping_address_id').value=='0')
                {
                    aForm.action = this.cont+'billing/'+$('billing_address_id').value;
                    aForm.submit();
                    return;
                }
            }
            if($('shipping_address_id')!=null && $('billing_address_id')!=null){
                if($('shipping_address_id').value=='0' || $('billing_address_id').value=='0'){
                    aForm.action = this.cont;
                    aForm.submit();
                    return;
                }
            }
            this.showProgress();
            cartForm = $('oneclick_cart');
            cartForm.request({
                onComplete:function(resp){
                    $('aw_clickcheckout_progress').hide();
                    if (resp.responseText!=null) {
                        try{
                            var check = resp.responseText.evalJSON();
                            if(check.redirect!= null){
                                window.location=check.redirect;
                                return;
                            }
                            if(check.redirector!= null){
                                window.location=check.redirector;
                                return;
                            }
                            var response = resp.responseText || " ";
                            onepopup = new AwclickPopup(response);
                            onepopup.displayPopup();
                        }catch(e){
                        }
                    }
                }
            });
        },
        callController:function () {
            var aForm = $('product_addtocart_form');
            var oldUrl = aForm.action;
            var sep = '?';
            if(aForm.action.indexOf('?') != -1){
                sep = '&';
            }
            aForm.action = aForm.action + sep +'oneclick=1';
            if($('billing_address_id')!=null){
                if($('billing_address_id').value=='0'){
                    aForm.action = this.cont;
                    aForm.submit();
                    return;
                }
            }
            if($('shipping_address_id')!=null){
                if($('shipping_address_id').value=='0')
                {
                    aForm.action = this.cont;
                    aForm.submit();
                    return;
                }
            }
            if($('shipping_address_id')!=null && $('billing_address_id')!=null){
                if($('shipping_address_id').value=='0' || $('billing_address_id').value=='0'){
                    aForm.action = this.cont;
                    aForm.submit();
                    return;
                }
            }
            this.showProgress();
            aForm.request({
                    onCreate:function (){
                        aForm.action = oldUrl;
                    },
                    onComplete:function (resp) {
                        $('aw_clickcheckout_progress').hide();
                        if (resp.responseText!=null) {
                            try{
                                var check = resp.responseText.evalJSON();
                                if(check.redirect!= null){
                                    window.location=check.redirect;
                                    return;
                                }
                                if(check.redirector!= null){
                                    window.location=check.redirector;
                                    return;
                                }
                                if(check.r == 'error'){
                                    productAddToCartForm.submit();
                                    return;
                                }
                                var response = resp.responseText || " ";
                                onepopup = new AwclickPopup(response);
                                onepopup.displayPopup();
                            }catch(e){
                                productAddToCartForm.submit();
                            }
                            oneClickCheckout.useACP(response);
                        }
                    }
                }
            );
        },
        recalculateTotals:function (url, nativeurl) {
            var oForm = $('co-payment-form');
            oForm.action = url;
            try{
            $('aw_clickcheckout_checkout_button').disabled = true;
            $('aw_clickcheckout_checkout_button').update(buttonwait);
            }catch(e){}
            oForm.request({
                onComplete:function (resp) {
                    try{
                    $('aw_clickcheckout_checkout_button').update(buttonready);
                    $('aw_clickcheckout_checkout_button').disabled = false;
                    oForm.action = nativeurl;
                    var response = resp.responseText || " ";
                    refreshedTotals = response.evalJSON();
                    $('aw_clickcheckout_totals_block').update(refreshedTotals.subtotals);
                    $('aw_clickcheckout_points').update(refreshedTotals.points);
                    }catch(e){};
                }
            });
        },
        placeOrder:function (url) {
            var oForm = $('co-payment-form');
            var vForm = new VarienForm(payment.form);
            validateResult = vForm.validator.validate();
            if(!validateResult){
                return;
            }
            if(window.location.href.match('https://')){
                url=url.replace('http://', 'https://');
            }else{
                url=url.replace('https://', 'http://');
            }
            oForm.action = url;
            try{
            $('aw_clickcheckout_checkout_button').disabled=true;
            }catch(e){};
            this.showProgress();
            oForm.request({
                onComplete:function (resp) {
                    $('aw_clickcheckout_progress').hide();
                    $('aw_clickcheckout_checkout_button').disabled=false;
                    var response = resp.responseText || " ";
                    result = response.evalJSON();
                    if (result.error == false) {
                        if (result.redirect != null) {
                            window.location = result.redirect;
                        } else {
                            if (result.success == true) {
                                window.location = $('aw_oneclick_success').value;
                            } else {
                                window.location = $('aw_oneclick_failure').value;
                            }
                        }
                    } else {
                        if (typeof(result.error_messages)) {
                            alert(result.error_messages);
                        }
                    }
                }
            });
        }
    }
);
function setAgreements(status,count){
    for(var i=1;i<count+1;i++){
        try{
        if(status==true){$('agreement-'+i).disabled=false;}
        else{$('agreement-'+i).disabled=true;}
        }catch(e){}
    }
}
function toggleToolTip(event){
            if($('payment-tool-tip')){
                $('payment-tool-tip').setStyle({
                    top: (Event.pointerY(event)-560)+'px'//,
                    //left: (Event.pointerX(event)+100)+'px'
                })
                $('payment-tool-tip').toggle();
            }
            Event.stop(event);

        if($('payment-tool-tip-close')){
            Event.observe($('payment-tool-tip-close'), 'click', toggleToolTip);
        };
}
