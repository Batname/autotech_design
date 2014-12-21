<?php


require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Searchanise_Search_Indexer extends Mage_Shell_Abstract
{

    public function indexation() {
        Mage::helper('searchanise/ApiSe')->queueImport();
    }



    public function run()
    {
        if ($this->getArg('indexation')) {
            $this->indexation();
        }
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f cache.php -- [options]

  <cachetype>     Comma separated cache codes or value "all" for all caches

USAGE;
    }

}

$shell = new Searchanise_Search_Indexer();
$shell->run();
