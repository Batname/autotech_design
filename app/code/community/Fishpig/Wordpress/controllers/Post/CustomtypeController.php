<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Post_CustomtypeController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	 * Used to do things en-masse
	 * eg. include canonical URL
	 *
	 * @return false|Fishpig_Wordpress_Model_Post_Category
	 */
	public function getEntityObject()
	{
		if (($type = Mage::registry('wordpress_post_type')) !== null) {
			return $type;
		}
		
		$postType = Mage::helper('wp_addon_cpt')->getPostTypeBySlug($this->getRequest()->getParam('slug'));
		
		if ($postType !== false) {
			Mage::register('wordpress_post_type', $postType);
			
			return $postType;
		}

		return false;
	}

	/**
	  * Display the category page and list blog posts
	  *
	  */
	public function viewAction()
	{
		$type = $this->getEntityObject();
		
		$this->_addCustomLayoutHandles(array(
			'wordpress_post_customtype',
			'wordpress_post_type_' . $type->getPostType(), 
			'wordpress_post_list',
		));
			
		$this->_initLayout();
		
		$this->_rootTemplates[] = 'post_list';
		
		$this->_title($type->getName());
			
		$this->addCrumb('cpt', array('label' => $type->getName()));
		
		$this->renderLayout();
	}
}
