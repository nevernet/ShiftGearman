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
use DateTimeZone;
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
    /*
     * Task properties
     */


    /**
     * Unique task id
     *
     * @ORM\Id
     * @ORM\Column(type = "string", length = 250, unique = true)
     * @var string
     */
    protected $id;

    /**
     * Client connection to send this task to.
     *
     * @ORM\Column(type = "string", length = 50, nullable = false)
     * @var string
     */
    protected $clientName = 'default';

    /**
     * Run in background
     *
     * @ORM\Column(type = "boolean")
     * @var bool
     */
    protected $runInBackground = false;

    /**
     * Current task priority
     *
     * @ORM\Column(type = "string", length = 15, nullable = false)
     * @var string
     */
    protected $priority = 'normal';

    /**
     * Job name
     * Must be have workers capable of executing.
     *
     * @ORM\Column(type = "string", length = 250, nullable = false)
     * @var string
     */
    protected $jobName;

    /**
     * Job workload
     *
     * @ORM\Column(type = "text", nullable = true)
     * @var string
     */
    protected $workload;

    /**
     * Start
     * Executes task at this time (optionally with repeat interval).
     *
     * @ORM\Column(type = "datetime", nullable = true)
     * @var \DateTime
     */
    protected $start = null;

    /**
     * How much times to repeat a job
     *
     * @ORM\Column(type = "integer")
     * @var int
     */
    protected $repeatTimes = 1;

    /**
     * Repeat interval
     *
     * @ORM\Column(type = "string", length = 100, nullable = true)
     * @var \string
     */
    protected $repeatInterval = null;


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
        //generate id
        $this->setId(uniqid());

        //start now by default
        $this->setStart(new DateTime);
    }


    /**
     * Set id
     * Sets unique job task id.
     *
     * @param string $id
     * @return \ShiftGearman\Task
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * Get id
     * Returns currently set job task id.
     *
     * @return string | null
     */
    public function getId()
    {
        return $this->id;
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
     * Set run at
     * Sets a datetime this job would be subitted to gearman.
     * Note: a special scheduler worker required.
     *
     * @param \DateTime $datetime
     * @return \ShiftGearman\Task
     */
    public function setStart(DateTime $datetime)
    {
        //convert to UTC if not
        if('UTC' != $datetime->getTimezone()->getName())
            $datetime->setTimezone(new DateTimeZone('UTC'));

        $this->start = $datetime;
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
        return $this->start;
    }


    /**
     * Set repeat
     * A handy shortcut to quickly set task repetition properties.
     *
     * @param int $times
     * @param string $interval
     * @throws \ShiftGearman\Exception\DomainException
     * @return \ShiftGearman\Task
     */
    public function setRepeat($times, $interval)
    {
        //evaluate interval
        try {
            new DateInterval((string) $interval);
        } catch(\Exception $exception) {
            unset($interval);
        }

        if(!is_numeric($times) || !isset($interval))
            throw new DomainException("Task repetition settings invalid");

        $this->runInBackground();
        $this->repeatTimes = $times;
        $this->repeatInterval = $interval;
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
        return $this->repeatTimes;
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
     * Is scheduled
     * A boolean method that returns a flag indicating whether this task
     * should be scheduled. When adding a task this method will be consulted
     * to decide whether task should be put in scheduler queue or passed
     * directly to gearman for instant execution.
     *
     * @return bool
     */
    public function isScheduled()
    {
        $now = new DateTime;
        $now->setTimezone(new DateTimeZone('UTC'));

        $inFuture = ($this->start > $now);
        $recurring = ($this->repeatTimes > 1 && isset($this->repeatInterval));

        $scheduled = false;
        if($inFuture || $recurring)
            $scheduled = true;

        return $scheduled;
    }


    /**
     * Mark repeated once
     * Marks a task as repeated once. This usually gets triggered by
     * scheduler and used to decrement repeat times and extend start datetime
     * with repeat interval.
     *
     * @return \ShiftGearman\Task
     */
    public function repeatOnce()
    {
        if($this->repeatTimes <= 0)
            return $this;

        $this->repeatTimes = $this->repeatTimes - 1;
        $this->start = $this->start->add(
            new DateInterval($this->repeatInterval)
        );

        return $this;
    }














}// class ends here