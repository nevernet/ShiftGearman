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

use GearmanClient;
use Zend\Di\Locator;
use ShiftGearman\Module;
use ShiftGearman\Exception\ConfigurationException;


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
     * Service locator instance
     * @var \Zend\Di\Locator
     */
    protected $locator;

    /**
     * Gearman module configuration
     * @var array
     */
    protected $config;

    /**
     * An array of gearman client connections.
     * @var array
     */
    protected $clients = array();


    /**
     * Construct
     * Instantiates the service. Requires an instance of service locator.
     *
     * @param \Zend\Di\Locator $locator
     * @return void
     */
    public function __construct(Locator $locator)
    {
        $this->locator = $locator;
    }


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


    /**
     * Get worker
     * Instantiates and configures a worker.May optionally accept worker
     * configuration profile name. Then configured worker properties are
     * applied, otherwise you'll get a default worker with no servers or
     * job capabilities.
     *
     * @param string $workerName
     * @throws \ShiftGearman\Exception\ConfigurationException
     */
    public function getWorker($workerName = null)
    {
        //get config
        $config = $this->getConfig();

        //check worker config
        if(!isset($config['workers'][$workerName]))
        {
            $message = "Worker '$workerName' is not configured";
            throw new ConfigurationException($message);
        }

        //check worker connection config
        $connectionName = $config['workers'][$workerName]['connection'];
        if(!isset($config['connections'][$connectionName]))
        {
            $message = "Connection '$connectionName' requested by worker ";
            $message .= "'$workerName' is not configured";
            throw new ConfigurationException($message);
        }

        //create worker
        $worker = $this->locator->newInstance('ShiftGearman\Worker\Worker');
        $worker->setConfig($config['workers'][$workerName]);
        $worker->setConnectionConfig($config['connections'][$connectionName]);
        return $worker;
    }


    /**
     * Get client
     * Checks if we already have a client connection and returns that.
     * Otherwise creates a new connection.
     *
     * @param string $name
     * @return \GearmanClient
     */
    public function getClient($name)
    {
        if(!isset($this->connections[$name]))
        {
            $config = $this->getConfig();
            if(!isset($config['connections'][$name]))
            {
                $message = "Can't create client '$name'. Connection ";
                $message .= "configuration is missing";
                throw new ConfigurationException($message);
            }

            //create connection
            $connectionConfig = $config['connections'][$name];
            $client = new GearmanClient;

            //set timeout
            if($connectionConfig['timeout'])
                $client->setTimeout($connectionConfig['timeout']);

            //add servers
            foreach($connectionConfig['servers'] as $server)
                $client->addServer($server['host'], $server['port']);

            //preserve
            $this->clients[$name] = $client;
        }

        //return
        return $this->clients[$name];
    }


    /**
     * Get clients
     * Returns all currently existing client connections.
     *
     * @return array
     */
    public function getClients()
    {
        return $this->clients;
    }



    /**
     * Run task
     * Accepts a task and passes it to gearman server for execution.
     *
     * @param \ShiftGearman\Task $task
     * @return void
     */
    public function runTask(Task $task)
    {
        $client = $this->getClient($task->getClientName());

        $priority = $task->getPriority();
        switch($priority)
        {

            case 'high':
                $method = 'doHigh';
                if($task->isBackground())
                    $method = 'doHighBackground';
            break;


            case 'low':
                $method = 'doLow';
                if($task->isBackground())
                    $method = 'doLowBackground';
            break;

            default:
                $method = 'do';
                if($task->isBackground())
                    $method = 'doBackground';
            break;
        }

        //add task to queue
        $client->$method(
            $task->getJobName(),
            $task->getWorkload(),
            $task->getContext(),
            $task->getTastId()
        );
    }


    /**
     * Run tasks
     * Accepts an array of tasks to be executed at once. Tasks may have
     * different connections configured for them, so we first sort all tasks
     * by connection.
     *
     * @param array $tasks
     * @return void
     */
    public function runTasks(array $tasks)
    {
        $tasksByClient = array();
        foreach($tasks as $task)
        {
            $clientName = $task->getClientName();
            $tasksByClient[$clientName][] = $task;
        }


        foreach($tasksByClient as $clientName => $clientTasks)
        {
            //get client
            $client = $this->getClient($clientName);

            //add task
            foreach($clientTasks as $clientTask)
            {
                //figure out priority
                $priority = $clientTask->getPriority();
                switch($priority)
                {

                    case 'high':
                        $method = 'addTaskHigh';
                        if($task->isBackground())
                            $method = 'addTaskHighBackground';
                    break;


                    case 'low':
                        $method = 'addTaskHigh';
                        if($task->isBackground())
                            $method = 'addTaskHighBackground';
                    break;

                    default:
                        $method = 'addTask';
                        if($task->isBackground())
                            $method = 'addTaskBackground';
                    break;
                }

                //add
                $client->$method(
                    $task->getJobName(),
                    $task->getWorkload(),
                    $task->getContext(),
                    $task->getTastId()
                );
            }

            //run client tasks at once
            $client->runTasks();
        }
    }





}// class ends here