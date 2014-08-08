<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Smasoft
 * @package     Smasoft_Oneclikorder
 * @copyright   Copyright (c) 2013 Slabko Michail. <l.nagash@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



/**
 * Сustomer field grid renderer
 */
class Smasoft_Oneclickorder_Block_Adminhtml_Orders_Grid_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if ($row->getCustomerId()) {
            $name = $row->getFirstname() . ' ' . $row->getLastname();
            $result = sprintf('<a href="%s">%s</a>', Mage::getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId())), $name);
        } else {
            $result = 'Guest';
        }

        return $result;
    }
}