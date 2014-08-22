<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

class Simtech_Searchanise_InfoController extends Mage_Core_Controller_Front_Action
{
    const RESYNC             = 'resync'; 
    const OUTPUT             = 'visual';
    const PROFILER           = 'profiler';
    const STORE_ID           = 'store_id';
    const CHECK_DATA         = 'check_data';
    const DISPLAY_ERRORS     = 'display_errors';
    const PRODUCT_ID         = 'product_id';
    const PRODUCT_IDS        = 'product_ids';
    const CATEGORY_ID        = 'category_id';
    const CATEGORY_IDS       = 'category_ids';
    const PAGE_ID            = 'page_id';
    const PAGE_IDS           = 'page_ids';
    const BY_ITEMS           = 'by_items';
    const PARENT_PRIVATE_KEY = 'parent_private_key';

    /**
     * Dispatch event before action
     *
     * @return void
    */
    public function preDispatch()
    {
        // It is need if controller will used the "generateProductsFeed" function

        // Do not start standart session
        $this->setFlag('', self::FLAG_NO_START_SESSION, 1); 
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, 1);
        $this->setFlag('', self::FLAG_NO_COOKIES_REDIRECT, 0);
        $this->setFlag('', self::FLAG_NO_PRE_DISPATCH, 1);

        // Need for delete the "PDOExceptionPDOException" error
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, 1); 

        parent::preDispatch();

        return $this;
    }

    public function indexAction()
    {
        $visual = $this->getRequest()->getParam(self::OUTPUT);

        if (!Mage::helper('searchanise')->checkPrivateKey()) {
            $_options = Mage::helper('searchanise/ApiSe')->getAddonOptions();
            $options = array(
                'status'  => $_options['addon_status'],
                'api_key' => $_options['api_key'],
            );

            if ($visual) {
                Mage::helper('searchanise/ApiSe')->printR($options);
            } else {
                echo Mage::helper('core')->jsonEncode($options);
            }
        } else {
            $resync        = $this->getRequest()->getParam(self::RESYNC);
            $profiler      = $this->getRequest()->getParam(self::PROFILER);
            $storeId       = $this->getRequest()->getParam(self::STORE_ID);
            $checkData     = $this->getRequest()->getParam(self::CHECK_DATA);
            $displayErrors = $this->getRequest()->getParam(self::DISPLAY_ERRORS);
            $productId     = $this->getRequest()->getParam(self::PRODUCT_ID);
            $productIds    = $this->getRequest()->getParam(self::PRODUCT_IDS);
            $categoryId    = $this->getRequest()->getParam(self::CATEGORY_ID);
            $categoryIds   = $this->getRequest()->getParam(self::CATEGORY_IDS);
            $pageId        = $this->getRequest()->getParam(self::PAGE_ID);
            $pageIds       = $this->getRequest()->getParam(self::PAGE_IDS);
            $byItems       = $this->getRequest()->getParam(self::BY_ITEMS);
            if ($byItems == 'Y') {
                Mage::helper('searchanise/ApiProducts')->setIsGetProductsByItems(true);
            }
            $checkData = $checkData != 'N';

            if ($displayErrors) {
                @error_reporting(E_ALL | E_STRICT);
                @ini_set('display_errors', 1);
                @ini_set('display_startup_errors', 1);
            } else {
                @error_reporting(0);
                @ini_set('display_errors', 0);
                @ini_set('display_startup_errors', 0);
            }

            $productIds = $productId ? $productId : ($productIds ? explode(',', $productIds) : 0);
            $categoryIds = $categoryId ? $categoryId : ($categoryIds ? explode(',', $categoryIds) : 0);
            $pageIds = $pageId ? $pageId : ($pageIds ? explode(',', $pageIds) : 0);

            $store = null;
            if (!empty($storeId)) {
                $store = Mage::app()->getStore($storeId);
            }

            if ($profiler) {
                $numberIterations = 1000;

                if (empty($productIds)) {
                    Mage::app()->setCurrentStore(0);
                    $allProductIds = Mage::getModel('catalog/product')->getCollection()->setPageSize($numberIterations)->load();

                    $productIds = array();
                    foreach ($allProductIds as $key => $value) {
                        $productIds[] = $value['entity_id'];
                        if (count($productIds) > $numberIterations) {
                            break;
                        }
                    }
                    $numberIterations = 1;
                }

                $n = 0;
                $productFeeds = '';
                while ($n < $numberIterations) {
                    $productFeeds = Mage::helper('searchanise/ApiProducts')->generateProductsFeed($productIds, $store, true);
                    $n++;
                }

                echo $this->_profiler();

                $feed = array(
                    'header'     => Mage::helper('searchanise/ApiProducts')->getHeader($store),
                    'items'      => $productFeeds,
                    'schema'     => Mage::helper('searchanise/ApiProducts')->getSchema(Simtech_Searchanise_Model_Queue::NOT_DATA, $store),
                    'categories' => Mage::helper('searchanise/ApiCategories')->generateCategoriesFeed(Simtech_Searchanise_Model_Queue::NOT_DATA, $store, true),
                );
                
                if ($visual) {
                    Mage::helper('searchanise/ApiSe')->printR(Mage::helper('core')->jsonEncode($feed), $feed);
                } else {
                    echo Mage::helper('core')->jsonEncode($feed);
                }

            } elseif ($resync) {
                Mage::helper('searchanise/ApiSe')->queueImport($store);

            } elseif (!empty($productIds) || !empty($categoryIds) || !empty($pageIds)) {
                if (!$categoryIds) {
                    $categoryIds = Simtech_Searchanise_Model_Queue::NOT_DATA;
                }
                $feed = array(
                    'header'     => Mage::helper('searchanise/ApiProducts')->getHeader($store),
                    'items'      => Mage::helper('searchanise/ApiProducts')->generateProductsFeed($productIds, $store, $checkData),
                    'schema'     => Mage::helper('searchanise/ApiProducts')->getSchema(Simtech_Searchanise_Model_Queue::NOT_DATA, $store),
                    'categories' => Mage::helper('searchanise/ApiCategories')->generateCategoriesFeed($categoryIds, $store, $checkData),
                    'pages'      => Mage::helper('searchanise/ApiPages')->generatePagesFeed($pageIds, $store, $checkData),
                );

                if ($visual) {
                    Mage::helper('searchanise/ApiSe')->printR(Mage::helper('core')->jsonEncode($feed), $feed);
                } else {
                    echo Mage::helper('core')->jsonEncode($feed);
                }

            } else {
                Mage::helper('searchanise/ApiSe')->checkImportIsDone();
                
                $options = Mage::helper('searchanise/ApiSe')->getAddonOptions();
                if (!$options) {
                    $options = array();
                }
                $options['next_queue'] = Mage::getModel('searchanise/queue')->getNextQueue();
                $options['total_items_in_queue'] = Mage::getModel('searchanise/queue')->getTotalItems();
                
                $options['cron_async_enabled'] = Mage::helper('searchanise/ApiSe')->checkCronAsync();
                $options['ajax_async_enabled'] = Mage::helper('searchanise/ApiSe')->checkAjaxAsync();
                $options['object_async_enabled'] = Mage::helper('searchanise/ApiSe')->checkObjectAsync();

                $options['use_navigation'] = Mage::helper('searchanise/ApiSe')->getUseNavigation();
                $options['use_full_feed'] = Mage::helper('searchanise/ApiSe')->getUseFullFeed();

                $options['max_execution_time'] = ini_get('max_execution_time');
                @set_time_limit(0);
                $options['max_execution_time_after'] = ini_get('max_execution_time');

                $options['ignore_user_abort'] = ini_get('ignore_user_abort');
                @ignore_user_abort(1);
                $options['ignore_user_abort_after'] = ini_get('ignore_user_abort_after');

                $options['memory_limit'] = ini_get('memory_limit');
                $asyncMemoryLimit = Mage::helper('searchanise/ApiSe')->getAsyncMemoryLimit();
                if (substr(ini_get('memory_limit'), 0, -1) < $asyncMemoryLimit) {
                    @ini_set('memory_limit', $asyncMemoryLimit . 'M');
                }
                $options['memory_limit_after'] = ini_get('memory_limit');

                if ($visual) {
                    Mage::helper('searchanise/ApiSe')->printR($options);
                } else {
                    echo Mage::helper('core')->jsonEncode($options);
                }
            }
        }

        die();
        return $this;
    }

    protected function _profiler()
    {
        // if (!$this->_beforeToHtml()
        //     || !Mage::getStoreConfig('dev/debug/profiler')
        //     || !Mage::helper('core')->isDevAllowed()) {
        //     return '';
        // }

        $timers = Varien_Profiler::getTimers();

        #$out = '<div style="position:fixed;bottom:5px;right:5px;opacity:.1;background:white" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.1">';
        #$out = '<div style="opacity:.1" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.1">';
        $out = "<a href=\"javascript:void(0)\" onclick=\"$('profiler_section').style.display=$('profiler_section').style.display==''?'none':''\">[profiler]</a>";
        $out .= '<div id="profiler_section" style="background:white; display:block">';
        $out .= '<pre>Memory usage: real: '.memory_get_usage(true).', emalloc: '.memory_get_usage().'</pre>';
        $out .= '<table border="1" cellspacing="0" cellpadding="2" style="width:auto">';
        $out .= '<tr><th>Code Profiler</th><th>Time</th><th>Cnt</th><th>Emalloc</th><th>RealMem</th></tr>';
        foreach ($timers as $name=>$timer) {
            $sum = Varien_Profiler::fetch($name,'sum');
            $count = Varien_Profiler::fetch($name,'count');
            $realmem = Varien_Profiler::fetch($name,'realmem');
            $emalloc = Varien_Profiler::fetch($name,'emalloc');
            if ($sum<.0010 && $count<10 && $emalloc<10000) {
                continue;
            }
            $out .= '<tr>'
                .'<td align="left">'.$name.'</td>'
                .'<td>'.number_format($sum,4).'</td>'
                .'<td align="right">'.$count.'</td>'
                .'<td align="right">'.number_format($emalloc).'</td>'
                .'<td align="right">'.number_format($realmem).'</td>'
                .'</tr>'
            ;
        }
        $out .= '</table>';
        $out .= '<pre>';
        $out .= print_r(Varien_Profiler::getSqlProfiler(Mage::getSingleton('core/resource')->getConnection('core_write')), 1);
        $out .= '</pre>';
        $out .= '</div>';

        return $out;
    }
}