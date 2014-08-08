<?php

	if (!defined('__FP_UNIT')) {
		return;
	}

	$posts = Mage::getResourceModel('wordpress/post_collection')
		->addIsPublishedFilter()
		->load();

	_title('Post Collection', 2);

	echo '<ul>';
	
	foreach($posts as $post) {
		echo sprintf('<li><a href="%s">%s</a> (%s)</li>', $post->getUrl(), $post->getPostTitle(), implode(', ', $post->getCategoryIds()));
	}
	
	echo '</ul>';
