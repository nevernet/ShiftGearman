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
 * @package     ShiftGearman
 * @subpackage  Job
 */

/**
 * @namespace
 */
namespace ShiftGearman;

use ShiftGearman\Module;

/**
 * Gearman service
 * This is a general application-level API to gearman functionality. The service
 * is capable of instantiation and configuration of workers, and adding
 * tasks to gearman queue.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Job
 */
class GearmanService
{
    /**
     * Gearman module configuration
     * @var array
     */
    protected $config;


    /**
     * Set config
     * Allows you to inject arbitrary configuration to be used within service.
     *
     * @param array $config
     * @return \ShiftGearman\GearmanService
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }


    /**
     * Get config
     * Returns injected configuration if any, otherwise returns configuration
     * from module bootstrap.
     *
     * @return array
     */
    public function getConfig()
    {
        if(!$this->config)
            $this->config = Module::getModuleConfig()->toArray();

        return $this->config;
    }



}// class ends here