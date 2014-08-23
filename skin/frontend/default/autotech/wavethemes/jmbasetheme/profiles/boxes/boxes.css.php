<?php
	$mageFilename = "../../../../../../../../app/Mage.php";
	require_once $mageFilename; 
	umask(0);
	Mage::app();
	$baseconfig = Mage::helper("jmbasetheme")->getactiveprofile();
	header("Content-type: text/css; charset: UTF-8");
?>

/* Base settings */
body#bd {
	background-image:url("images/<?php echo $baseconfig["bgimage"]; ?>");
	background-color: <?php echo $baseconfig["bgolor"] ?>;
}

#jm-wrapper{
	background-image:url("images/<?php echo $baseconfig["wrapperbg"]; ?>");
	background-color: <?php echo $baseconfig["wrappercolor"] ?>;
	box-shadow: 0 0 5px #DDDDDD;
	margin: 0 auto;
	padding: 0 10px;
	width: 1100px;
}

#jm-head {
	background-image:url("images/<?php echo $baseconfig["headbg"]; ?>");
	background-color: <?php echo $baseconfig["headcolor"] ?>;
}


#jm-header {
	background-image:url("images/<?php echo $baseconfig["headerimage"]; ?>");
	background-color: <?php echo $baseconfig["headerolor"] ?>;
}


#jm-footer {
	background-image:url("images/<?php echo $baseconfig["footerimage"]; ?>");
	background-color: <?php echo $baseconfig["footerolor"] ?>;
	margin: -15px -10px;
}



#jm-tops1 {
	background-image:url("images/<?php echo $baseconfig["tops1bg"]; ?>");
	background-color: <?php echo $baseconfig["tops1color"] ?>;
}

#jm-tops2 {
	background-image:url("images/<?php echo $baseconfig["tops2bg"]; ?>");
	background-color: <?php echo $baseconfig["tops2color"] ?>;
}

#jm-pathway,
#jm-container {
	background-image:url("images/<?php echo $baseconfig["containerbg"]; ?>");
	background-color: <?php echo $baseconfig["containercolor"] ?>;
}


#jm-mass-bottom {
	background-image:url("images/<?php echo $baseconfig["massbottombg"]; ?>");
	background-color: <?php echo $baseconfig["massbottomcolor"] ?>;
}

#jm-bots1 {
	background-image:url("images/<?php echo $baseconfig["bots1bg"]; ?>");
	background-color: <?php echo $baseconfig["bots1color"] ?>;
	margin: 15px -10px;
}

#logo a{
	background-image:url("images/<?php echo $baseconfig["logobg"]; ?>") !important;
}

a,
a:active, a:focus, a:hover,
ul.ul-dropdown li.active a,
ul.ul-dropdown li a:active,
ul.ul-dropdown li a:focus, 
ul.ul-dropdown li a:hover,
.block-cate ul li a:active, 
.block-cate ul li a:focus, 
.block-cate ul li a:hover,
.block-cate h2,
.page-title.category-title h1,
#jm-head a:active, 
#jm-head a:focus, 
#jm-head a:hover,
.product-view .product-shop .add-to-links a:active, 
.product-view .product-shop .add-to-links a:focus, 
.product-view .product-shop .add-to-links a:hover,
ul.ja-tab-navigator li a,
ul.ja-tab-navigator li.active a,
.block-account .block-content li.current,
.block-account .block-content li a:active, 
.block-account .block-content li a:focus, 
.block-account .block-content li a:hover,
.block-tags .block-content a:active, 
.block-tags .block-content a:hover, 
.block-tags .block-content a:focus,
.block-cart .subtotal .price,
.block-cart .summary a,
.jm-megamenu ul.level1 li.mega a.mega:hover, 
.jm-megamenu ul.level1 li.mega:hover > a.mega, 
.jm-megamenu ul.level1 li.mega a.mega:active, 
.jm-megamenu ul.level1 li.mega a.mega:focus, 
.jm-megamenu ul.level1 li.mega a.mega:hover,
.mycart-toggle span,
#narrow-by-list dt,
.product-view .product-img-box .more-views li:focus a i, 
.product-view .product-img-box .more-views li:hover a i, 
.product-view .product-img-box .more-views li.active a i,
.advanced-search-summary strong,
.page-sitemap .sitemap a:active, 
.page-sitemap .sitemap a:focus, 
.page-sitemap .sitemap a:hover,
.tags-list li a:active, 
.tags-list li a:focus, 
.tags-list li a:hover,
.checkout-progress li.active,
.checkout-onepage-index .page-title h1,
ul.ul-dropdown li.active a img, 
ul.ul-dropdown li a:active img, 
ul.ul-dropdown li a:focus img, 
ul.ul-dropdown li a:hover img,
.block-cart.product-details .edit:active, 
.block-cart .product-details .edit:focus, 
.block-cart .product-details .edit:hover, 
.block-cart .product-details .remove:active, 
.block-cart .product-details .remove:focus, 
.block-cart .product-details .remove:hover,
.checkout-progress li.active,
.multiple-checkout h3, 
.multiple-checkout h4,
.multiple-checkout .box h2,
.subtitle, 
.sub-title,
.products-list .add-to-links li a:active,
.products-list .add-to-links li a:focus,
.products-list .add-to-links li a:hover,
.jm-megamenu ul.level2 li.mega li.mega a.mega:active, .jm-megamenu ul.level2 li.mega li.mega a.mega:focus, .jm-megamenu ul.level2 li.mega li.mega a.mega:hover, .jm-megamenu ul.level2 li.mega li a:active, .jm-megamenu ul.level2 li.mega li a:focus, .jm-megamenu ul.level2 li.mega li a:hover,
.addresses-list h2,
#jm-error h3,
#jm-error dl dt,
#jm-error ul.none-disc li a:active,
#jm-error ul.none-disc li a:focus,
#jm-error ul.none-disc li a:hover,
.message h2,
.block-compare ol#compare-items li a.btn-remove:active, 
.block-compare ol#compare-items li a.btn-remove:focus, 
.block-compare ol#compare-items li a.btn-remove:hover,
.block-compare ol#compare-items li a:active, 
.block-compare ol#compare-items li a:focus, 
.block-compare ol#compare-items li a:hover,
#jm-mycart .btn-toggle span{
	 color: <?php echo $baseconfig["topcartcolor"] ?>;
}


.bkg-top-cart,
.icon-label,
.hot-label.icon-label,
.sales-label.icon-label,
.vertical-mega-menu .block-title,
.block-subscribe .input-box button.button, 
.block-subscribe .input-box button.button:focus, 
.block-subscribe .input-box button.button:hover,
#button-btt,
button.jmquickview,
.jm-product-lemmon .prev:focus, .jm-product-lemmon .prev:hover, 
.jm-product-lemmon .next:focus, .jm-product-lemmon .next:hover,
.block-layered-content-filter,
#narrow-by-list dd li .filter-subcat span a:active,
#narrow-by-list dd li .filter-subcat span a:focus,
#narrow-by-list dd li .filter-subcat span a:hover,
.block-layered-content-filter{
	background: <?php echo $baseconfig["topcartcolor"] ?>;
	border-color: <?php echo $baseconfig["topcartcolor"] ?>;
}


.view-mode span.active,
.pages ol li a,
span.price, 
.special-price .price,
.price-notice .price,
ul.list-info li a,
.opc .active .step-title h2,
.jm-legal a,
#jm-bots1 .block-aboutus ul li a,
.breadcrumbs li strong,
.breadcrumbs li a:active, 
.breadcrumbs li a:focus, 
.breadcrumbs li a:hover,
.view-mode a:focus,
.view-mode a:hover,
.product-view .product-shop .availability span{
  color: <?php echo $baseconfig["color"] ?>;
}

.demo-notice,
.remember-me-popup .remember-me-popup-body a,
.dashboard .box-tags .number,
.contact-form .buttons-set button.button:focus, 
.contact-form .buttons-set button.button:hover,
.dashboard .box-reviews .number,
.jm-megamenu,
.opc .active .step-title .number,
#mainnav-inner{
	background-color: <?php echo $baseconfig["menubg"] ?>;
}

#jm-header #jm-mainnav .btn-toggle,
#jm-header #jm-search .btn-toggle{
	background: <?php echo $baseconfig["menucolor"] ?>;
	border-color: <?php echo $baseconfig["menucolor"] ?>;
}

.remember-me-popup .remember-me-popup-body a,
.jm-two-products .products-list button.button:active,
.jm-two-products .products-list button.button:hover,
.jm-two-products .products-list button.button:focus,
.product-view .product-img-box .more-views li.active a, 
.product-view .product-img-box .more-views li a:active, 
.product-view .product-img-box .more-views li a:focus, 
.product-view .product-img-box .more-views li a:hover,
.checkout-progress li.active{
	border-color: <?php echo $baseconfig["topcartcolor"] ?>;
}

.jm-prev:hover, .jm-prev:focus, 
.jm-next:hover, .jm-next:focus{
	background: <?php echo $baseconfig["color"] ?>;
}

.jm-menu-top .jm-megamenu ul li a:active, 
.jm-menu-top .jm-megamenu ul li a:focus, 
.jm-menu-top .jm-megamenu ul li a:hover, 
.jm-menu-top .jm-megamenu ul li.active a,
.jm-megamenu ul.level0 li.mega a.mega:hover, 
.jm-megamenu ul.level0 li.mega:hover > a.mega{
	background: <?php echo $baseconfig["menucolor"] ?>;
}

.new-label.icon-label{
	background-color: <?php echo $baseconfig["color"] ?>;
}

.rating-box .rating{
	background-image:url("images/<?php echo $baseconfig["ratingbg"]; ?>")
}

#jm-mycart .btn-toggle .ico-shopping-cart{
	background-image:url("images/<?php echo $baseconfig["topcartbg"]; ?>")
}

@media (max-width:479px){
	#jm-head .btn-toggle,
	#jm-header .btn-toggle,
	#jm-header #jm-mainnav .btn-toggle, 
	#jm-header #jm-search .btn-toggle,
	#jm-header #jm-mycart .btn-toggle,
	#jm-header #jm-search .btn-toggle.active{
		background-color: <?php echo $baseconfig["menubg"] ?>;
		border-right-color: <?php echo $baseconfig["menucolor"] ?>;
	}
	
	#jm-head .btn-toggle.active, 
	#jm-head .btn-toggle:focus, 
	#jm-head .btn-toggle:hover,
	#jm-header .btn-toggle.active, 
	#jm-header .btn-toggle:focus, 
	#jm-headere .btn-toggle:hover,
	#jm-header #jm-mainnav .btn-toggle.active, 
	#jm-header #jm-mainnav .btn-toggle:focus,
	#jm-header #jm-mainnav .btn-toggle:hover,
	#jm-header #jm-search .btn-toggle.active,
	#jm-header #jm-search .btn-toggle:focus,
	#jm-header #jm-search .btn-toggle:hover,
	#jm-header #jm-mycart .btn-toggle.active,
	#jm-header #jm-mycart .btn-toggle:focus,
	#jm-header #jm-mycart .btn-toggle:hover{
		background-color: <?php echo $baseconfig["menucolor"] ?>;
	}
}