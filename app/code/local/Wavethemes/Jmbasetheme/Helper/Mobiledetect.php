<?php

/**
 * Optimiseweb MobileDetect Helper Data
 *
 * @package     Optimiseweb_MobileDetect
 * @author      Kathir Vel (sid@optimiseweb.co.uk)
 * @copyright   Copyright (c) 2013 Optimise Web Limited
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
if(!class_exists("Mobile_Detect")){
require_once Mage::getModuleDir('', 'Wavethemes_Jmbasetheme') . DS . 'Library' . DS . 'Mobile_Detect.php';
}

class Wavethemes_Jmbasetheme_Helper_Mobiledetect extends Mobile_Detect
{

}