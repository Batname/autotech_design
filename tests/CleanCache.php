<?php
require_once '/var/www/autotech/autotech.ua/app/Mage.php';
Mage::app();


$observer = Mage::getSingleton('fpc/observer_clean');
$observer->coreCleanCache();