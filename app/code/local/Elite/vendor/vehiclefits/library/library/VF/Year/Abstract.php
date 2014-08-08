<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class VF_Year_Abstract
{
    protected $threshold = 25;
    protected $Y2KMode = true;

    function setThreshold($threshold)
    {
        $this->threshold = $threshold;
    }

    function setY2KMode($bool)
    {
        $this->Y2KMode = $bool;
    }
}