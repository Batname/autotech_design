<?php

class Sitemaster_Exportvceneua_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog/exportvceneua');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('exportvceneua/list'));
        $this->renderLayout();
    }


    public function exportPostVceneUaAction()
    {

        //check for selected categories
        if(!isset($_POST['cats'])) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('exportvceneua')->__('Not selected Categories'));
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
        foreach($post_cats as $c) {



            $category = Mage::getModel('catalog/category')->load($c);

            //add to category array
            $categoriesArray[] = array(
                'id'=>$category->getId(),
                'parentId' => $category->getParentId(), // parent id
                'name'=>$category->getName(),

            );


            $products = Mage::getModel('catalog/product')
                ->getCollection()
                ->addCategoryFilter($category)
                ->addAttributeToSelect('*')
                ->load();

            foreach($products as $p) {

                if($p->isAvailable()) {
                    //var_dump($p->getData('name'));


                    //check for unique products
                    if (!in_array($p->getData('entity_id'), $prodArrayCheck)) {

                        $prodArrayCheck[] = $p->getData('entity_id');

                        $p = Mage::getModel('catalog/product')->load($p->getData('entity_id'));

                        if($p->getFinalPrice()) {
                            $price = number_format($p->getFinalPrice(), 2, '.',',');
                        } else {
                            $price = number_format($p->getPrice(), 2, '.','');
                        }

                        if($p->getData('manufacturer')) {
                            $brand = $p->getAttributeText('manufacturer');
                        } else {
                            $brand = '';
                        }


                        $productsArray[] = array(
                            'cat_ids'=>$c,
                            'product_id'=>$p->getData('entity_id'),
                            'product_name'=>$p->getData('name'),
                            'product_description'=>$p->getData('description'),
                            'product_url'=>Mage::getUrl($p->getUrlPath()),
                            'product_image'=>Mage::getBaseUrl('media').'catalog/product'.$p->getImage(),
                            'product_brand'=>$brand,
                            'product_price'=>$price
                        );

                        unset($price, $brand);


                    }

                    unset($products, $p, $category);
                }
            }
        }




        //name of store
        $site_name = Mage::getStoreConfig('exportvceneua/settings/storename',Mage::app()->getStore());;
        $shop_name = Mage::getStoreConfig('exportvceneua/settings/shopname',Mage::app()->getStore());;



        // Limit output
        //$_productCollection->getSelect()->limit(50);

        $i = 1;
        $doc = new DomDocument("1.0", "utf-8");
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $root = $doc->createElement('yml_catalog');
        $root->setAttribute("date", strftime("%Y-%m-%d %H:%M"));

        $shop = $doc->createElement("shop");  // create child
        $shop = $root->appendChild($shop);


        $name_ = $doc->createElement("name");
        $shop->appendChild($name_);
        $value_name = $doc->createTextNode($site_name);
        $name_->appendChild($value_name);

        $company_ = $doc->createElement("company");
        $shop->appendChild($company_);
        $company_name = $doc->createTextNode($shop_name);
        $company_->appendChild($company_name);

//      Get store Url
		$url_ = $doc->createElement("url");
        $shop->appendChild($url_);
		$url_name = $doc->createTextNode(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
		$url_->appendChild($url_name);

        $currency = $doc->createElement("currency");
        $shop->appendChild($currency);
        $currency->setAttribute("id", Mage::app()->getStore()->getCurrentCurrencyCode());

        $cat = $doc->createElement("categories");
        $cat = $shop->appendChild($cat);

        $items = $doc->createElement("offers");
        $items = $shop->appendChild($items);



        foreach ($productsArray as $_product) {

            $v['url'] = $_product['product_url'];

            $v['vendor'] = strip_tags($_product['product_brand']);

            $v['price'] = $_product['product_price'];

            $v['categoryId'] = $_product['cat_ids'];

            $v['picture'] = $_product['product_image'];

            $v['name'] = $_product['product_name'];

            $v['description'] = strip_tags($_product['product_description']);


            if(!empty($_product['product_name']) AND !empty($_product['product_price'])){
                $occ = $doc->createElement('offer');
                $occ = $items->appendChild($occ);
                $occ->setAttribute("id", $_product['product_id']);


                foreach ( $v as $fieldName => $fieldValue ) {
                    $child = $doc->createElement($fieldName);
                    $child = $occ->appendChild($child);
                    $value = $doc->createTextNode($fieldValue);
                    $child->appendChild($value);
                }
            }


            $i++;
        }


        //create category section
        foreach($categoriesArray as $c){
            //$ctg = $doc->createElement('category');
            //$ctg = $cat->appendChild($ctg);
            //$ctg->setAttribute("parentId", $c['parentId']);
            //$value = $doc->createTextNode($c['name']);
            //$ctg->appendChild($value);

            $ctg = $doc->createElement('category');
            $ctg = $cat->appendChild($ctg);
            $ctg->setAttribute("id", $c['id']);  // node category
            $ctg->setAttribute("parentId", $c['parentId']);  // parent
            $value = $doc->createTextNode($c['name']);
            $ctg->appendChild($value);
        }




        $root = $doc->appendChild($root);


        $doc->formatOutput = true;

        header('Content-type: text/xml',true);
        header('Content-Disposition:  attachment; filename="vcene-ua.xml"');

        echo $doc->saveXML();

        //sleep(10);

    }


}

