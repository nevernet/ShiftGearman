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
        $this->setDescription('An example job used for testing');
    }



    // @codeCoverageIgnoreStart
    /**
     * Execute
     * Runs the job procedure. This is run by background process and cannot
     * be effectively tested.
     *
     *
     * @param \GearmanJob $job
     * @return mixed|void
     */
    public function execute(GearmanJob $job)
    {
        $workload = $job->workload();
        if($workload != 'quick')
        {
            echo '-------------------------------------------' . PHP_EOL;
            echo 'Executing job' . PHP_EOL . PHP_EOL;

            $iterations = 10;
            for($i = 1; $i < $iterations; $i++)
            {
                echo 'Iteration ' . $i . ' of ' . $iterations . PHP_EOL;
                sleep(1);
            }

            echo PHP_EOL . 'Done' . PHP_EOL . PHP_EOL;
            return;
        }


        echo 'Executing in quick mode.' . PHP_EOL;
        return;
    }
    // @codeCoverageIgnoreEnd



}// class ends here