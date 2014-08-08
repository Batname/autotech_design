<?php
/*------------------------------------------------------------------------
# JM Fabian - Version 1.0 - Licence Owner JA155256
# ------------------------------------------------------------------------
# Copyright (C) 2004-2009 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: J.O.O.M Solutions Co., Ltd
# Websites: http://www.joomlart.com - http://www.joomlancers.com
# This file may not be redistributed in whole or significant part.
-------------------------------------------------------------------------*/ 


class Wavethemes_Jmbasetheme_Model_Config_Offcanvas_Listeffect
{
    public function toOptionArray()
    {
        return array(
			array('value'=>'st-effect-default', 'label'=> 'Default'),
            array('value'=>'jm-effect st-effect-push', 'label'=> 'Push'),
            array('value'=>'jm-effect st-effect-rotate-pusher', 'label'=> 'Rotate Pusher'),
            array('value'=>'jm-effect st-effect-rotate-in', 'label'=>'3D Rotate in'),
            array('value'=>'jm-effect st-effect-rotate-out', 'label'=>'3D Rotate out'),
            array('value'=>'jm-effect st-effect-delayed-rotate', 'label'=>'Delayed 3D rotate')
        );
    }    
}
