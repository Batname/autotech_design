<?php
/**
 * Ext4mage Orders2csv Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to Henrik Kier <info@ext4mage.com> so we can send you a copy immediately.
 *
 * @category   Ext4mage
 * @package    Ext4mage_Orders2csv
 * @copyright  Copyright (c) 2012 Ext4mage (http://ext4mage.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Henrik Kier <info@ext4mage.com>
 * */
class Ext4mage_Orders2csv_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
	const XPATH_CONFIG_SETTINGS_IS_ACTIVE		= 'orders2csv/settings/is_active';
	
	protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
    	if (Mage::getStoreConfig(self::XPATH_CONFIG_SETTINGS_IS_ACTIVE)) {
    		
	        $this->getMassactionBlock()->addItem('orders2csv', array(
	             'label'=> Mage::helper('sales')->__('Orders2CSV'),
	             'url'  => $this->getUrl('*/sales_order_orders2csv/makecsv'),
	        ));
    	}
    }
}
?>