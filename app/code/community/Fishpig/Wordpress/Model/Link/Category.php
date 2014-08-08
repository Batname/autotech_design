<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Link_Category extends Fishpig_Wordpress_Model_Term
{
	public function _construct()
	{
		$this->_init('wordpress/link_category');
	}

	/**
	 * Retrieve the taxonomy type
	 *
	 * @return string
	 */
	public function getTaxonomy()
	{
		return 'link_category';
	}
	
	/**
	 * Retrieve a collection of links that belong to this category
	 * Added for backwards compatibility
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Link_Collection
	 */	
	public function getLinks()
	{
		return $this->getLinksCollection();
	}
	
	/**
	 * Retrieve a collection of links that belong to this category
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Link_Collection
	 */
	public function getLinksCollection()
	{
		if (!$this->hasLinksCollection()) {
			$links = Mage::getResourceModel('wordpress/link_collection')
			->addCategoryIdFilter($this->getId());
			
			$this->setLinks($links);
		}
		
		return $this->_getData('links');
	}
}
