<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class VF_FlexibleSearch_Wrapper
{
    /** @var VF_FlexibleSearch */
    protected $wrappedFlexibleSearch;

    function __construct(VF_FlexibleSearch_Interface $flexibleSearchToWrap)
    {
        $this->wrappedFlexibleSearch = $flexibleSearchToWrap;
    }

    function getParam($param)
    {
        if (is_numeric($this->getRequest()->getParam($param))) {
            return $this->getRequest()->getParam($param);
        }
        if (isset($_SESSION[$param])) {
            return $_SESSION[$param];
        }
    }

    function __call($methodName, $arguments)
    {
        $method = array($this->wrappedFlexibleSearch, $methodName);
        return call_user_func_array($method, $arguments);
    }

    /** @return Zend_Db_Statement_Interface */
    protected function query($sql)
    {
        return $this->getReadAdapter()->query($sql);
    }

    /** @return Zend_Db_Adapter_Abstract */
    protected function getReadAdapter()
    {
        return VF_Singleton::getInstance()->getReadAdapter();
    }
}