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
class Ext4mage_Orders2csv_Model_Orders2csv extends Mage_Core_Model_Abstract
{
    const XPATH_CONFIG_SETTINGS_IS_ACTIVE		= 'orders2csv/settings/is_active';
	const XPATH_CONFIG_SETTINGS_FILE_ID        	= 'orders2csv/settings/file';
	const ENCLOSURE = '"';
    const DELIMITER = ';'; // sitemaster - default     const DELIMITER = ','


    /**
     * Main function being called
     *
     * @param $orders Orders (Mage_Sales_Model_Order) to be saved in file.
     * @return String filename
     */
    public function saveOrdersAsCsv($orders){
    	$fileStructur = Mage::getModel('orders2csv/file')->load(Mage::getStoreConfig(self::XPATH_CONFIG_SETTINGS_FILE_ID));
    	$fileName = str_replace(' ', '_', $fileStructur->getTitle());
        $fileName .= '_'.date("Ymd_His").'.csv';
        $fp = fopen(Mage::getBaseDir('export').'/'.$fileName, 'w');
        
        $this->writeTopRow($fp, $fileStructur);
        foreach ($orders as $order) {
        	$order = Mage::getModel('sales/order')->load($order);
            $this->writeLines($order, $fp, $fileStructur);
        }

        fclose($fp);

        return $fileName;
    }

    /**
	 * Writes top row with the names provided in columns title.
	 * 
	 * @param $fp The cvs file
	 * @param $fileStructur The filestructur set in settings
	 */
    protected function writeTopRow($fp, $fileStructur)
    {
    	$columns = $fileStructur->getColumns();
    	 
    	$headerTitles = null;
    	foreach ($columns as $column){
    		// $headerTitles[] = $column->getTitle();  // sitemaster convert "UTF-8" to "CP1251"
            $headerTitles[] = mb_convert_encoding($column->getTitle(), "CP1251", "UTF-8");

        }
    	
    	fputcsv($fp, $headerTitles, self::DELIMITER, self::ENCLOSURE);
    }

    /**
	 * Make the single order lines
	 * 
	 * @param Mage_Sales_Model_Order $order The order to write csv of
	 * @param $fp The file handle of the csv file
	 */
    protected function writeLines($order, $fp, $fileStructur) 
    {
        $common = $this->getCommonOrderValues($order);

        $columns = $fileStructur->getColumns();
        $rows = null;
        $values = null;
        $runItems = false;
        foreach ($columns as $column){

            // look at this loop - items

        	$value = "";
        	$matches = null;
        	
        	if(preg_match('/order_data_(.*)/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->getData($matches[1]), $column->getValue());
        	}elseif(preg_match('/order_shipping_data_(.*)/',$column->getValue(),$matches)){
        		if(is_object($order->getShippingAddress()))
        			$value = preg_replace('/'.$matches[0].'/', $order->getShippingAddress()->getData($matches[1]), $column->getValue());
        	}elseif(preg_match('/order_shipping_country_name/',$column->getValue(),$matches)){
        		if(is_object($order->getShippingAddress()))
        			$value = preg_replace('/'.$matches[0].'/', $order->getShippingAddress()->getCountryModel()->getName(), $column->getValue());
        	}elseif(preg_match('/order_billing_data_(.*)/',$column->getValue(),$matches)){
        		if(is_object($order->getBillingAddress()))
        			$value = preg_replace('/'.$matches[0].'/', $order->getBillingAddress()->getData($matches[1]), $column->getValue());
        	}elseif(preg_match('/order_billing_country_name/',$column->getValue(),$matches)){
        		if(is_object($order->getBillingAddress()))
        			$value = preg_replace('/'.$matches[0].'/', $order->getBillingAddress()->getCountryModel()->getName(), $column->getValue());
        	}elseif(preg_match('/order_shipping_description/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->getShippingDescription(), $column->getValue());
        	}elseif(preg_match('/order_payment_block/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', preg_replace("{{{pdf_row_separator}}}", " : ",Mage::helper("payment")->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf()), $column->getValue());
        	}elseif(preg_match('/order_store_url/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->getStore()->getUrl(), $column->getValue());
        	}elseif(preg_match('/order_store_base_url/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->getStore()->getBaseUrl(), $column->getValue());
        	}elseif(preg_match('/order_num_invoices}}/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->hasInvoices(), $column->getValue());
        	}elseif(preg_match('/order_num_shipments}}/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->hasShipments(), $column->getValue());
        	}elseif(preg_match('/order_num_creditmemos}}/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->hasCreditmemos(), $column->getValue());
        	}

        	if(in_array($matches[0], Mage::helper('orders2csv')->getCurrencyKeys())){
        		switch($fileStructur->getNumFormatting()){
        			case 2:
        				$value = $order->getStore()->convertPrice($value);
        				break;
        			case 3:
        				$value = $order->formatPriceTxt($value);
        				break;
        		}
        	}
        	
        	if(preg_match('/item_(.*)/',$column->getValue())){
        		$runItems = true;
        	}
        	//$values[$column->getValue()] = $value; //sitemaster convert
            $values[$column->getValue()] = mb_convert_encoding($value, "CP1251", "UTF-8");



        }

        $orderItems = $order->getItemsCollection();
        if($runItems){
	        foreach ($orderItems as $item){
	            if (!$item->isDummy()) {
                    // look at this loop - order
	            	foreach ($columns as $column){
	            		$value = "";
	            		$matches = null;

	            		if(preg_match('/item_data_(.*)/',$column->getValue(),$matches)){
	        				$value = preg_replace('/'.$matches[0].'/',$item->getData($matches[1]), $column->getValue());
	            		}elseif(preg_match('/item_status/',$column->getValue(),$matches)){
	        				$value = preg_replace('/'.$matches[0].'/',$item->getStatus(), $column->getValue());
	            		}

	            		if(in_array($matches[0], Mage::helper('orders2csv')->getCurrencyKeys())){
	            			switch($fileStructur->getNumFormatting()){
	            				case 2:
	            					$value = $order->getStore()->convertPrice($value);
	            					break;
	            				case 3:
	            					$value = $order->formatPriceTxt($value);
	            					break;
	            			}
	            		}
	            		if($value != null)
	            			//$values[$column->getValue()] = $value;  // sitemaster
                        $values[$column->getValue()] = mb_convert_encoding($value, "CP1251", "UTF-8");

                    }

	            	$options = $this->getItemOptions($item);
	            	foreach ($options as $option){
		            	foreach ($columns as $column){
		            		$value = "";
		            		$matches = null;
		            		if(preg_match('/item_option_data_(.*)/',$column->getValue(),$matches)){
		            			$value = preg_replace('/'.$matches[0].'/', $option[$matches[1]], $column->getValue());
		            		}
		            		if($value != null)
		            			$values[$column->getValue()] = $value;
		            	}
		            	$rows[] = $values;
	            	}
	            	if(count($options)==0)
	            		$rows[] = $values;

	            }
	        }

	        foreach ($rows as $row)
	        	fputcsv($fp, $row, self::DELIMITER, self::ENCLOSURE);


        }else{
        	fputcsv($fp, $values, self::DELIMITER, self::ENCLOSURE);
        }
    }

	/**
	 * Get all option values from a general item
	 *
	 * @param item $item
	 * @return array of options
	 */
	public function getItemOptions($item) {
		$result = array();
		if(method_exists($item, 'getOrderItem')){
			$orderItem = $item->getOrderItem();
		}else{
			$orderItem = $item;
		}
		if ($options = $orderItem->getProductOptions()) {
			if (isset($options['options'])) {
				$result = array_merge($result, $options['options']);
			}
			if (isset($options['additional_options'])) {
				$result = array_merge($result, $options['additional_options']);
			}
			if (isset($options['attributes_info'])) {
				$result = array_merge($result, $options['attributes_info']);
			}
		}
		return $result;
	}
}
?>