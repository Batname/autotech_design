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
class AW_Advancedreports_Model_Mysql4_Ordered_Collection extends Mage_Reports_Model_Mysql4_Product_Ordered_Collection
{
    //It fix magento sstore filter bug 8p
    public function setStoreIds($storeIds)
    {
        $storeIds = array_values( $storeIds );
        if ($this->getAwStoreIds($storeIds))
        {
            $this->getSelect()->where('order.store_id in (?)', $this->getAwStoreIds($storeIds));
        }
        return parent::setStoreIds($storeIds);
    }

    public function getAwStoreIds($ids = array())
    {
        if (count($ids))
        {
            $res = '';
            $is_first = true;
            foreach ($ids as $id)
            {
                $res .= $is_first ? $id : ",".$id;
                $is_first = false;
            }
            return $res;
        }
        return '';
    }

    public function addOrderedQty($from = '', $to = '')
    {
        $qtyOrderedTableName = $this->getTable('sales/order_item');
        $qtyOrderedFieldName = 'qty_ordered';

        $productIdTableName = $this->getTable('sales/order_item');
        $productIdFieldName = 'product_id';

//        $productTypes = " AND (e.type_id = '" .
//            Mage_Catalog_Model_Product_Type::TYPE_SIMPLE .
//            "' OR e.type_id = '" . Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL  .
//	    "' OR e.type_id = '" . Mage_Catalog_Model_Product_Type::TYPE_BUNDLE ."' )";
        $productTypes = " AND (e.type_id <> '" . Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE . "' )";

        if ($from != '' && $to != '') {
            $dateFilter = " AND `order`.created_at BETWEEN '{$from}' AND '{$to}'";
        } else {
            $dateFilter = "";
        }

        $this->getSelect()->reset()
            ->from(
                array('order_items' => $qtyOrderedTableName),
                array('ordered_qty' => "SUM(order_items.{$qtyOrderedFieldName})"))
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                'order.entity_id = order_items.order_id'.$dateFilter,
                array())
            ->joinInner(array('e' => $this->getProductEntityTableName()),
                "e.entity_id = order_items.{$productIdFieldName} AND e.entity_type_id = {$this->getProductEntityTypeId()}{$productTypes}")
//                "e.entity_id = order_items.{$productIdFieldName}")
            ->group('e.entity_id')
            ->having('ordered_qty > 0');

        return $this;
    }

}
