<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_AddThis_Block_Icons extends Mage_Core_Block_Template
{
	/**
	 * Generate and return the ShareThis markup
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (Mage::helper('wp_addon_addthis')->isActiveOnPage()) {
			return $this->getHtml($this->getPost());
		}
		
		return parent::_toHtml();
	}
	
	/**
	 * Retrieve the icon HTML for the post
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract $post
	 * @return string
	 */
	public function getHtml(Fishpig_Wordpress_Model_Post_Abstract $post)
	{
		return $this->_getHtml($post, Mage::helper('wp_addon_addthis')->getOption($this->getPosition()));
	}
	
	/**
	 * Retrieve the HTML 
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract
	 * @param string $type
	 * @return string
	 */
	protected function _getHtml($post, $type)
	{
		if ($type === 'custom_string') {
			$html  = str_replace('<div class="addthis_toolbox', '<div %s class="addthis_toolbox', Mage::helper('wp_addon_addthis')->getOption($this->getPosition() . '_custom_string'));
		}
		else if (($html = $this->_getButtonHtmlTemplate($type)) === '') {
			return '';
		}

		return sprintf(
			$html,
			'addthis:url="' . $post->getPermalink() . '" addthis:title="' . addslashes($this->escapeHtml($post->getPostTitle())) . '"'
		);
	}
	
	/**
	 * Retrieve the button template
	 *
	 * @param string $type
	 * @return string
	 */
	protected function _getButtonHtmlTemplate($type)
	{
		$buttons = array(
			'fb_tw_p1_sc' => '<div class="addthis_toolbox addthis_default_style " %s  ><a class="addthis_button_facebook_like" fb:like:layout="button_count"></a><a class="addthis_button_tweet"></a><a class="addthis_button_pinterest_pinit"></a><a class="addthis_counter addthis_pill_style"></a></div>',
			'large_toolbox' => '<div class="addthis_toolbox addthis_default_style addthis_32x32_style" %s ><a class="addthis_button_facebook"></a><a class="addthis_button_twitter"></a><a class="addthis_button_email"></a><a class="addthis_button_pinterest_share"></a><a class="addthis_button_compact"></a><a class="addthis_counter addthis_bubble_style"></a></div>',
			'small_toolbox' => '<div class="addthis_toolbox addthis_default_style addthis_" %s ><a class="addthis_button_facebook"></a><a class="addthis_button_twitter"></a><a class="addthis_button_email"></a><a class="addthis_button_pinterest_share"></a><a class="addthis_button_compact"></a><a class="addthis_counter addthis_bubble_style"></a></div>',
			'plus_one_share_counter' => '<div class="addthis_toolbox addthis_default_style" %s ><a class="addthis_button_google_plusone" g:plusone:size="medium" ></a><a class="addthis_counter addthis_pill_style"></a></div>',
			'small_toolbox_with_share' =>  '<div class="addthis_toolbox addthis_default_style " %s ><a href="//addthis.com/bookmark.php?v='.$this->getVersion().'&amp;username=xa-4d2b47597ad291fb" class="addthis_button_compact">Share</a><span class="addthis_separator">|</span><a class="addthis_button_preferred_1"></a><a class="addthis_button_preferred_2"></a><a class="addthis_button_preferred_3"></a><a class="addthis_button_preferred_4"></a></div>',
			'fb_tw_sc' => '<div class="addthis_toolbox addthis_default_style " %s  ><a class="addthis_button_facebook_like" fb:like:layout="button_count"></a><a class="addthis_button_tweet"></a><a class="addthis_counter addthis_pill_style"></a></div>' , 'img' => 'fb-tw-sc.jpg' , 'name' => 'Like, Tweet, Counter',
			'simple_button' => '<div class="addthis_toolbox addthis_default_style " %s><a href="//addthis.com/bookmark.php?v='.$this->getVersion().'&amp;username=xa-4d2b47f81ddfbdce" class="addthis_button_compact">Share</a></div>',
			'button' => '<div><a class="addthis_button" href="//addthis.com/bookmark.php?v='.$this->getVersion().'" %s><img src="//cache.addthis.com/cachefly/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="Bookmark and Share" style="border:0"/></a></div>',
			'share_counter' => '<div class="addthis_toolbox addthis_default_style " %s  ><a class="addthis_counter"></a></div>',
		);
		
		return isset($buttons[$type]) 
			? $buttons[$type]
			: '';
	}
}
