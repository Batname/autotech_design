<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Year extends VF_Year_Abstract
{
    protected $year;

    function __construct($year)
    {
        $this->year = $year;
    }

    function isValid()
    {
        if (!is_numeric($this->year)) {
            return false;
        }
        if (strlen($this->year) == 2 || strlen($this->year) == 4) {
            return true;
        }
        return false;
    }

    function value()
    {
        if (!$this->isValid()) {
            throw new VF_Year_Exception('Trying to work with invalid year [' . $this->year . ']');
        }

        if (strlen($this->year) != 2 || !$this->Y2KMode) {
            return $this->year;
        }
        if ($this->year < $this->threshold) {
            $this->year = '20' . $this->year;
        } else {
            $this->year = '19' . $this->year;
        }
        return $this->year;
    }
}