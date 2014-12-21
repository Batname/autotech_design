<?php


require_once 'abstract.php';

/**
 * Magento Searchanise_Search_Indexer Script
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
        } else {
            echo $this->usageHelp();
        }
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f search_indexer.php -- [options]

  indexation     Start indexation

USAGE;
    }

}

$shell = new Searchanise_Search_Indexer();
$shell->run();
