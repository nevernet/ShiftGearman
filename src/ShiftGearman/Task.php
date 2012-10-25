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
 */

/**
 * @namespace
 */
namespace ShiftGearman;


/**
 * Gearman task
 * Task is a request to execute a certain job with parameters. Essentially
 * it is a directive of what to execute, how, when and with what parameters.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 */
class Task
{
    /**
     * Client connection to send this task to
     * @var string
     */
    protected $clientName = 'default';

    /**
     * Run in background
     * @var bool
     */
    protected $runInBackground = false;

    /**
     * Current task priority
     * @var string
     */
    protected $priority = 'normal';

    /**
     * Job name
     * Must be have workers capable of executing.
     * @var string
     */
    protected $jobName;

    /**
     * Job workload
     * @var string
     */
    protected $workload;

    /**
     * Unique task id
     * @var string
     */
    protected $taskId;

    /**
     * Job context
     * @var string
     */
    protected $context;


    /**
     * Set client name
     * Sets name of client connection (from config) this tas will be sent to.
     *
     * @param string $clientName
     * @return \ShiftGearman\Task
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;
        return $this;
    }


    /**
     * Get client name
     * Returns client connection name for this task.
     *
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }


    /**
     * Run in background
     * Sets a flag indicating whether this task must in background.
     *
     * @return \ShiftGearman\Task
     */
    public function runInBackground()
    {
        $this->runInBackground = true;
        return $this;
    }


    /**
     * Is background?
     * Returns a boolean value indicating whether this is a background task.
     * @return bool
     */
    public function isBackground()
    {
        return $this->runInBackground;
    }


    /**
     * Priority high
     * Sets current task priority to 'high'.
     *
     * @return \ShiftGearman\Task
     */
    public function priorityHigh()
    {
        $this->priority = 'high';
        return $this;
    }


    /**
     * Priority normal
     * Sets current task priority to 'normal'.
     *
     * @return \ShiftGearman\Task
     */
    public function priorityNormal()
    {
        $this->priority = 'normal';
        return $this;
    }


    /**
     * Priority low
     * Sets current task priority to 'low'.
     *
     * @return \ShiftGearman\Task
     */
    public function priorityLow()
    {
        $this->priority = 'low';
        return $this;
    }


    /**
     * Get priority
     * Returns current priority value.
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }


    /**
     * Set job name
     * Sets job name to be executed.
     *
     * @param string $jobName
     * @return \ShiftGearman\Task
     */
    public function setJobName($jobName)
    {
        $this->jobName = $jobName;
        return $this;
    }


    /**
     * Get job name
     * Returns currently set job name.
     * @return string | null
     */
    public function getJobName()
    {
        return $this->jobName;
    }


    /**
     * Set workload
     * Sets job workload.
     *
     * @param string $workload
     * @return \ShiftGearman\Task
     */
    public function setWorkload($workload)
    {
        $this->workload = $workload;
        return $this;
    }


    /**
     * Get workload
     * Returns currently set job workload.
     * @return string | null
     */
    public function getWorkload()
    {
        return $this->workload;
    }


    /**
     * Set task id
     * Sets unique job task id.
     *
     * @param string $taskId
     * @return \ShiftGearman\Task
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;
        return $this;
    }


    /**
     * Get task id
     * Returns currently set job task id.
     * @return string | null
     */
    public function getTastId()
    {
        return $this->taskId;
    }


    /**
     * Set context
     * Sets unique job context
     *
     * @param string $context
     * @return \ShiftGearman\Task
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }


    /**
     * Get context
     * Returns currently set job context.
     * @return string | null
     */
    public function getContext()
    {
        return $this->context;
    }







}// class ends here