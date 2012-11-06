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
use Zend\Di\Locator;
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
     * Service locator instance
     * Used to retrieve jobs by class name
     *
     * @var \Zend\Di\Locator
     */
    private $locator;


    /**
     * Worker config
     * Includes an array of job capabilities and connection name.
     * @var array
     */
    protected $config = array();


    /**
     * Worker connection configuration.
     * @var array
     */
    protected $connectionConfig;


    /**
     * Gearman worker instance.
     *
     * @var \GearmanWorker
     */
    protected $gearmanWorker;


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
     * @param \Zend\Di\Locator $locator
     * @param array $config
     * @return void
     */
    public function __construct(Locator $locator)
    {
        //set locator
        $this->locator = $locator;
    }


    /**
     * Get locator
     * Returns service locator instance.
     *
     * @return \Zend\Di\Locator
     */
    public function getLocator()
    {
        return $this->locator;
    }


    /**
     * Set config
     * Sets worker and connection configuration.
     *
     * @param array $config
     * @return \ShiftGearman\Worker\Worker
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }


    /**
     * Get config
     * Returns currently set config.
     * @return array | void
     */
    public function getConfig()
    {
        return $this->config;
    }


    /**
     * Set connection config
     * Sets a connection properties such as list of servers this worker
     * should connect to.
     *
     * @param array $connectionConfig
     * @return \ShiftGearman\Worker\Worker
     */
    public function setConnectionConfig(array $connectionConfig)
    {
        $this->connectionConfig = $connectionConfig;
        return $this;
    }


    /**
     * Get connection config
     * Returns currently set connection config.
     * @return array
     */
    public function getConnectionConfig()
    {
        return $this->connectionConfig;
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
     * @throws \ShiftGearman\Exception\ConfigurationException
     * @return \GearmanWorker
     */
    public function getGearmanWorker()
    {
        if(!$this->gearmanWorker)
        {
            //check config
            if(!$this->config || !$this->connectionConfig)
            {
                $message = "Can't start worker. Either configuration or ";
                $message .= "connection properties missing.";
                throw new ConfigurationException($message);
            }

            //start new worker
            $this->gearmanWorker = new GearmanWorker;

            //set timeout
            $timeout = $this->connectionConfig['timeout'];
            if($timeout)
                $this->gearmanWorker->setTimeout($timeout);

            //add gearman servers
            foreach($this->connectionConfig['servers'] as $server)
            {
                $this->gearmanWorker->addServer(
                    $server['host'],
                    $server['port']);
            }

            //add jobs
            foreach($this->config['jobs'] as $job)
                $this->addJob($job);

            //register worker capabilities
            $jobs = $this->getJobs();
            foreach($jobs as $job)
            {
                $name = $job->getName();
                $this->gearmanWorker->addFunction(
                    $name,
                    array($job, 'execute')
                );
            }
        }

        return $this->gearmanWorker;
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
        if(!$job instanceof AbstractJob)
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


    /**
     * Get jobs
     * Returns an array of jobs currently registered as this worker
     * capabilities.
     *
     * @return array
     */
    public function getJobs()
    {
        return $this->jobs;
    }


    /**
     * Run
     * Connects to gearman server, registers configured jobs and waits
     * for gearman to trigger.
     */
    public function run()
    {
        $worker = $this->getGearmanWorker();

        echo 'Waiting for job...' . PHP_EOL;
        while($worker->work())
        {
            if($worker->returnCode() != GEARMAN_SUCCESS)
            {
              echo "return_code: " . $worker->returnCode() . "\n";
              break;
            }
        }
    }



} // class ends here