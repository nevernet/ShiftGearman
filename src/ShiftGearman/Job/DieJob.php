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
use GearmanJob;

/**
 * Example job
 * This is an example of gearman job implementation.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Job
 */
class DieJob extends AbstractJob
{
    /**
     * Init
     * Initialize
     *
     * @return \ShiftGearman\Job\ExampleJob
     */
    public function init()
    {
        $this->setName('shiftgearman.diejob');
        $this->setDescription('A job for testing different kinds of errors');
    }


    /**
     * Execute
     * Runs the job procedure
     *
     * @param \GearmanJob $job
     * @return mixed|void
     */
    public function execute(GearmanJob $job)
    {
        echo 'Executing job';
        $workload = $job->workload();

        if($workload == 'die')
        {
            echo 'Issuing a die statement' . PHP_EOL;
            die('Die execution requested');
        }

        if($workload == 'exception')
        {
            echo 'Throwing an exception' . PHP_EOL;
            throw new \Exception('Job produced an exception');
        }

        if($workload == 'error')
        {
            echo 'Triggering an error' . PHP_EOL;
            trigger_error("Error requested", E_USER_ERROR);
        }

    }



}// class ends here