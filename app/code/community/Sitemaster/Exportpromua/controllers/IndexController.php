<?php

class Sitemaster_Exportpromua_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog/exportpromua');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('exportpromua/list'));
        $this->renderLayout();
    }


    public function exportPostPromUaAction()
    {

        //check for selected categories
        if (!isset($_POST['cats'])) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('exportpromua')->__('Not selected Categories'));
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
                'name' => $category->getName(),
                'parentname' =>  $category->getParentCategory()->getName(),  //http://www.magentocommerce.com/boards/viewthread/19476/

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

                        if ($p->getData('manufacturer')) {
                            $brand = $p->getAttributeText('manufacturer');
                        } else {
                            $brand = '';
                        }


                        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($p)->getQty(); // get available="true"
                        if ($stock > 0) {
                            $stock = "true";
                        }

                        $currencyid = Mage::app()->getStore()->getCurrentCurrencyCode();


                        if ($p->getData('pickup')) {
                            $pickup = $p->getAttributeText('pickup');
                        } else {
                            $pickup = '';
                        }


                        if ($p->getData('delivery')) {
                            $delivery = $p->getAttributeText('delivery');
                        } else {
                            $delivery = '';
                        }



                        $productsArray[] = array(
                            'cat_ids' => $c,
                            'product_id' => $p->getData('entity_id'),
                            'product_name' => $p->getData('name'),
                            'product_description' => $p->getData('description'),
                            'product_url' => Mage::getUrl($p->getUrlPath()),
                            'product_image' => Mage::getBaseUrl('media') . 'catalog/product' . $p->getImage(),
                            'product_brand' => $brand,
                            'product_stock' => $stock, // Mage::getModel('cataloginventory/stock_item')->loadByProduct($p)->getIsInStock(), // get stock getQty()  getIsInStock()
                            'product_price' => $price,
                            'product_currency' => $currencyid,
                            'product_pickup' => $pickup,
                            'product_delivery' => $delivery
                        );

                        unset($price, $brand);


                    }

                    unset($products, $p, $category);
                }
            }
        }


        //name of store
        $site_name = Mage::getStoreConfig('exportpromua/settings/storename', Mage::app()->getStore());
        $shop_name = Mage::getStoreConfig('exportpromua/settings/shopname', Mage::app()->getStore());

        // Limit output
        //$_productCollection->getSelect()->limit(50);

        $i = 1;
// Creates an instance of the DOMImplementation class
        $imp = new DOMImplementation;

// Creates a DOMDocumentType instance
        $dtd = $imp->createDocumentType('yml_catalog', '', 'shops.dtd');

// Creates a DOMDocument instance
        $dom = $imp->createDocument("", "", $dtd);

// Set other properties
        $dom->encoding = 'utf-8';
    //    $dom->standalone = false;


        // Create an empty element
        $root = $dom->createElement('yml_catalog');
        $root->setAttribute("date", strftime("%Y-%m-%d %H:%M"));

       // Append the element
        $shop = $dom->createElement("shop");
        $shop = $root->appendChild($shop);


        $name_ = $dom->createElement("name");
        $shop->appendChild($name_);
        $value_name = $dom->createTextNode($site_name);
        $name_->appendChild($value_name);

        $company_ = $dom->createElement("company");
        $shop->appendChild($company_);
        $company_name = $dom->createTextNode($shop_name);
        $company_->appendChild($company_name);

        $url_ = $dom->createElement("url");
        $shop->appendChild($url_);
        $url_name = $dom->createTextNode(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
        $url_->appendChild($url_name);

        $currencies = $dom->createElement("currencies"); // create currencies
        $currencies = $shop->appendChild($currencies);

        $currency = $dom->createElement("currency");
        $currency = $currencies->appendChild($currency);
        $currency->setAttribute("id", Mage::app()->getStore()->getCurrentCurrencyCode());
        $currency->setAttribute("rate", Mage::app()->getStore()->getCurrentCurrencyRate());

        $cat = $dom->createElement("categories");
        $cat = $shop->appendChild($cat);

        $items = $dom->createElement("offers");
        $items = $shop->appendChild($items);

        foreach ($productsArray as $_product) {

            $v['url'] = $_product['product_url'];

        //    $v['vendor'] = strip_tags($_product['product_brand']);

            $v['price'] = $_product['product_price'];

            $v['currencyId'] = $_product['product_currency'];

            $v['categoryId'] = $_product['cat_ids'];

            $v['picture'] = $_product['product_image'];

            $v['pickup'] = $_product['product_pickup'];

            $v['delivery'] = $_product['product_delivery'];

            $v['name'] = $_product['product_name'];

            $v['description'] = strip_tags($_product['product_description']);

            if (!empty($_product['product_name']) AND !empty($_product['product_price'])) {
                $occ = $dom->createElement('offer');
                $occ = $items->appendChild($occ);
                $occ->setAttribute("available", $_product['product_stock']); // get available
                $occ->setAttribute("id", $_product['product_id']);



                foreach ($v as $fieldName => $fieldValue) {
                    $child = $dom->createElement($fieldName);
                    $child = $occ->appendChild($child);
                    $value = $dom->createTextNode($fieldValue);
                    $child->appendChild($value);
                }
            }


            $i++;
        }


        //create category section
        foreach ($categoriesArray as $c) {
            //$ctg = $doc->createElement('category');
            //$ctg = $cat->appendChild($ctg);
            //$ctg->setAttribute("parentId", $c['parentId']);
            //$value = $doc->createTextNode($c['name']);
            //$ctg->appendChild($value);

            $ctg = $dom->createElement('category');
            $ctg = $cat->appendChild($ctg);
            $ctg->setAttribute("id", $c['parentId']); // node category
           // $ctg->setAttribute("parentId", $c['parentId']); // parent
            $value = $dom->createTextNode($c['parentname']);
            $ctg->appendChild($value);

            $ctg = $dom->createElement('category');
            $ctg = $cat->appendChild($ctg);
            $ctg->setAttribute("id", $c['id']); // node category
            $ctg->setAttribute("parentId", $c['parentId']); // parent
            $value = $dom->createTextNode($c['name']);
            $ctg->appendChild($value);
        }


        $root = $dom->appendChild($root);


        $dom->formatOutput = true;

        header('Content-type: text/xml', true);
        header('Content-Disposition:  attachment; filename="prom-ua.xml"');


        echo $dom->saveXML();

        //sleep(10);

    }


}

