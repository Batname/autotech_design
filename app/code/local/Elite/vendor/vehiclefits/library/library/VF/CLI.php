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
 *
 * @copyright  Copyright (c) 2013 Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VF_CLI
{
    protected $opt;
    protected $options = array();

    function __construct()
    {
        $this->bootstrap();

        # Define the command line arguments this tool accepts
        $this->opt = new Zend_Console_Getopt($this->options + array(
            'config|c=s' => 'PHP config file to initialize with',
        ));

        $this->requireConfig();
        $this->injectDb();
    }

    /* Set up include paths & register autoloader */
    function bootstrap()
    {
        require_once(__DIR__ . '/../../bootstrap-tests.php');
    }

    /* Figure out where we are reading the database configuration from */
    function requireConfig()
    {
        if (!defined('DB_ENV_INJECTED')) {
            $config = $this->opt->getOption('config');
            if ($config) {
                require_once($config);
            } elseif (file_exists('vfconfig.php')) {
                require_once('vfconfig.php');
            } else {
                require_once('vfconfig.default.php');
            }
        }
    }

    /* Inject a database adapter into VF_Singleton using the configuration from previous step */
    function injectDb()
    {
        VF_Singleton::getInstance()->setReadAdapter(
            new VF_TestDbAdapter(array(
                                      'dbname'   => getenv('PHP_VAF_DB_NAME'),
                                      'username' => getenv('PHP_VAF_DB_USERNAME'),
                                      'password' => getenv('PHP_VAF_DB_PASSWORD')
                                 ))
        );
    }

    function lastArgument()
    {
        global $argv;
        if (isset($argv[count($argv) - 1])) {
            return $argv[count($argv) - 1];
        }
    }

    function usage()
    {
        echo "Usage vf <command> [<args>]\n\n";
        echo "The most commonly used vf commands are:\n";
        echo "  schema - (re)Set the schema levels\n";
        echo "  importvehicles - Import a list of vehicles\n";
        echo "  exportvehicles - Export a list of vehicles\n";
        echo "  importfitments - Import product fitments\n";
        echo "  exportfitments - Export product fitments\n";
    }
}
