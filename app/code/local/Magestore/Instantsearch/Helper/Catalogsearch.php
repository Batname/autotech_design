<?php

class Magestore_Instantsearch_Helper_Catalogsearch extends Mage_CatalogSearch_Helper_Data
{
	const OVERWRITE_QUICK_SEARCH = 'instantsearch/general/overwrite_quick_search';
	
	public function getResultUrl($query = null)
    {
		if(!Mage::helper('magenotification')->checkLicenseKey('Instantsearch')){
			return parent::getResultUrl($query);
		}
		
		if(Mage::getStoreConfig(self::OVERWRITE_QUICK_SEARCH)){
			return $this->_getUrl('instantsearch');
		}
		else{
			return $this->_getUrl('catalogsearch/result', array(
				'_query' => array(self::QUERY_VAR_NAME => $query),
				'_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
			));
		}
    }
	public function getResultInstantSearch($query = null)
	{        
		return $this->_getUrl('instantsearch');        
    }
}