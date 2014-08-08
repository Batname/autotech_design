<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * System config image field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Wavethemes_Jmbasetheme_Model_Backend_Image extends Mage_Adminhtml_Model_System_Config_Backend_File
{
    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array('jpg', 'jpeg', 'gif', 'png');
    }
    
     /**
     * Return the root part of directory path for uploading
     *
     * @var string
     * @return string
     */
     protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']){
            $uploadDir = $this->_getUploadDir();
            try {
                $file = array();
                $tmpName = $_FILES['groups']['tmp_name'];
                $file['tmp_name'] = $tmpName[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $name = $_FILES['groups']['name'];
                $file['name'] = $name[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $url = getimagesize($uploadDir.DS.$file['name']); //print_r($url); returns an array
                if (is_array($url)) 
                {
                    $fieldConfig = $this->getFieldConfig();
                    Mage::throwException($fieldConfig->label.Mage::helper('core')->__("with same name existed"));
                }else {
                    $uploader = new Mage_Core_Model_File_Uploader($file);
                    $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                    $uploader->setAllowRenameFiles(true);
                    $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                    $result = $uploader->save($uploadDir);
                }
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }
            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }
                $this->setValue($filename);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                $this->setValue('');
            } else {
                $this->unsValue();
            }
        }
        return $this;
    }
    protected function _getUploadRoot($token)
    {
        $groups = Mage::app()->getRequest()->getPost('groups');
        $profile = $groups['jmbasethemegeneral']['fields']['profile']['value'];
        if(!$profile){
            $profile = Mage::getStoreConfig("wavethemes_jmbasetheme/jmbasethemegeneral/profile");
        }
        $cssfolder = Mage::helper("jmbasetheme")->getskinProfileFolder();
        //die($cssfolder.DS.$profile.DS);
        return $cssfolder.DS.$profile.DS;
    }
}
