<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Countdown
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Countdown_Model_Source_Block_Product_Condition extends AW_Countdown_Model_Source_Abstract {

    public function toOptionArray() {
        $_helper = $this->_getHelper();
        return array(
            array('value' => '=', 'label' => $_helper->__('same as')),
            array('value' => '!=', 'label' => $_helper->__('is not same as')),
            array('value' => 'LIKE', 'label' => $_helper->__('contains')),
            array('value' => 'NOT LIKE', 'label' => $_helper->__("doesn't contain")),
            array('value' => '>', 'label' => $_helper->__('greater')),
            array('value' => '<', 'label' => $_helper->__('less')),
            array('value' => '>=', 'label' => $_helper->__('greater or equal')),
            array('value' => '<=', 'label' => $_helper->__('less or equal'))
        );
    }

}
