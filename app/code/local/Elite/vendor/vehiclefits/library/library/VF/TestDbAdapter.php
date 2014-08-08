<?php
/**
 * Test only DB Adapter for emulating transaction nesting in MYSQL
 * Vehicle Fits
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@vehiclefits.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Vehicle Fits to newer
 * versions in the future. If you wish to customize Vehicle Fits for your
 * needs please refer to http://www.vehiclefits.com for more information.
 * @copyright  Copyright (c) 2013 Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_TestDbAdapter extends Zend_Db_Adapter_Pdo_Mysql
{

    /**
     * Keeps track of transaction nest level, to emulate mysql support, -1 meaning no transaction
     * has begun, 0 meaning there is no nesting, 1 meaning there are 2 transactions, ad infintum
     *
     * @var integer
     */
    public $_transaction_depth = -1;
    protected $_should_emulate_nesting = true;

    function beginTransaction()
    {
        $this->_transaction_depth++;
        if ($this->_transaction_depth > 0) {
            return;
        }
        return parent::beginTransaction();
    }

    function commit()
    {
        $this->_transaction_depth--;
        if ($this->shouldEmulateNesting()) {
            return;
        }
        return parent::commit();
    }

    function rollBack()
    {
        $this->_transaction_depth--;
        if ($this->shouldEmulateNesting()) {
            return;
        }
        return parent::rollBack();
    }

    protected function shouldEmulateNesting()
    {
        return $this->_should_emulate_nesting && $this->isNested();
    }

    protected function isNested()
    {
        return $this->_transaction_depth >= 0;
    }

    function __call($methodName, $arguments)
    {
        $method = array($this->wrapped, $methodName);
        return call_user_func_array($method, $arguments);
    }
}