<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_IdentityMap
{
    protected $levels = array();

    static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new VF_Level_IdentityMap;
        }
        return $instance;
    }

    /** TEST ONLY */
    static function reset()
    {
        self::getInstance()->doReset();
    }

    /** TEST ONLY */
    function doReset()
    {
        $this->levels = array();
    }

    function add($level)
    {
        array_push($this->levels, $level);
    }

    function remove($level)
    {

    }

    function has($level, $id)
    {
        foreach ($this->levels as $level) {
            if ($level->getType() == $level && $level->getId() == $id) {
                return true;
            }
        }
        return false;
    }

    function get($level, $id)
    {
        foreach ($this->levels as $level) {
            if ($level->getType() == $level && $level->getId() == $id) {
                return $level;
            }
        }
    }
}