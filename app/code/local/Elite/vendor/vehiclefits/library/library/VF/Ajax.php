<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Ajax implements VF_Configurable
{
    protected $alphaNumeric;
    protected $schema;

    /** @var Zend_Config */
    protected $config;

    function execute(VF_Schema $schema, $alphaNumeric = false)
    {
        $this->alphaNumeric = $alphaNumeric;
        $this->schema = $schema;

        $levels = $schema->getLevels();
        $c = count($levels);

        $levelFinder = new VF_Level_Finder($schema);
        if (isset($_GET['front'])) {
            $product = isset($_GET['product']) ? $_GET['product'] : null;
            if ($alphaNumeric) {
                $children = $levelFinder->listInUseByTitle(new VF_Level($this->requestLevel()), $this->requestLevels(), $product);
            } else {
                if($this->shouldListAll()) {
                    $children = $levelFinder->listAll(new VF_Level($this->requestLevel()), $this->requestLevels(), $product);
                } else {
                    $children = $levelFinder->listInUse(new VF_Level($this->requestLevel()), $this->requestLevels(), $product);
                }
            }
        } else {
            $children = $levelFinder->listAll($this->requestLevel(), $this->requestLevels());
        }

        echo $this->renderChildren($children);
    }

    function shouldListAll()
    {
        return $this->getConfig()->search->showAllOptions;
    }

    function requestLevel()
    {
        return $this->getRequest()->getParam('requestLevel');
    }

    function getValue($level)
    {
        return isset($_GET[$level]) ? $_GET[$level] : null;
    }

    /** Get the option text prompting the user to make a selection */
    function getDefaultSearchOptionText($level = null)
    {
        if (!isset($_GET['front'])) {
            return false;
        }
        return VF_Singleton::getInstance()->getDefaultSearchOptionText($level, $this->getConfig());
    }

    function renderChildren($children)
    {
        ob_start();
        $label = $this->getDefaultSearchOptionText($this->requestLevel());
        if (count($children) > 1 && $label) {
            echo '<option value="0">' . $label . '</option>';
        }

        foreach ($children as $child) {
            if ($this->alphaNumeric) {
                echo '<option value="' . $child->getTitle() . '">' . htmlentities($child->getTitle(), ENT_QUOTES, 'UTF-8') . '</option>';
            } else {
                echo '<option value="' . $child->getId() . '">' . htmlentities($child->getTitle(), ENT_QUOTES, 'UTF-8') . '</option>';
            }
        }
        return ob_get_clean();
    }

    function requestLevels()
    {
        $params = array();
        foreach ($this->schema()->getLevels() as $level) {
            if ($this->getRequest()->getParam($level)) {
                $params[$level] = $this->getRequest()->getParam($level);
            }
        }
        return $params;
    }

    function schema()
    {
        return $this->schema;
    }

    function getRequest()
    {
        return new Zend_Controller_Request_Http();
    }

    function getConfig()
    {
        if (!$this->config instanceof Zend_Config) {

            $this->config = VF_Singleton::getInstance()->getConfig();
        }
        return $this->config;
    }

    function setConfig(Zend_Config $config)
    {
        $this->config = $config;
    }
}
