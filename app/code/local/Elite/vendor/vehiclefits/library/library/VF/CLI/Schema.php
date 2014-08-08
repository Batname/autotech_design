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
class VF_CLI_Schema extends  VF_CLI
{
    protected $levels;

    protected $generator;
    protected $opt;
    protected $options = array(
        'force|f'    => 'force creation without prompting to delete old schema',
        'levels|l=s'    => 'levels to create',
        'add|a' => 'add schema instead of replace',
    );

    const DONE = "\nDone";

    function __construct()
    {
        $this->generator = new VF_Schema_Generator();
        parent::__construct();
    }

    function main()
    {
        $this->doMain(array(
            'force'=>$this->opt->getOption('force'),
            'levels'=>$this->opt->getOption('levels'),
            'add'=>$this->opt->getOption('add')
        ));
    }

    function doMain($options)
    {
        if(!$options['levels']) {
            $this->askUserLevels();
        } else {
            $this->levels = $options['levels'];
        }
        if(!$options['add'] && !$options['force']) {
            $this->confirmTablesToDrop();
        }
        if(!$options['add']) {
            $this->generator->dropExistingTables();
        }

        if($options['add']) {
            VF_Schema::create($options['levels']);
            $this->notifyUser( self::DONE );
        } else {
            $this->createTheNewTables();
        }
    }

    protected function isYes( $value )
    {
        return 'y' == strtolower($value);
    }

    protected function askUserLevels()
    {
        $this->levels = $this->askUser( "Enter Levels, Comma Delim, [enter] for [make,model,year]:" );
        if( empty($this->levels) )
        {
            $this->levels = 'make,model,year';
        }
    }

    protected function askUser( $prompt )
    {
        $this->notifyUser( $prompt . ':' );
        return trim(fread(STDIN, 80),"\n\r "); // Read up to 80 characters or a newline
    }

    protected function confirmTablesToDrop()
    {
        $tables = $this->generator->getEliteTables();
        $this->notifyUser( "Will drop " . count( $tables ) . ' tables (' . implode( ', ', $tables ) . '), this ok? Y/N' );
        $response = trim(fread(STDIN, 80),"\n\r "); // Read up to 80 characters or a newline
        if( trim(ucfirst($response)) != 'Y' ) exit();
    }

    protected function createTheNewTables()
    {
        $this->notifyUser( "Applying Standard Schema" );
        $sql = $this->generator->execute( explode(',', $this->levels), true );
        $this->notifyUser( self::DONE );
    }

    protected function notifyUser( $msg )
    {
        echo $msg . "\n";
    }
}