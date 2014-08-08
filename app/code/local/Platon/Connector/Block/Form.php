<?php

class Platon_Connector_Block_Form extends Mage_Payment_Block_Form {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('platon/form/form.phtml');
    }

}

?> 