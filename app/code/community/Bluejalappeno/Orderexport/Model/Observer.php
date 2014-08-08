<?php
/**
 *
 * @category   Bluejalappeno
 * @package    Bluejalappeno_Orderexport
 * @copyright  Copyright (c) 2012 Wimbolt Ltd (http://www.bluejalappeno.com)
 * @license    http://www.bluejalappeno.com/license.txt - Commercial license
 */


class Bluejalappeno_Orderexport_Model_Observer
{
	public function addMassAction($observer) {
   		$block = $observer->getEvent()->getBlock();
   		if(($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction || $block instanceof Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction)
		&& strstr( $block->getRequest()->getControllerName(), 'sales_order') && Mage::getStoreConfig("order_export/export_orders/active"))
		{
        	$block->addItem('orderexport', array(
                'label' => Mage::helper('sales')->__('Export Orders'),
                'url' => Mage::getModel('adminhtml/url')->getUrl('*/sales_order_export/csvexport'),
            ));
        }
       
   	
   }

}