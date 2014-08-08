<?php
/**
 * Zeon Solutions, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Zeon Solutions License
 * that is bundled with this package in the file LICENSE_ZE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.zeonsolutions.com/license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zeonsolutions.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.zeonsolutions.com for more information.
 *
 * @category    Zeon
 * @package     Zeon_Manufacturer
 * @copyright   Copyright (c) 2012 Zeon Solutions, Inc. All Rights Reserved.(http://www.zeonsolutions.com)
 * @license     http://www.zeonsolutions.com/license/
 */

/**
 * Manufacturer Controller Router
 *
 * @category    Zeon
 * @package     Zeon Manufacturer
 * @author      Zeon Solutions Core Team
 */
class Zeon_Manufacturer_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('manufacturers', $this);
    }

    /**
     * Validate and Match Manufacturer Page and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $router = 'manufacturers';
        $identifier = trim(str_replace('/manufacturers/', '', $request->getPathInfo()), '/');

        $condition = new Varien_Object(array(
                'identifier' => $identifier,
                'continue'   => true
        ));
        Mage::dispatchEvent('manufacturer_controller_router_match_before', array(
                'router'    => $this,
                'condition' => $condition
        ));
        $identifier = $condition->getIdentifier();

        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }
        if (!$condition->getContinue()) {
            return false;
        }
        $manufacturer = Mage::getModel('zeon_manufacturer/manufacturer');
        $manufacturerId = $manufacturer->checkIdentifier($identifier, Mage::app()->getStore()->getId());
        if (trim($identifier) && $manufacturerId) {
            $request->setModuleName('manufacturers')
                ->setControllerName('index')
                ->setActionName('view')
                ->setParam('manufacturer_id', $manufacturerId);
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $router.'/'.$identifier
            );
            return true;
        }
        return false;
    }
}
