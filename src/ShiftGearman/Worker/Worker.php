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
use ShiftGearman\Job\AbstractJob;
use ShiftGearman\Exception\ConfigurationException;
use ShiftGearman\Exception\RuntimeException;


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
     * Gearman worker instance.
     *
     * @var \GearmanWorker
     */
    protected $gearmanWorker;


    /**
     * Gearman worker timeout
     * Idle worker will terminate after this timeout.
     *
     * @var int
     */
    protected $workerTimeout;

    /**
     * Servers
     * An array of gearman servers to listen to.
     *
     * @var array
     */
    protected $servers = array();


    /**
     * Jobs
     * An array of jobs this worker is capable of executing.
     *
     * @var array
     */
    protected $jobs = array();


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


    /**
     * Set servers
     * Allows to set a pool of gearman servers to listen to.
     *
     * @param array $servers
     * @return \ShiftGearman\Worker\Worker
     */
    public function setServers(array $servers)
    {
        foreach($servers as $server)
        {
            $host = $server['host'] ?: null;
            $port = $server['port'] ?: null;
            $this->addServer($host, $port);
        }

        return $this;
    }


    /**
     * Add server
     * Adds a single gearman server to listen to.
     *
     * @param string $host
     * @param int $port
     * @return \ShiftGearman\Worker\Worker
     */
    public function addServer($host, $port = 4730)
    {
        $this->servers[] = array($host, $port);
        return $this;
    }


    /**
     * Get servers
     * Return an array of currently configured gearman servers.
     * @return array
     */
    public function getServers()
    {
        return $this->servers;
    }


    /**
     * Set jobs
     * Sets an array of jobs this worker is capable of executing.
     *
     * @param array $jobs
     * @return \ShiftGearman\Worker\Worker
     */
    public function setJobs(array $jobs)
    {
        foreach($jobs as $job)
            $this->addJob($job);

        return $this;
    }


    /**
     * Add job
     * Evaluates and registeres a job. Jobs may be either a class name to get
     * from locator, or an instance of AbstractJob.
     *
     * @param string | \ShiftGearman\Job\AbstractJob $job
     * @throws \ShiftGearman\Exception\ConfigurationException
     * @throws \ShiftGearman\Exception\RuntimeException
     */
    public function addJob($job)
    {
        //get job from locator
        try
        {
            if(is_string($job))
                $job = $this->locator->newInstance($job);
        }
        catch(\Exception $excepion)
        {
            $message = "Can't get $job from service locator. Error: ";
            $message .= $excepion->getMessage();
            throw new ConfigurationException($message, 500);
        }

        //check job
        if($job instanceof AbstractJob)
            throw new RuntimeException("Invalid job type.");


        //add job
        $this->jobs[] = $job;
        return $this;
    }


    /**
     * Remove job
     * Removes a job from worker capabilities.
     *
     * @param \ShiftGearman\Job\AbstractJob $job
     * @return \ShiftGearman\Worker\Worker
     */
    public function removeJob(AbstractJob $job)
    {
        foreach($this->jobs as $index => $workerJob)
        {
            if($workerJob == $job)
                unset($this->jobs[$index]);
        }

        return $this;
    }



}// class ends here