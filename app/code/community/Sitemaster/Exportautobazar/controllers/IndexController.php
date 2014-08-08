<?php

class Sitemaster_Exportautobazar_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog/exportautobazar');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('exportautobazar/list'));
        $this->renderLayout();
    }


    public function exportPostAutobazarAction()
    {

        //check for selected categories
        if (!isset($_POST['cats'])) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('exportautobazar')->__('Not selected Categories'));
            $this->_redirect('*/*');
            return;
        }


        //set Frontend mode and Store
        Mage::app()->loadArea(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_EVENTS);
        Mage::app()->setCurrentStore(1);


        $post_cats = $_POST['cats'];

        //var_dump($post_cats);


        //work  with  Categories - for sort by level
        foreach ($post_cats as $pc) {
            $pce = explode('_', $pc);
            $arrayCatLevel[] = $pce[1];
            $arrayCatStr[$pce[1]][] = $pce[0];
        }
        unset($post_cats, $pc, $pce);


        $arrayCatLevel = array_unique($arrayCatLevel);
        rsort($arrayCatLevel);
        //var_dump($arrayCatLevel);
        //var_dump($arrayCatStr);


        foreach ($arrayCatLevel as $ac) {

            //var_dump($arrayCatStr[$ac]);

            foreach ($arrayCatStr[$ac] as $acs) {
                //echo $acs
                $post_cats[] = $acs;
            }

            unset($ac);

        }


        //var_dump($post_cats);


        //loop for array
        $prodArrayCheck = array();
        foreach ($post_cats as $c) {


            $category = Mage::getModel('catalog/category')->load($c);

            //add to category array
            $categoriesArray[] = array(
                'id' => $category->getId(),
                'parentId' => $category->getParentId(), // parent id
                'name' => $category->getName()
            );


            $products = Mage::getModel('catalog/product')
                ->getCollection()
                ->addCategoryFilter($category)
                ->addAttributeToSelect('*')
                ->load();

            foreach ($products as $p) {

                if ($p->isAvailable()) {
                    //var_dump($p->getData('name'));


                    //check for unique products
                    if (!in_array($p->getData('entity_id'), $prodArrayCheck)) {

                        $prodArrayCheck[] = $p->getData('entity_id');

                        $p = Mage::getModel('catalog/product')->load($p->getData('entity_id'));

                        if ($p->getFinalPrice()) {
                            $price = number_format($p->getFinalPrice(), 2, '.', ',');
                        } else {
                            $price = number_format($p->getPrice(), 2, '.', '');
                        }

                        // get attribute for
                       // if ($p->getData('manufacturer')) {
                        //    $brand = $p->getAttributeText('manufacturer');
                        //} else {
                        //    $brand = '';
                       // }

                        $manufacturer = array(
                            "man1" => $p->getAttributeText('manufacturer'),
                            "man2" => $p->getAttributeText('brend_diskov'),
                            "man3" => $p->getAttributeText('brend_shin')
                        );

                        $brand = $manufacturer["man1"]. "" . $manufacturer["man2"]. "" . $manufacturer["man3"];

                        // get attribute guarantee
                        if ($p->getData('guarantee')) {
                            $guarantee = $p->getAttributeText('guarantee');
                        } else {
                            $guarantee = '';
                        }

                        // get attribute term
                        if ($p->getData('term')) {
                            $term = $p->getAttributeText('term');
                        } else {
                            $term = '';
                        }


                        if ($p->getImage() == true) {
                            $imagePath = Mage::getBaseUrl('media') . 'catalog/product' . $p->getImage();
                        } else {
                            $imagePath = '';
                        }

                        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($p)->getQty() ;

                        $productsArray[] = array(
                            'cat_ids' => $c,
                            'product_id' => $p->getData('entity_id'),
                            'product_sku' => $p->getData('sku'),
                            'product_name' => $p->getData('name'),
                            'product_description' => $p->getData('description'),
                            'product_url' => Mage::getUrl($p->getUrlPath()),  //getUrlPath() for html getUrlKey()
                            'product_image' => $imagePath,
                            'product_brand' => $brand,
                            'product_price' => $price,
                            'product_stock' =>  $stock,  // Mage::getModel('cataloginventory/stock_item')->loadByProduct($p)->getIsInStock(), // get stock getQty()  getIsInStock()
                            'product_guarantee' => $guarantee,
                            'product_term' => $term,
                        );

                        unset($price, $brand);


                    }

                    unset($products, $p, $category);
                }
            }
        }


        //name of store
        $site_name = Mage::getStoreConfig('exportautobazar/settings/storename', Mage::app()->getStore());
        $site_id = Mage::getStoreConfig('exportautobazar/settings/storeid', Mage::app()->getStore());


        // Limit output
        //$_productCollection->getSelect()->limit(50);

        $i = 1;
        $doc = new DomDocument("1.0", "utf-8");
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $root = $doc->createElement('price');

        //date
        //$date_ = $doc->createElement("date");
        //$root->appendChild($date_);
        //$value_date = $doc->createTextNode(strftime("%Y-%m-%d %H:%M"));
        //$date_->appendChild($value_date);

        //firmName
        $name_ = $doc->createElement("firmName");
        $root->appendChild($name_);
        $value_name = $doc->createTextNode($site_name);
        $name_->appendChild($value_name);

        //firmId
        $firmId_ = $doc->createElement("firmId");
        $root->appendChild($firmId_);
        $value_firmId = $doc->createTextNode($site_id);
        $firmId_->appendChild($value_firmId);


        // create section categories
//        $cat = $doc->createElement("categories");
//        $cat = $root->appendChild($cat);


        // create section products
        $items = $doc->createElement("items");
        $items = $root->appendChild($items);

        // create products

        foreach ($productsArray as $_product) {

            // $v['id'] = $_product['product_id'];

            // $v['categoryId'] = $_product['cat_ids'];

            $v['code'] = $_product['product_sku']; // get sku product

            $v['vendor'] = strip_tags($_product['product_brand']);

            $v['name'] = $_product['product_name'];

            // $v['description'] = strip_tags($_product['product_description']);

            $v['url'] = $_product['product_url'];

            $v['image'] = $_product['product_image'];

            $v['price_UAH'] = str_replace(",", "", $_product['product_price']);

            $v['stock'] = $_product['product_stock'];

            //$v['guarantee'] = $_product['product_guarantee'];

            $v['term'] = $_product['product_term'];


            if (!empty($_product['product_name']) AND !empty($_product['product_price'])) {
                $occ = $doc->createElement('item');
                $occ = $items->appendChild($occ);
                //$occ->setAttribute("id", $_product['product_id']);


                foreach ($v as $fieldName => $fieldValue) {
                    $child = $doc->createElement($fieldName);
                    $child = $occ->appendChild($child);
                    $value = $doc->createTextNode($fieldValue);
                    $child->appendChild($value);
                }
            }


            $i++;
        }


        // create category section
//        foreach ($categoriesArray as $c) {
//            $ctg = $doc->createElement('category');
//            $ctg = $cat->appendChild($ctg);
//
//
//            // child id
//            $ctgId = $doc->createElement('id');
//            $ctgId = $ctg->appendChild($ctgId);
//            $valuectgId = $doc->createTextNode($c['id']);
//            $ctgId->appendChild($valuectgId);
//
//            //  parent Id
//            $prtId = $doc->createElement('parentId');
//            $prtId = $ctg->appendChild($prtId);
//            $valueprtId = $doc->createTextNode($c['parentId']);
//            $prtId->appendChild($valueprtId);
//
//            $ctgName = $doc->createElement('name');
//            $ctgName = $ctg->appendChild($ctgName);
//            $valuectgName = $doc->createTextNode($c['name']);
//            $ctgName->appendChild($valuectgName);
//        }


        $root = $doc->appendChild($root);

        $doc->formatOutput = true;

        header('Content-type: text/xml', true);
        header('Content-Disposition:  attachment; filename="exportautobazar.xml"');

        echo $doc->saveXML();

        //sleep(10);

    }


}

