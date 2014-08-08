<?php

class Platon_Connector_Block_Redirect extends Mage_Core_Block_Abstract
{
    /**
     * Generate redirect form 
     * @return string
     */
    
    protected function _toHtml()
    {
        $platon = Mage::getModel('platon/main');

        $form = new Varien_Data_Form();
        $form->setAction($platon->getGatewayUrl())
            ->setId('platon_checkout')
            ->setName('platon_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($platon->getFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to the Platon website in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("platon_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}
