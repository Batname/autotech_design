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
class AW_Advancedreports_ChartController extends Mage_Adminhtml_Controller_Action
{
    public function ajaxBlockAction()
    {
        $output = '';
        $width  = $this->getRequest()->getParam('width');
        $block  = $this->getRequest()->getParam('block');
        $option = $this->getRequest()->getParam('option');
        $type   = $this->getRequest()->getParam('type');
        $output = $this->getLayout()->createBlock( 'advancedreports/chart' )
                       ->setType( $type )
                       ->setOption( $option )
                       ->setWidth( $width )
                       ->setRouteOption( $block )
                       ->setHeight( Mage::helper('advancedreports')->getChartHeight() )
                       ->toHtml();
        $this->getResponse()->setBody($output);
        return;
    }
}
