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
namespace ShiftGearman\Job;

/**
 * Example job
 * This is an example of gearman job implementation.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Job
 */
class ExampleJob extends AbstractJob
{
    /**
     * Init
     * Initialize
     *
     * @return \ShiftGearman\Job\ExampleJob
     */
    public function init()
    {
        $this->setName('shiftgearman.example');
    }


    /**
     * Execute
     * Runs the job procedure
     *
     * @return mixed|void
     */
    public function execute()
    {
        echo 'Executing job' . PHP_EOL;
        echo 'Done' . PHP_EOL;
    }



}// class ends here