<?php


require_once '/var/www/autotech/autotech.ua/app/Mage.php';
Mage::app();

$model = Mage::getModel('sitemaster_siterobot/parser');
$model->getContent();

