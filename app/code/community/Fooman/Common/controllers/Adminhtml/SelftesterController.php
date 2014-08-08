<?php

class Fooman_Common_Adminhtml_SelftesterController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        return $this;
    }

    public function indexAction()
    {
        //Here we actually run the process of selftesting. All messages are returned to the session
        $module = $this->getRequest()->getParam('module');
        $selftester = Mage::getModel($module . '/selftester')->main();
        //Here we get db version of the given module code
        if (Mage::getModel('core/mysql4_resource')->getDbVersion($module . '_setup')) {
            $dbVersion = Mage::getModel('core/mysql4_resource')->getDbVersion($module . '_setup');
        } else {
            $dbVersion = 'Not Available';
        }

        //Here we get data version of the given module code
        if (Mage::getModel('core/mysql4_resource')->getDataVersion($module . '_setup')) {
            $dataVersion = Mage::getModel('core/mysql4_resource')->getDataVersion($module . '_setup');
        } else {
            $dataVersion = 'Not Available';
        }
        //Here we get configuration version of the given module name
        $moduleName = $this->getRequest()->getParam('moduleName');
        $configVersion = (string)Mage::getConfig()->getModuleConfig($moduleName)->version;
        $selftester->messages = array_merge(
            array(
                'Config Version: ' . $configVersion,
                'DB Version: ' . $dbVersion,
                'Data Version: ' . $dataVersion,
            ), $selftester->messages
        );

        if (!$selftester->errorOccurred) {
            Mage::getSingleton('core/session')->addSuccess(implode("<br/>", $selftester->messages));
        } else {
            Mage::getSingleton('core/session')->addError(implode("<br/>", $selftester->messages));
        }
        //Here we load appropriate layout. In our case its popup
        $layout = $this->getLayout();
        $layout->getUpdate()->load('selftest_popup');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
            
        $this->getResponse()->setBody($output);
        //Here we clear all the messages of the current session, because otherwise we will get a number
        //of duplicates from the previous page loads.
        Mage::getSingleton('core/session')->getMesssages(true);
    }
}