<?php
/**
 * Projectshift
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file license/projectshift.mit.txt
 * It is also available through the world-wide-web at this URL:
 * http://projectshift.eu/license/mit
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@projectshift.eu so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2010 Webcomplex LLC (http://www.projectshift.eu)
 * @license    http://projectshift.eu/license/mit     MIT License
 *
 * @category    Projectshift
 * @package     ShiftMedia
 */

/**
 * @namespace
 */
namespace ShiftGearman;

use Zend\Module\Consumer\AutoloaderProvider;
use Zend\EventManager\StaticEventManager;
use Zend\Module\ModuleEvent;
use Zend\Module\Manager;


/**
 * Module manifest
 * This is consumed by ModuleManager to provide configuration and autoloader
 * settings
 *
 * @category    Projectshift
 * @package     ShiftMedia
 */
class Module implements AutoloaderProvider
{
    /**
     * Module config
     * Contains merged module configuration
     * @var \Zend\Config\Config
     */
    protected static $moduleConfig;


    /**
     * Get autoloader config
     * Returns configuration for module autoloading
     * @see Zend\Module\Consumer.AutoloaderProvider::getAutoloaderConfig()
     * @return array
     */
    public function getAutoloaderConfig()
    {
        $dir = __DIR__;
        $gearman = 'ShiftGearman';
        $mwGearman = 'mwGearman';
        $config = array();

        //class map loader config
        $config['Zend\Loader\ClassMapAutoloader'] = array(
            $dir . '/autoload_classmap.php'
        );

        //fallback to standard autoloader
        $config['Zend\Loader\StandardAutoloader'] = array(
            'namespaces' => array(
                  $gearman => $dir . '/src/' .$gearman,
                  $mwGearman => $dir . '/src/mw-gearman/src/mwGearman',
              )
        );

        return $config;
    }


    /**
     * Get config
     * Returns module configuration
     * @return array
     */
    public function getConfig()
    {
        $config = require __DIR__ . '/config/module.config.php';
        $di = require __DIR__ . '/config/di.php';
        $config = array_merge_recursive($config, $di);

        //add routes
        $routes = require_once __DIR__ . '/config/routes.php';
        $routerParams = array('parameters' => array('routes' => $routes));
        $config['di']['instance']['Zend\Mvc\Router\RouteStack'] = $routerParams;

		//return config
		return $config;
    }

    /**
     * Initialize
     * Bind to system events
     *
     * @param \Zend\Module\Manager $moduleManager
     * @return void
     */
    public function init(Manager $moduleManager)
    {
        //initialize config
        $moduleManager->events()->attach(
            'loadModules.post',
            array($this, 'initializeConfig')
        );
    }


    /**
     * Initialize config
     * This handler gets run once all application modules are loaded to
     * grab merged configuration
     *
     * @param \Zend\Module\ModuleEvent $moduleEvent
     * @return void
     */
    public function initializeConfig(ModuleEvent $moduleEvent)
    {
        //Grab module config
        $config = $moduleEvent->getConfigListener()->getMergedConfig();
        static::$moduleConfig = $config->ShiftGearman;
    }


    /**
     * Get module config
     * Returns merged module configuration.
     * @return \Zend\Config\Config
     */
    public static function getModuleConfig()
    {
        return static::$moduleConfig;
    }


} //class ends here

