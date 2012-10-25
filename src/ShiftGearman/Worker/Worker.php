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
 * @subpackage  Worker
 */

/**
 * @namespace
 */
namespace ShiftGearman\Worker;

use GearmanWorker;


/**
 * Base worker
 * Initialize this worker from your processes.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Worker
 */
class Worker
{

    /**
     * Gearman worker instance
     * @var \GearmanWorker
     */
    protected $gearmanWorker;


    /**
     * Gearman worker timeout
     * Idle worker will terminate after this timeout.
     * @var int
     */
    protected $workerTimeout;


    /**
     * Construct
     * Instantiates worker, registers jobs and waits for gearman to
     * trigger execution.
     *
     * @param array $config
     * @return void
     */
    public function __construct(array $config = null)
    {
        //use setters to set option
        if(is_array($config))
        {
            foreach($config as $property => $value)
            {
                $method = 'set' . ucfirst($property);
                if(method_exists($this, $method))
                    $this->$method($value);
            }
        }
    }


    /**
     * Run
     * Connects to gearman server, registers configured jobs and waits
     * for gearman to trigger.
     */
    public function run()
    {
        sleep(3);
    }



    /**
     * Set gearman worker
     * Allows you to inject arbitrary gearman worker instance.
     *
     * @param \GearmanWorker $gearmanWorker
     * @return \ShiftGearman\Worker\Worker
     */
    public function setGearmanWorker(GearmanWorker $gearmanWorker)
    {
        $this->gearmanWorker = $gearmanWorker;
        return $this;
    }


    /**
     * Get gearman worker
     * Checks if we already have a worker and returns that, otherwise
     * will create a new default worker instance.
     *
     * @return \GearmanWorker
     */
    public function getGearmanWorker()
    {
        if(!$this->gearmanWorker)
        {
            //start new worker
            $this->gearmanWorker = new GearmanWorker;

            //set timeout
            if(null != $this->getWorkerTimeout())
                $this->gearmanWorker->setTimeout($this->getWorkerTimeout());
        }
    }


    /**
     * Set worker timeout
     * Sets worker timeout value in milliseconds
     *
     * @param int $milliseconds
     * @return \ShiftGearman\Worker\Worker
     */
    public function setWorkerTimeout($milliseconds)
    {
        $this->workerTimeout = $milliseconds;
        return $this;
    }


    /**
     * Get worker timeout
     * Returns current timeout value in milliseconds.
     * @return int|null
     */
    public function getWorkerTimeout()
    {
        return $this->workerTimeout;
    }




    //Connect to configured servers
    //Register configured jobs
    //Wait for work





}// class ends here