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
var awunlock = function(){
    try{
        if(checkout.accordion.currentSection == 'opc-shipping'){
            new Effect.Fade($('aw_clickcheckout'), { duration:0.3,from:0.3, to:0});
        }else{
            setTimeout('awunlock()',1);
        }
    }catch(e){}
}
AwclickSubmitter = Class.create(
    {
        initialize:function () {
            this.url = window.location.toString().split('/');
            try {
                this.id = parseInt(this.url.last())
            } catch (e) {
            }
        },
        submitBilling:function () {
            if (this.id > 0) {
                try {
                    $('aw_clickcheckout').style.display='block';
                    awunlock();
                    $('billing-address-select').value = this.id;
                    $('billing:use_for_shipping_no').click();
                    billing.save();
                } catch (e) {
                    setTimeout('submitter.submitBilling()', 2);
                }
            }
        }
    }
);

