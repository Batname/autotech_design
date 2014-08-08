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
 * @package    AW_Zblocks
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Zblocks_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
     * Returns array of schedule patterns
     * @return array As 'pattern code' => 'pattern name'
     */
    public function getPatternsToOptionsArray()
    {
        return array(
            'every day'     => $this->__('Every day'),
            'odd days'      => $this->__('Odd days of the month'),
            'even days'     => $this->__('Even days of the month'),
            '1'             => $this->__('On 1'),
            '2'             => $this->__('On 2'),
            '3'             => $this->__('On 3'),
            '4'             => $this->__('On 4'),
            '5'             => $this->__('On 5'),
            '6'             => $this->__('On 6'),
            '7'             => $this->__('On 7'),
            '8'             => $this->__('On 8'),
            '9'             => $this->__('On 9'),
            '10'            => $this->__('On 10'),
            '11'            => $this->__('On 11'),
            '12'            => $this->__('On 12'),
            '13'            => $this->__('On 13'),
            '14'            => $this->__('On 14'),
            '15'            => $this->__('On 15'),
            '16'            => $this->__('On 16'),
            '17'            => $this->__('On 17'),
            '18'            => $this->__('On 18'),
            '19'            => $this->__('On 19'),
            '20'            => $this->__('On 20'),
            '21'            => $this->__('On 21'),
            '22'            => $this->__('On 22'),
            '23'            => $this->__('On 23'),
            '24'            => $this->__('On 24'),
            '25'            => $this->__('On 25'),
            '26'            => $this->__('On 26'),
            '27'            => $this->__('On 27'),
            '28'            => $this->__('On 28'),
            '29'            => $this->__('On 29'),
            '30'            => $this->__('On 30'),
            '31'            => $this->__('On 31'),
            '1,11,21'       => $this->__('On 1, 11, and 21st of the month'),
            '1,11,21,31'    => $this->__('On 1, 11, 21, and 31st of the month'),
            '10,20,30'      => $this->__('On 10, 20, and 30th of the month'),
            'su'            => $this->__('On Sundays'),
            'mo'            => $this->__('On Mondays'),
            'tu'            => $this->__('On Tuesdays'),
            'we'            => $this->__('On Wednesdays'),
            'th'            => $this->__('On Thursdays'),
            'fr'            => $this->__('On Fridays'),
            'sa'            => $this->__('On Saturdays'),
            'mon-fri'       => $this->__('From Monday to Friday'),
            'sat-sun'       => $this->__('On Saturdays and Sundays'),
            'tue-fri'       => $this->__('From Tuesday to Friday'),
            'mon-sat'       => $this->__('From Monday to Saturday'),
            'mon,wed,fri'   => $this->__('On Mondays, Wednesdays, and Fridays'),
            'tue,thu,sat'   => $this->__('On Tuesdays, Thursdays, and Saturdays'),
        );
    }

    /*
     * Needed to display block position select in backend
     * @return array Returns multi-level array containin block groups and key => value pairs with 'label' and 'value' keys
     */
    public function getBlockIdsToOptionsArray()
    {   return array(
            array(
                'label' => $this->__('Default for using in CMS page template'),
                'value' => array(
                    array('value' => 'custom',              'label' => $this->__('Custom')),
                )),
            array(
                'label' => $this->__('General (will be disaplyed on all pages)'),
                'value' => array(
                    array('value' => 'sidebar-right-top',   'label' => $this->__('Sidebar right top')),
                    array('value' => 'sidebar-right-bottom','label' => $this->__('Sidebar right bottom')),
                    array('value' => 'sidebar-left-top',    'label' => $this->__('Sidebar left top')),
                    array('value' => 'sidebar-left-bottom', 'label' => $this->__('Sidebar left bottom')),
                    array('value' => 'content-top',         'label' => $this->__('Content top')),
                    array('value' => 'menu-top',            'label' => $this->__('Menu top')),
                    array('value' => 'menu-bottom',         'label' => $this->__('Menu bottom')),
                    array('value' => 'page-bottom',         'label' => $this->__('Page bottom')),
                )),
            array(
                'label' => $this->__('Catalog and product'),
                'value' => array(
                    array('value' => 'catalog-sidebar-right-top',   'label' => $this->__('Catalog Sidebar Right Top')),
                    array('value' => 'catalog-sidebar-right-bottom','label' => $this->__('Catalog Sidebar Right Bottom')),
                    array('value' => 'catalog-sidebar-left-top',    'label' => $this->__('Catalog Sidebar Left Top')),
                    array('value' => 'catalog-sidebar-left-bottom', 'label' => $this->__('Catalog Sidebar Left Bottom')),
                    array('value' => 'catalog-content-top',         'label' => $this->__('Catalog Content Top')),
                    array('value' => 'catalog-menu-top',            'label' => $this->__('Catalog Menu Top')),
                    array('value' => 'catalog-menu-bottom',         'label' => $this->__('Catalog Menu Bottom')),
                    array('value' => 'catalog-page-bottom',         'label' => $this->__('Catalog Page Bottom')),
                )),
            array(
                'label' => $this->__('Product only'),
                'value' => array(
                    array('value' => 'product-sidebar-right-top',   'label' => $this->__('Product Sidebar Right Top')),
                    array('value' => 'product-sidebar-right-bottom','label' => $this->__('Product Sidebar Right Bottom')),
                    array('value' => 'product-sidebar-left-top',    'label' => $this->__('Product Sidebar Left Top')),
                    array('value' => 'product-sidebar-left-bottom', 'label' => $this->__('Product Sidebar Left Bottom')),
                    array('value' => 'product-content-top',         'label' => $this->__('Product Content Top')),
                    array('value' => 'product-menu-top',            'label' => $this->__('Product Menu Top')),
                    array('value' => 'product-menu-bottom',         'label' => $this->__('Product Menu Bottom')),
                    array('value' => 'product-page-bottom',         'label' => $this->__('Product Page Bottom')),
                )),
            array(
                'label' => $this->__('Customer'),
                'value' => array(
                    array('value' => 'customer-content-top',        'label' => $this->__('Customer Content Top')),
                )),
            array(
                'label' => $this->__('Cart & Checkout'),
                'value' => array(
                    array('value' => 'cart-content-top',            'label' => $this->__('Cart Content Top')),
                    array('value' => 'checkout-content-top',        'label' => $this->__('Checkout Content Top')),
                )),
        );
    }

    /*
     * Returns array of block content items rotation modes
     * @return array
     */
    public function getRotatorModesToOptionsArray()
    {
        return array(
            0 => $this->__('Show all'),
            1 => $this->__('Rotate one by one'),
            2 => $this->__('Show random'),
        );
    }

    /*
     * Returns total quantity of seconds within time period given
     * @param int $hours Quantity of hours
     * @param int $minutes Quantity of minutes
     * @param int $seconds Quantity of seconds
     * @return int Quantity of seconds
     */
    protected function _getDaySeconds($hours, $minutes, $seconds)
    {
        return $hours*3600 + $minutes*60 + $seconds;
    }

    /*
     * Checks whether the block is valid to be displayed
     * @param array $data Block data
     * @return bool Block visibility
     */
    protected function _isValid($data)
    {
        // MSS check

        if( Mage::helper('zblocks')->isMSSInstalled()
        &&  isset($data['mss_rule_id'])
        &&  $mssRuleId = $data['mss_rule_id']
        ) {
            $object = false;

            $session = Mage::getSingleton('customer/session');
            if($session->isLoggedIn())
                $object = $session->getCustomer();
            else
                $object = Mage::getSingleton('checkout/session')->getQuote();

            if(!Mage::getModel('marketsuite/filter')->checkRule($object, $mssRuleId))
                return false;
        }

        // time check

        $timestamp = Mage::getModel('core/date')->timestamp();
        $time = getdate($timestamp);

        $daySecondsNow = $this->_getDaySeconds($time['hours'], $time['minutes'], $time['seconds']);
        $today = $timestamp - $daySecondsNow; // in seconds

        if(($data['schedule_from_date'] && $today < strtotime($data['schedule_from_date']))
            ||
           ($data['schedule_to_date'] && $today > 1+strtotime($data['schedule_to_date']))) return false; // add 1 second for _very_ slow servers

        switch ($data['schedule_pattern'])
        {
            case 'every day'   : break;
            case 'odd days'    : if(!($time['mday']%2)) return false; break;
            case 'even days'   : if($time['mday']%2) return false; break;
            case '1'           :
            case '2'           :
            case '3'           :
            case '4'           :
            case '5'           :
            case '6'           :
            case '7'           :
            case '8'           :
            case '9'           :
            case '10'          :
            case '11'          :
            case '12'          :
            case '13'          :
            case '14'          :
            case '15'          :
            case '16'          :
            case '17'          :
            case '18'          :
            case '19'          :
            case '20'          :
            case '21'          :
            case '22'          :
            case '23'          :
            case '24'          :
            case '25'          :
            case '26'          :
            case '27'          :
            case '28'          :
            case '29'          :
            case '30'          :
            case '31'          : if((string)$time['mday'] !== $data['schedule_pattern']) return false; break;
            case '1,11,21'     : if(($time['mday']%10)-1 || $time['mday']=='31') return false; break;
            case '1,11,21,31'  : if(($time['mday']%10)-1) return false; break;
            case '10,20,30'    : if($time['mday']%10) return false; break;
            case 'mo'          :
            case 'tu'          :
            case 'we'          :
            case 'th'          :
            case 'fr'          :
            case 'sa'          :
            case 'su'          : if(strtolower(substr($time['weekday'], 0, 2)) !== $data['schedule_pattern']) return false; break;
            case 'mon-fri'     : if(!$time['wday'] || $time['wday'] == 6) return false; break;
            case 'sat-sun'     : if($time['wday'] || $time['wday'] !== 6) return false; break;
            case 'tue-fri'     : if($time['wday'] < 2 || $time['wday'] > 5) return false; break;
            case 'mon-sat'     : if(!$time['wday']) return false; break;
            case 'mon,wed,fri' : if($time['wday'] !== 1 && $time['wday'] !== 3 && $time['wday'] !== 5) return false; break;
            case 'tue,thu,sat' : if($time['wday'] !== 2 && $time['wday'] !== 4 && $time['wday'] !== 6) return false; break;
            default : return false; // unknown pattern? that's incredible!!!
        }

        if($data['schedule_from_time'])
        {
            $time = date_parse($data['schedule_from_time']);
            $timeFrom = $this->_getDaySeconds($time['hour'], $time['minute'], $time['second']);
        }
        else $timeFrom = 0;

        if($data['schedule_to_time'])
        {
            $time = date_parse($data['schedule_to_time']);
            $timeTo = $this->_getDaySeconds($time['hour'], $time['minute'], $time['second']);
        }
        else $timeTo = 0;

        if ($timeFrom && $timeTo)
            if($timeFrom < $timeTo) {
                if ($daySecondsNow < $timeFrom || $daySecondsNow > $timeTo) return false;
            }
            else {
                if (!($daySecondsNow > $timeFrom || $daySecondsNow < $timeTo)) return false;
            }
        else {
            if ($timeFrom && $daySecondsNow < $timeFrom) return false;
            if ($timeTo && $daySecondsNow > $timeTo) return false;
        }

        return true;
    }

    /*
     * Returns content of the blocks to display on the position specified
     * @param string $customPosition Custom block position name
     * @param string $blockPosition Pre-defined block position name
     * @param string $categoryPath Product category path
     * @param string $currentCategoryId The ID of the current category
     * @return array Blocks content to be displayed
     */
    public function getBlocks($customPosition, $blockPosition, $categoryPath, $currentCategoryId)
    {
        $blocks=array();

        $collection = Mage::getResourceModel('zblocks/zblocks_collection');

        $collection->getSelect()
            ->where('block_is_active=1')
            ->where('find_in_set(0, store_ids) OR find_in_set(?, store_ids)', (int)(Mage::app()->getStore()->getId()))
            ->order('block_sort_order');

        if($customPosition)
            $collection->getSelect()->where('block_position_custom=?', $customPosition);
        elseif($blockPosition)
            $collection->getSelect()->where('block_position=?', $blockPosition);
        else return $blocks; // neither blockId nor block_position specified, illegal call probably from CMS page layout

        if(count($categoryPath)) // if it's category or product page
        {
            $categorySelect = '(show_in_subcategories=0 and find_in_set('.(string)$currentCategoryId.', category_ids)) OR (';

                $categorySelect .= "show_in_subcategories=1 AND (category_ids='' OR ";

            foreach($categoryPath as $c)
                $categorySelect .= 'find_in_set('.$c.', category_ids) OR ';

            $categorySelect = substr($categorySelect, 0, strlen($categorySelect)-4);
            $categorySelect .= '))';

            $collection->getSelect()->where($categorySelect);
        }
        else $collection->getSelect()->where('category_ids=\'\'');

        $collection->load();

        foreach($collection->getItems() as $item)
        {
            if(!$this->_isValid($item->getData())) continue;

            $contentColl = Mage::getResourceModel('zblocks/content_collection');
            $contentColl->getSelect()
                            ->from('', array())
                            ->where('zblock_id=?', $item->getZblockId())
                            ->where('is_active=?', 1)
                            ->order('sort_order');
            $contentColl->load();

            switch ($item->getRotatorMode())
            {
                case 0: //all
                        foreach($contentColl->getItems() as $v)
                            $blocks[] = $v->getContent();
                        break;

                case 1: // one by one
                        $session = Mage::getSingleton('core/session');
                        $session->start();
                        if(!$oneByOneBlockIds = $session->getZBlocksOneByOneBlockIds())
                            $oneByOneBlockIds = array();

                        $zblockId = $item->getZblockId();

                        $takeFirstBlock = true;
                        if(array_key_exists($zblockId, $oneByOneBlockIds))
                        {
                            $currentBlockId = $oneByOneBlockIds[$zblockId];
                            $foundCurrent = false;
                            foreach($contentColl->getItems() as $k => $v)
                                if($foundCurrent)
                                {    // if we are here then the current block is not at the end of the collection
                                    $takeFirstBlock = false; // so, we don't have to take first block
                                    break;
                                }
                                elseif($k == $currentBlockId) $foundCurrent = true;
                        }
                        if($takeFirstBlock)
                            foreach($contentColl->getItems() as $k => $v) break; //we need only first record

                        if(isset($k))
                        {
                            $oneByOneBlockIds[$zblockId] = $k;
                            $session->setZBlocksOneByOneBlockIds($oneByOneBlockIds);
                            $blocks[] = $v->getContent();
                        }
                        break;

                case 2: // random
                        if(!$size = $contentColl->getSize()) break;
                        $randomBlock = rand(1, $size);
                        $i = 1;
                        foreach($contentColl->getItems() as $v)
                            if($i == $randomBlock)
                            {
                                $blocks[] = $v->getContent();
                                break;
                            }
                            else $i++;
                        break;

                default: // none - we take only first block to show
                        foreach($contentColl->getItems() as $v)
                        {
                            $blocks[] = $v->getContent();
                            break; //we need only first record
                        }
            }
        }
        $processor = Mage::getModel('core/email_template_filter');
        //var_dump($customPosition);
        //var_dump($blockPosition);
        //die;
        foreach($blocks as $k => $v)
        {
            $blocks[$k] = $processor->filter($v);
            $CssClass = "zblock zblock-".$blockPosition;
            $blocks[$k] = '<div class="'.$CssClass.'">'.$blocks[$k].'</div>';
        }

        return $blocks;
    }

    /*
     * Returns true if module output is disabled from the admin section
     * @return bool
     */
    public static function isModuleOutputDisabled()
    {
        return (bool) Mage::getStoreConfig('advanced/modules_disable_output/AW_ZBlocks');
    }

    /*
     * Returns true if Market Segmentation Suite by aheadWorks is installed and active
     * @return bool
     */
    public static function isMSSInstalled()
    {
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        return      array_key_exists('AW_Marketsuite', $modules)
                &&  'true' == (string) $modules['AW_Marketsuite']->active;
    }

    public static function recursiveReplace($search, $replace, $subject)
    {
        if(!is_array($subject))
            return $subject;

        foreach($subject as $key => $value)
            if(is_string($value))
                $subject[$key] = str_replace($search, $replace, $value);
            elseif(is_array($value))
                $subject[$key] = self::recursiveReplace($search, $replace, $value);

        return $subject;
    }


}