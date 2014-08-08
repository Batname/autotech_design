<?php
/**
 * @category    	Fishpig
 * @package  	Fishpig_Wordpress
 * @license     	http://fishpig.co.uk/license.txt
 * @author      	Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Resource_Link_Collection extends Fishpig_Wordpress_Model_Resource_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/link');
	}

	/**
	 * Add a category ID filter to the collection
	 *
	 * @param int $categoryId
	 * @return $this
	 */
	public function addCategoryIdFilter($categoryId)
	{
		$taxonomyTable = $this->getTable('wordpress/term_taxonomy');
		$relationshipTable = $this->getTable('wordpress/term_relationship');

		$this->getSelect()
			->join($relationshipTable, "`$relationshipTable`.`object_id` = `main_table`.`link_id`", '')
			->join($taxonomyTable, "`$taxonomyTable`.`term_taxonomy_id` = `$relationshipTable`.`term_taxonomy_id` AND `$taxonomyTable`.`term_id` = $categoryId AND `$taxonomyTable`.`taxonomy` = 'link_category'", '');

		return $this;
	}
}
