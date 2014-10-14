<?php
header('Content-Type: text/html; charset=utf-8');
require_once '../app/Mage.php';
Mage::app();

$value = Mage::getConfig()->getNode('default/checkout/sitemaster/after_checkout_javascript');
var_dump($value);