<?php
class AW_Advancedreports_Model_Admin_Config extends Mage_Admin_Model_Config
{
	public function __construct()
	{
		parent::__construct();		
	}

    public function loadAclResources(Mage_Admin_Model_Acl $acl, $resource=null, $parentName=null)
    {					
		parent::loadAclResources($acl, $resource, $parentName);
		if ( $acl && ($parentName == 'admin/report') && ($resource->getName() == 'advancedreports') ){
			Varien_Profiler::start('aw::advancedreports::load_acl_resources');
			$reports = Mage::getModel('advancedreports/additional_reports');
			if ($reports->getCount())
			{
				foreach ($reports->getReports() as $item){
					$acl_resource = Mage::getModel('admin/acl_resource', $parentName.'/'.$resource->getName().'/'.$item->getName());
					if (!$acl->has($acl_resource)){
						$acl->add($acl_resource, $parentName.'/'.$resource->getName());
					}
				}
			}
			Varien_Profiler::stop('aw::advancedreports::load_acl_resources');
		}
		return $this;
    }






}


