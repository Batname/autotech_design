<?php

require_once Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'CartController.php';
class Wavethemes_Jmquickview_IndexController extends Mage_Checkout_CartController {



    public function IndexAction() {
      
	  // Get initial data from request
     
        $productId  = (int) $this->getRequest()->getParam('id');
        //$specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');

        $params = new Varien_Object();
        $params->setCategoryId(false);
        $params->setSpecifyOptions(false);

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
	  
    }
	
	public function addAction() {
	        $response = array();
			$cart   = $this->_getCart();
            $params = $this->getRequest()->getParams();
            try {
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }
 
                $product = $this->_initProduct();
                $related = $this->getRequest()->getParam('related_product');
 
                /**
                 * Check product availability
                 */
                if (!$product) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Unable to find Product ID');
                }
 
                $cart->addProduct($product, $params);
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }
 
                $cart->save();
 
                $this->_getSession()->setCartWasUpdated(true);
 
                /**
                 * @todo remove wishlist observer processAddToCart
                 */
                Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );
 
                if (!$this->_getSession()->getNoCartRedirect(true)) {
                    if (!$cart->getQuote()->getHasError()){
                        $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                        $response['status'] = 'SUCCESS';
                        $response['message'] = $message;
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $msg = "";
                if ($this->_getSession()->getUseNotice(true)) {
                    $msg = $e->getMessage();
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $msg .= $message.'<br/>';
                    }
                }
 
                $response['status'] = 'ERROR';
                $response['message'] = $msg;
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
			
			if(isset($params["onlyadd"])){
               $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
               return;
            }else{
              Mage::register('cartreturn', $response);
              $this->loadLayout();
              $this->renderLayout();
            }
            return;
	}
	public function optionsAction() {
	       // Get initial data from request
     
        $productId  = (int) $this->getRequest()->getParam('id');
        //$specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');

        $params = new Varien_Object();
        $params->setCategoryId(false);
        $params->setSpecifyOptions(false);

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
	}
	
	public function deleteAction(){
	   $id = (int) $this->getRequest()->getParam('id');
      
       if ($id) {
            try {
                $this->_getCart()->removeItem($id)
                  ->save();
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
       }
	   $this->loadLayout();
	   $this->renderLayout();
       return;
	
	}

	/**
     * get the top cart
     */
    public function topcartAction(){
       $this->loadLayout();
       $this->renderLayout(); 
    }

    /**
     * Delete cart item from sidebar 
     */

    public function deletecartsidebarAction(){
       $id = (int) $this->getRequest()->getParam('id');
       $response = array();
       if ($id) {
            try {
                $this->_getCart()->removeItem($id)
                  ->save();
                $response['status'] = 'SUCCESS';
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = 'Cannot remove the item.';
                $this->_getSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
       }
       $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
       return;
    
    }  

	/**
     * Update shopping cart data action
     */
    public function updatePostAction()
    {
        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }
       $this->loadLayout();
	   $this->renderLayout();
       return;
    }
	
	public function compareAction(){
        $response = array();
 
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
 
            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                $response['status'] = 'SUCCESS';
                $response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
               // Mage::register('referrer_url', $this->_getRefererUrl());
                Mage::helper('catalog/product_compare')->calculate();
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
                $this->loadLayout();
                $sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
                $sidebar_block->setTemplate('jmquickview/compare/sidebar.phtml');
                $sidebar = $sidebar_block->toHtml();
                $response['sidebar'] = $sidebar;
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }

    /**
     * Remove item from compare list
     */
    public function removeAction()
    {
        $response = array();
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if($product->getId()) {
                /** @var $item Mage_Catalog_Model_Product_Compare_Item */
                $item = Mage::getModel('catalog/product_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByProduct($product);

                if($item->getId()) {
                    $item->delete();
                    $response['status'] = 'SUCCESS';
                    $response['message'] = $this->__('The product %s has been removed from comparison list.', $product->getName());
                    Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
                    Mage::helper('catalog/product_compare')->calculate();
                     $this->loadLayout();
                    $sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
                    $sidebar_block->setTemplate('jmquickview/compare/sidebar.phtml');
                    $sidebar = $sidebar_block->toHtml();
                    $response['sidebar'] = $sidebar;
                }
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

    }

	 /**
     * Remove all items from comparison list
     */
    public function clearAction()
    {
        $response = array();
        $items = Mage::getResourceModel('catalog/product_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('catalog/session');

        try {
            $items->clear();
            $session->addSuccess($this->__('The comparison list was cleared.'));
            Mage::helper('catalog/product_compare')->calculate();
            $response['status'] = 'SUCCESS';
            $response['message'] = $this->__('The comparison list was cleared.');
            $this->loadLayout();
            $sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
            $sidebar_block->setTemplate('jmquickview/compare/sidebar.phtml');
            $sidebar = $sidebar_block->toHtml();
            $response['sidebar'] = $sidebar;
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred while clearing comparison list.'));
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        
    }
	
	
}