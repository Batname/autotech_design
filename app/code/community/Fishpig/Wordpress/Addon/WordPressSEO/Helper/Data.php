<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_WordPressSEO_Helper_Data extends Fishpig_Wordpress_Helper_Plugin_Seo_Abstract
{
	/**
	 * A list of option fields used by the extension
	 * All fields are prefixed with wpseo_
	 *
	 * @var array
	 */
	protected $_optionFields = array('', 'titles', 'xml', 'social', 'rss', 'internallinks');
	
	/**
	 * The value used to separate token's in the title
	 *
	 * @var string
	 */
	protected $_rewriteTitleToken = '%%';

	/**
	 * Automatically load the plugin options
	 *
	 */
	protected function _construct()
	{
		parent::_construct();

		$data = array();
		
		foreach($this->_optionFields as $key) {
			if ($key !== '') {
				$key = '_' . $key;
			}

			$options = Mage::helper('wordpress')->getWpOption('wpseo' . $key);
			
			if ($options) {
				$options = unserialize($options);

				foreach($options as $key => $value) {
					if (strpos($key, '-') !== false) {
						unset($options[$key]);
						$options[str_replace('-', '_', $key)] = $value;
					}
				}
				
				$data = array_merge($data, $options);
			}
		}

		$this->setData($data);
	}

	/**
	 * Determine whether All In One SEO is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::helper('wordpress')->isPluginEnabled('Wordpress SEO');
	}
	
	/**
	 * Perform global actions after the user_func has been called
	 *
	 * @return $this
	 */	
	protected function _afterObserver()
	{
		$headBlock = $this->_getHeadBlock();
		
		$robots = array();
			
		if ($this->getNoodp()) {
			$robots[] = 'noodp';
		}
			
		if ($this->getNoydir()) {
			$robots[] = 'noydir';
		}
		
		if (count($robots) > 0) {
			if ($headBlock->getRobots() === '*') {
				$headBlock->setRobots('index,follow,' . implode(',', $robots));
			}
			else {
				$robots = array_unique(array_merge(explode(',', $headBlock->getRobots()), $robots));

				$headBlock->setRobots(implode(',', $robots));
			}
		}

		$this->_updateBreadcrumb('blog', $this->getBreadcrumbsHome());

		return $this;
	}

	/**
	 * Process the SEO values for the homepage
	 *
	 * @param $action
	 * @param Varien_Object $object
	 */	
	public function processRouteWordPressIndexIndex($object = null)
	{
		if (($headBlock = $this->_getHeadBlock()) !== false) {
			$this->_applyMeta(array(
				'title' => $this->_getTitleFormat('home'),
				'description' => trim($this->getMetadescHome()),
				'keywords' => trim($this->getMetakeyHome()),
			));
			
			$this->_applyOpenGraph(array());
			
			if ($this->getPlusAuthor()) {
				$this->_addGooglePlusLinkRel($this->getPlusAuthor());
			}
		}
			
		return $this;
	}

	/**
	 * Process the SEO values for the blog view page
	 *
	 * @param $action
	 * @param Varien_Object $post
	 */	
	public function processRouteWordPressPostView($post)	
	{
		$this->_applyPostPageLogic($post);

		return $this;
	}
	
	/**
	 * Process the SEO values for the blog view page
	 *
	 * @param $action
	 * @param Varien_Object $page
	 */	
	public function processRouteWordPressPageView($page)	
	{
		$this->_applyPostPageLogic($page, 'page');

		return $this;
	}
	
	/**
	 * Process the SEO values for the blog view page
	 *
	 * @param Varien_Object $object
	 *  @param string $type
	 * @param Varien_Object $page
	 */	
	protected function _applyPostPageLogic($object, $type = 'post')
	{
		$meta = new Varien_Object(array(
			'title' => $this->_getTitleFormat($type),
			'description' => trim($this->getData('metadesc_' . $type)),
			'keywords' => trim($this->getData('metakey_' . $type)),
		));

		if (($value = trim($object->getMetaValue('_yoast_wpseo_title'))) !== '') {
			$data = $this->getRewriteData();
			$data['title'] = $value;
			$this->setRewriteData($data);
		}
		
		if (($value = trim($object->getMetaValue('_yoast_wpseo_metadesc'))) !== '') {
			$meta->setDescription($value);
		}
		
		if (($value = trim($object->getMetaValue('_yoast_wpseo_metakeywords'))) !== '') {
			$meta->setKeywords($value);
		}
		
		$robots = array();

		$noIndex = (int)$object->getMetaValue('_yoast_wpseo_meta-robots-noindex');

		if ($noIndex === 0) {
			$robots['index'] = '';
		}
		else if ($noIndex === 1) {
			$robots['noindex'] = '';
		}
		else if ($noIndex === 2) {
			$robots['index'] = '';
		}
		else if ($this->getNoindexPost()) {
			$robots['noindex'] = '';
		}
		
		if ($object->getMetaValue('_yoast_wpseo_meta-robots-nofollow')) {
			$robots['nofollow'] = '';
		}
		else {
			$robots['follow'] = '';
		}

		if (($advancedRobots = trim($object->getMetaValue('_yoast_wpseo_meta-robots-adv'))) !== '') {
			if ($advancedRobots !== 'none') {
				$robots = explode(',', $advancedRobots);
			}
		}
		
		$robots = array_keys($robots);

		if (count($robots) > 0) {
			$meta->setRobots(implode(',', $robots));
		}

		$this->_applyMeta($meta->getData());

		if ($canon = $object->getMetaValue('_yoast_wpseo_canonical')) {
			$object->setCanonicalUrl($canon);			}
		
		$this->_addGooglePlusLinkRel($object->getAuthor());
		
		if ($this->getOpengraph() === 'on') {
			$this->_addPostOpenGraphTags($object, $type);
		}
		
		if ($this->getTwitter() === 'on') {
			$this->_addTwitterCard(array(
				'card' => 'summary',
				'site' => ($this->getData('twitter_site') ? '@' . $this->getData('twitter_site') : ''),
				'title' => $object->getPostTitle(),
				'creator' => ($creator = $object->getAuthor()->getMetaValue('twitter')) ? '@' . $creator : '',
			));
		}
		return $this;
	}
	
	
	protected function _applyOpenGraph(array $tags)
	{
		$head = Mage::getSingleton('core/layout')->getBlock('head');

		if (!$head) {
			return $this;
		}
		
		foreach($tags as $key => $value) {
			if (!is_array($value) && trim($value) === '') {
				unset($tags[$key]);
			}
		}

		$helper = Mage::helper('wordpress');

		$tags = array_merge(array(
			'locale' => Mage::app()->getLocale()->getLocaleCode(),
			'type' => 'blog',
			'title' => $helper->getWpOption('blogname'),
			'url' => $helper->getUrl(),
			'site_name' => $helper->getWpOption('blogname'),
			'article:publisher' => $this->getFacebookSite(),
		), $tags);

		$tagString = '';

		foreach($tags as $key => $value) {
			$tkey = strpos($key, ':') === false ? 'og:' . $key : $key;
			
			if (!is_array($value)) {
				$value = array($value);
			}
			
			foreach($value as $v) {
				if (trim($v) !== '') {
					$tagString .= sprintf('<meta property="%s" content="%s" />', $tkey, addslashes(Mage::helper('core')->escapeHtml($v))) . "\n";
				}
			}
		}

		$head->setChild('wp.openGraph', 
			Mage::getSingleton('core/layout')->createBlock('core/text')->setText($tagString . "\n")
		);
		
		return $this;
	}
	
	/**
	 * Add the open graph tags to the post/page
	 *
	 * @object
	 * @param string $type = 'post'
	 * @return
	 */
	protected function _addPostOpenGraphTags($object, $type = 'post')
	{
		$tags = array(
			'type' => array('publish', 'article'),
			'title' => $object->getPostTitle(),
			'description' => $object->getMetaDescription(),
			'url' => $object->getPermalink(),
			'image' => $object->getFeaturedImage() ? $object->getFeaturedImage()->getFullSizeImage() : '',
			'article:author' => $object->getAuthor()->getMetaValue('facebook'),
		);
		

		if ($head = Mage::getSingleton('core/layout')->getBlock('head')) {
			$tags['description'] = $head->getDescription();
		}
		
		if ($fbDesc = $object->getMetaValue('_yoast_wpseo_opengraph-description')) {
			$tags['description'] = $fbDesc;
		}

		if ($items = $object->getTags()) {
			$tagValue = array();

			foreach($items as $item) {
				$tagValue[] = $item->getName();
			}
			
			$tags['article:tag'] = $tagValue;
		}
		
		if ($items = $object->getParentCategories()) {
			$categoryValue = '';

			foreach($items as $item) {
				$categoryValue[] = $item->getName();
			}
			
			$tags['article:section'] = $categoryValue;
		}

		return $this->_applyOpenGraph($tags);
	}

	/**
	 * Category page
	 *
	 * @param $action
	 * @param Varien_Object $category
	 */
	public function processRouteWordpressPostCategoryView($category)
	{
		$this->_applyMeta(array(
			'title' => $this->_getTitleFormat('category'),
			'description' => $this->getMetadescCategory(),
			'keywords' => $this->getMetakeyCategory(),
			'robots' => $this->getNoindexCategory() ? 'noindex,follow' : '',
		));
		
		if ($meta = @unserialize(Mage::helper('wordpress')->getWpOption('wpseo_taxonomy_meta'))) {
			if (isset($meta['category']) && isset($meta['category'][$category->getId()])) {
				$meta = new Varien_Object((array)$meta['category'][$category->getId()]);

				$this->_applyMeta(array(
					'title' => $meta->getWpseoTitle(),
					'description' => $meta->getWpseoDesc(),
					'keywords' => $meta->getWpseoMetakey(),
				));
				
				if ($meta->getWpseoCanonical()) {
					$category->setCanonicalUrl($meta->getWpseoCanonical());
				}
		
				$this->_applyOpenGraph(array(
					'title' => $meta->getWpseoTitle(),
					'url' => $category->getCanonicalUrl(),
				));
			}
		}
		
		return $this;
	}


	/**
	 * Archive page
	 *
	 * @param $action
	 * @param Varien_Object $archive
	 */
	public function processRouteWordpressArchiveView($archive)
	{
		if ($this->getDisableDate()) {
			$this->_redirect(Mage::helper('wordpress')->getBlogRoute());
		}
		
		$meta = new Varien_Object(array(
			'title' => $this->_getTitleFormat('archive'),
			'description' => $this->getMetadescArchive(),
			'keywords' => $this->getMetakeyArchive(),
			'robots' => $this->getNoindexArchive() ? 'noindex,follow' : '',
		));

		$this->_applyMeta($meta->getData());
		
		$this->_updateBreadcrumb('archive_label', $this->getBreadcrumbsArchiveprefix());
		
		return $this;
	}
	
	/**
	 * Author page
	 *
	 * @param $action
	 * @param Varien_Object $author
	 */
	public function processRouteWordpressAuthorView($author)
	{
		if ($this->getDisableAuthor()) {
			$this->_redirect(Mage::helper('wordpress')->getBlogRoute());
		}
		
		$meta = new Varien_Object(array(
			'title' => $this->_getTitleFormat('author'),
			'description' => $this->getMetadescAuthor(),
			'keywords' => $this->getMetakeyAuthor(),
			'robots' => $this->getNoindexAuthor() ? 'noindex,follow' : '',
		));

		$this->_applyMeta($meta->getData());
		
		$this->_addGooglePlusLinkRel($author);
			
		return $this;
	}
	
	/**
	 * Tag page
	 *
	 * @param $action
	 * @param Varien_Object $tag
	 */
	public function processRouteWordpressPostTagView($tag)
	{
		$meta = new Varien_Object(array(
			'title' => $this->_getTitleFormat('post_tag'),
			'description' => $this->getMetadescPostTag(),
			'keywords' => $this->getMetakeyPostTag(),
			'robots' => $this->getNoindexPostTag() ? 'noindex,follow' : '',
		));

		$this->_applyMeta($meta->getData());

		return $this;
	}
	
	/**
	 * Process the search results page
	 *
	 * @param $action
	 * @param $object
	 */
	public function processRouteWordpressSearchIndex($object = null)
	{
		$meta = new Varien_Object(array(
			'title' => $this->_getTitleFormat('search'),
		));

		$this->_applyMeta($meta->getData());
		
		$this->_updateBreadcrumb('search_label', $this->getBreadcrumbsSearchprefix());
		
		return $this;		
	}
	
	/**
	 * Retrieve the rewrite data
	 *
	 * @return array
	 */
	public function getRewriteData()
	{
		if (!$this->hasRewriteData()) {
			$data = array(
				'sitename' => Mage::helper('wordpress')->getWpOption('blogname'),
				'sitedesc' => Mage::helper('wordpress')->getWpOption('blogdescription'),
			);
			
			if (($object = Mage::registry('wordpress_post')) !== null || ($object = Mage::registry('wordpress_page')) !== null) {
				$data['date'] = $object->getPostDate();
				$data['title'] = $object->getPostTitle();
				$data['excerpt'] = trim(strip_tags($object->getPostExcerpt()));
				$data['excerpt_only'] = $data['excerpt'];
				
				$categories = array();
				
				if ($object instanceof Fishpig_Wordpress_Model_Post) {
					foreach($object->getParentCategories()->load() as $category) {
						$categories[] = $category->getName();	
					}
				}
				
				$data['category'] = implode(', ', $categories);
				$data['modified'] = $object->getPostModified();
				$data['id'] = $object->getId();
				$data['name'] = $object->getAuthor()->getUserNicename();
				$data['userid'] = $object->getAuthor()->getId();
			}
			
			if (($category = Mage::registry('wordpress_category')) !== null) {
				$data['category_description'] = trim(strip_tags($category->getDescription()));
				$data['term_description'] = $data['category_description'];
				$data['term_title'] = $category->getName();
			}
			
			if (($tag = Mage::registry('wordpress_post_tag')) !== null) {
				$data['tag_description'] = trim(strip_tags($tag->getDescription()));
				$data['term_description'] = $data['tag_description'];
				$data['term_title'] = $tag->getName();
			}
			
			if (($term = Mage::registry('wordpress_term')) !== null) {
				$data['term_description'] = trim(strip_tags($term->getDescription()));
				$data['term_title'] = $term->getName();
			}
			
			if (($archive = Mage::registry('wordpress_archive')) !== null) {
				$data['date'] = $archive->getName();
			}
			
			if (($author = Mage::registry('wordpress_author')) !== null) {
				$data['name'] = $author->getDisplayName();
			}			
			
			$data['currenttime'] = Mage::helper('wordpress')->formatTime(date('Y-m-d H:i:s'));
			$data['currentdate'] = Mage::helper('wordpress')->formatDate(date('Y-m-d H:i:s'));
			$data['currentmonth'] = date('F');
			$data['currentyear'] = date('Y');
			$data['sep'] = '|';

			if (($value = trim(Mage::helper('wordpress/router')->getSearchTerm(true))) !== '') {
				$data['searchphrase'] = $value;
			}

			$this->setRewriteData($data);
		}
		
		return $this->_getData('rewrite_data');		
	}
	
	/**
	 * Retrieve the title format for the given key
	 *
	 * @param string $key
	 * @return string
	 */
	protected function _getTitleFormat($key)
	{
		return trim($this->getData('title_' . $key));
	}
	
	/**
	 * Add the Google Plus rel="author" tag
	 *
	 * @param int|Fishpig_Wordpress_Model_User
	 * @return $this
	 */
	protected function _addGooglePlusLinkRel($user)
	{
		if (!is_object($user)) {
			$user = Mage::getModel('wordpress/user')->load($user);
			
			if (!$user->getId()) {
				return $this;
			}
		}
		
		if ($user->getId() && $user->getMetaValue('googleplus')) {
			$this->_getHeadBlock()->addItem('link_rel', $user->getMetaValue('googleplus'), 'rel="author"');
		}
		
		if ($publisher = $this->getData('plus_publisher')) {
			$this->_getHeadBlock()->addItem('link_rel', $publisher, 'rel="publisher"');
		}

		return $this;	
	}
	
	/**
	 * Add a Twitter card to the head
	 *
	 * @param array $tafs
	 * @return $this
	 */
	protected function _addTwitterCard(array $tags)
	{
		if (($head = Mage::getSingleton('core/layout')->getBlock('head')) !== false) {
			foreach($tags as $key => $value) {
				if (trim($value) !== '') {
					$tags[$key] = sprintf('<meta name="twitter:%s" content="%s" />', $key, addslashes(Mage::helper('core')->escapeHtml($value)));
				}
				else {
					unset($tags[$key]);
				}
			}

			$head->setChild('wp.twitterCard', 
				Mage::getSingleton('core/layout')->createBlock('core/text')->setText(implode("\n", $tags) . "\n")
			);
		}

		return $this;
	}
	
	/**
	 * Given a key that determines which format to load
	 * and a data array, merge the 2 to create a valid title
	 *
	 * @param string $key
	 * @return string|false
	 */
	protected function _rewriteString($format)
	{
		if (($value = parent::_rewriteString($format)) !== false) {
			$data = $this->getRewriteData();

			if (is_array($data) && isset($data['sep'])) {
				$value = trim($value, $data['sep'] . ' -/\|,');
			}
		}
		
		return $value;
	}
}
