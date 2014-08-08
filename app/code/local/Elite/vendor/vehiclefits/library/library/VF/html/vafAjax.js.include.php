<?php
/**
 * Vehicle Fits
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@vehiclefits.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Vehicle Fits to newer
 * versions in the future. If you wish to customize Vehicle Fits for your
 * needs please refer to http://www.vehiclefits.com for more information.

 * @copyright  Copyright (c) 2013 Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$front = (bool)( isset( $_GET['front'] ) && $_GET['front'] );
$command = $front ? 'getLevels' : 'getLevels';
$schema = new VF_Schema;


$CONFIG['unavailableSelections'] = isset( $_GET['unavailableSelections'] ) ? $_GET['unavailableSelections'] : VF_Singleton::getInstance()->getConfig()->search->unavailableSelections;
$CONFIG['loadingStrategy'] = isset( $_GET['loadingStrategy'] ) ? $_GET['loadingStrategy'] : VF_Singleton::getInstance()->getConfig()->search->loadingStrategy;

if( $front )
{
    function shouldAutoSubmit()
    {
        return !VF_Singleton::getInstance()->showSearchButton();
    }
}
else
{
    function shouldAutoSubmit()
    {
        return false;
    }

    ?>
    
    jQuery(document).ready( function() {
        jQuery( '.vafDeleteSelected' ).click( function() {
            jQuery( '.vafcheck:checked' ).each( function() {
                jQuery( this ).nextAll( '.multiTree-closeLink' ).click();
            });
        });
        
        toggleVafFits = function()
        {
            var val = jQuery( '#universal' ).attr( 'checked' );
            if( val == true ) {
                jQuery( '#vaf-toggle' ).hide();
            } else {
                jQuery( '#vaf-toggle' ).show();
            }
        }
        
        jQuery( '#universal' ).click( toggleVafFits );
        toggleVafFits();
        
        jQuery( '.vafCheckAll' ).click( function() {
            jQuery( '.vafcheck' ).attr( 'checked', jQuery(this).attr( 'checked' ) );
        })
    });
    
    <?php
}



class VafJs
{
    protected $CONFIG, $schema, $front;
    public $decorators;
      
    function main( $CONFIG, $schema, $front )
    {

        $this->CONFIG = $CONFIG;
        $this->schema = $schema;
        $this->front = $front;
        
        $content = '';
        foreach( $this->decorators as $decorator )
        {
            $content = $decorator->main( $content, $this );
        }
        echo $content;
    }  
    
    function getConfig()
    {
        return $this->CONFIG;
    } 
    
    function getSchema()
    {
        return $this->schema;
    }
    
    function isFront()
    {
        return $this->front;
    }
}

interface VafJs_Decorator
{
    function main( $content, $main );
}     

class VafJs_Docready implements VafJs_Decorator
{
    function main( $content, $main )
    {
        ob_start();
        include( dirname(__FILE__).'/Js/docready.js.php' );
        return ob_get_clean();
    }
}     

class VafJs_Ucfirst implements VafJs_Decorator
{
    function main( $content, $main )
    {
        return file_get_contents(dirname(__FILE__).'/Js/ucfirst.js.php') . $content;
    }
} 

class VafJs_UnavailableSelections implements VafJs_Decorator
{
    function main( $content, $main )
    {
        $CONFIG = $main->getConfig(); 
        $mode = $CONFIG['unavailableSelections'];
        $schema = $main->getSchema();
        $levelsExceptRoot = $schema->getLevelsExceptRoot();
        ob_start();
        include( dirname(__FILE__).'/Js/unavailable-selections.js.php' ); 
        return ob_get_clean();
    }
}

class VafJs_Callbacks implements VafJs_Decorator
{
    function main( $content, $main )
    {
        $CONFIG = $main->getConfig(); 
        $schema = $main->getSchema();
        $levels = $schema->getLevels();
        ob_start();
        include( dirname(__FILE__).'/Js/callbacks.js.php' ); 
        return ob_get_clean();
    }
}

class VafJs_Submits implements VafJs_Decorator
{
    function main( $content, $main )
    {
        $levels = $main->getSchema()->getLevels();
        $c = count( $levels );
        ob_start();
        include( dirname(__FILE__).'/Js/submits.js.php' ); 
        return ob_get_clean();
    }
}  

class VafJs_Loader_Ajax implements VafJs_Decorator
{
    function main( $content, $main )
    {
        $levels = $main->getSchema()->getLevels();
        $leafLevel = $main->getSchema()->getLeafLevel();
        
        $c = count( $levels );
        ob_start();
        include( dirname(__FILE__).'/Js/loader_ajax.js.php' ); 
        return ob_get_clean();
    }
    
    protected function loadingText()
    {
        return VF_Singleton::getInstance()->getLoadingText();
    }
}     

class VafJs_Loader_Offline implements VafJs_Decorator
{
    function main( $content, $main )
    {
        $levels = $main->getSchema()->getLevels();
        $schema = $main->getSchema();
        $leafLevel = $schema->getLeafLevel();
        $c = count( $levels );
        ob_start();
        include( dirname(__FILE__).'/Js/loader_offline.js.php' ); 
        return ob_get_clean();
    }
    
    protected function leafFirst()
    {
        return VF_Singleton::getInstance()->getConfig()->search->leafLevelFirst;
    }
    
    protected function loadingText()
    {
        return VF_Singleton::getInstance()->getLoadingText();
    }
}     

class VafJs_Default implements VafJs_Decorator
{
    
    function main( $content, $main )
    {
        ob_start();
        
        include( dirname(__FILE__).'/Js/default.js.php' ); 
        
        $html = ob_get_clean();
        return $content . $html;
    }
    
    protected function leafLevel()
    {
        $schema = new Vf_Schema;
        return $schema->getLeafLevel();
    }
}


$vafJs = new VafJs;
$vafJs->decorators = array(
    new VafJs_Ucfirst(),
    new VafJs_UnavailableSelections(),
    new VafJs_Default,
    new VafJs_DocReady()
);
$vafJs->main( $CONFIG, $schema, $front );