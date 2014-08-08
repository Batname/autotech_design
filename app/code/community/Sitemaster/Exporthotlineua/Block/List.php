<?php

class Sitemaster_Exporthotlineua_Block_List extends Mage_Adminhtml_Block_Widget
{
	
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sitemaster/exporthotlineua/list.phtml');
    }
}
