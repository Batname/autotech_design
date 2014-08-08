<?php

class Sitemaster_Exportyandexua_Block_List extends Mage_Adminhtml_Block_Widget
{
	
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sitemaster/exportyandexua/list.phtml');
    }
}
