<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface VF_Level_Finder_Interface
{
    function query($sql);

    function listAll(VF_Level $entity, $parent_id = 0);

    function listInUse(VF_Level $entity, $parents = array(), $product_id = 0);
}
