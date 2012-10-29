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

use DateTime;
use DateInterval;
use ShiftGearman\Exception\DomainException;

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
    // ------------------------------------------------------------------------

    /*
     * Task properties
     */


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

    // ------------------------------------------------------------------------

    /*
     * Scheduling options
     */

    /**
     * Start running a task at this time (optionally repeat with interval).
     * @var \DateTime
     */
    protected $start;

    /**
     * How much times to repeat a job
     * @var int
     */
    protected $repeatTimes = 1;

    /**
     * Repeat interval
     * @var \DateInterval
     */
    protected $repeatInterval;


    // ------------------------------------------------------------------------

    /*
     * Task public API
     */


    /**
     * Construct
     * Instantiates task directive.
     *
     * @return void
     */
    public function __construct()
    {
        //set unique
        $this->setTaskId(crc32(microtime()));
    }



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


    // ------------------------------------------------------------------------

    /*
     * Task scheduling api
     */


    /**
     * Set repeat times
     * Sets how much times to repeat current task. Repetition requires
     * an interval to be provided.
     *
     * @param $times
     * @return Task
     */
    public function setRepeatTimes($times)
    {
        $this->repeatTimes($times);
        return $this;
    }


    /**
     * Get repeat times
     * Returns current task repetition value.
     *
     * @return int
     */
    public function getRepeatTimes()
    {
        return $this->repeatInterval;
    }


    /**
     * Set repeat interval
     * Sets task repetition interval.
     *
     * @param \DateInterval $interval
     * @return \ShiftGearman\Task
     */
    public function setRepeatInterval(DateInterval $interval)
    {
        $this->repeatInterval = $interval;
        return $this;
    }


    /**
     * Get repeat interval
     * Returns currently set repetition interval.
     * @return \DateInterval | void
     */
    public function getRepeatInterval()
    {
        return $this->repeatInterval;
    }


    /**
     * Set repeat
     * A handy shortcut to quickly set task repetition properties.
     *
     * @param $times
     * @param $interval
     * @return Task
     * @throws Exception\DomainException
     */
    public function setRepeat($times, $interval)
    {
        if(!$interval instanceof DateInterval)
            $interval = new DateInterval($interval);

        if(!is_numeric($times) || !$interval instanceof DateInterval)
            throw new DomainException("Task repetition settings invalid");

        $this->runInBackground();
        $this->repeatTimes = $times;
        $this->repeatInterval = new DateInterval($interval);
        return $this;
    }


    /**
     * Set run at
     * Sets a datetime this job would be subitted to gearman.
     * Note: a special scheduler worker required.
     *
     * @param \DateTime $datetime
     * @return \ShiftGearman\Task
     */
    public function setStart(DateTime $datetime)
    {
        $this->runAt = $datetime;
        $this->runInBackground();
        return $this;
    }


    /**
     * Get run at
     * Returns scheduled datetime or null meaning run at once.
     *
     * @return \DateTime | void
     */
    public function getStart()
    {
        return $this->runAt;
    }





}// class ends here