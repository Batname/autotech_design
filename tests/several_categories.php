<?php
header('Content-Type: text/html; charset=utf-8');
require_once '../app/Mage.php';
Mage::app();

//$categories = array(52,55);
//
//$collection = mage::getModel('catalog/product')->getCollection()
//    ->addAttributeToSelect('*')
//    ->joinField('category_id',
//        'catalog/category_product',
//        'category_id',
//        'product_id=entity_id',
//        null,
//        'left')
//    ->addAttributeToFilter('category_id', array('in' => $categories));
//$collection->getSelect()->group('e.entity_id');
//
//$i = 1;
//foreach ($collection as $product) {
//    var_dump($i ." ". $product->getName());
//    $i++;
//}


$categories = array(10,13); // category ids
$productIds = array();
$adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
$select = $adapter->select()
    ->from('catalog_category_product', 'catalog_category_product.product_id')
->where('catalog_category_product.category_id IN (?)', $categories)
->group('catalog_category_product.product_id');
$productIds = $adapter->fetchAll($select);
$product = Mage::getModel('catalog/product');
$collection = $product->getCollection()
    ->addAttributeToFilter('entity_id', array('in'=>$productIds));

print_r($collection);

$i = 1;
foreach ($collection as $product) {
    var_dump($i ." ". $product);
    $i++;
}




echo 'done';