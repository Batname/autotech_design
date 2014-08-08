<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Advancedreports
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */?>
<?php
class AW_Advancedreports_Additional_ReportController extends Mage_Adminhtml_Controller_Action
{	
	protected function _initAddName()
	{
		if (!$this->_getName()){
			Mage::register('aw_advancedreports_additional_name', $this->getRequest()->getParam('name'));	
		}			
		return $this;
	}
	
	protected function _getName()
	{
		return Mage::registry('aw_advancedreports_additional_name');
	}

    public function indexAction()
    {    					
        $this->_initAddName()
			->loadLayout()
            ->_setActiveMenu('report/advancedreports/'.$this->_getName())
            ->_addBreadcrumb( Mage::helper('advancedreports')->__('Advanced'), Mage::helper('advancedreports')->__('Advanced') )
            ->_addBreadcrumb( Mage::helper('advancedreports')->__( Mage::helper('advancedreports/additional')->getReports()->getTitle( $this->_getName() ) ), 
							  Mage::helper('advancedreports')->__( Mage::helper('advancedreports/additional')->getReports()->getTitle( $this->_getName() ) ))
            ->_addContent( $this->getLayout()->createBlock('advancedreports/additional_'.$this->_getName()) )
            ->renderLayout();			
    }

    public function exportOrderedCsvAction()
    {        
		$this->_initAddName();
		$fileName   = $this->_getName().'.csv';	
        $content    = $this->getLayout()->createBlock('advancedreports/additional_'.$this->_getName().'_grid')
            ->getCsv();		
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportOrderedExcelAction()
    {
        $this->_initAddName();
		$fileName   = $this->_getName().'.xml';
        $content    = $this->getLayout()->createBlock('advancedreports/additional_'.$this->_getName().'_grid')
            ->getExcel($fileName);		
        $this->_prepareDownloadResponse($fileName, $content);
    }
	
	public function gridAction()
	{
        $this->_initAddName();
		$this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('advancedreports/additional_'.$this->_getName().'_grid')->toHtml()
        );
	}	


}