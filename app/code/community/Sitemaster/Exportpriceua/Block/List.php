<?php

class Sitemaster_Exportpriceua_Block_List extends Mage_Adminhtml_Block_Widget
{
	
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sitemaster/exportpriceua/list.phtml');
    }
}
