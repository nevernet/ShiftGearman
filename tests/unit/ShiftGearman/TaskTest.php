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
 * @subpackage  Tests
 */

/**
 * @namespace
 */
namespace ShiftTest\Unit\ShiftGearman;
use Mockery;
use ShiftTest\TestCase;

use DateTime;
use DateTimeZone;
use DateInterval;
use ShiftGearman\Task;

/**
 * Task test
 * This holds unit tests for gearman task wrapper & entity.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Tests
 *
 * @group       unit
 */
class TaskTest extends TestCase
{

    /**
     * Test that we are able to create a task
     * @test
     */
    public function canCreateTask()
    {
        $task = new Task;
        $this->assertInstanceOf('ShiftGearman\Task', $task);
    }


    /**
     * Test that we create random task id at instantiation.
     * @test
     */
    public function createTaskIdUponInstantiation()
    {
        $task = new Task;
        $this->assertNotNull($task->getId());
    }


    /**
     * Test that we are able to set custom task id.
     * @test
     */
    public function canSetTaskId()
    {
        $customId = '123';
        $task = new Task;
        $task->setId($customId);
        $this->assertEquals($customId, $task->getId());
    }

    /**
     * Test that we are able to set client for this task to be executed with.
     * @test
     */
    public function canSetClientName()
    {
        $task = new Task;
        $this->assertEquals('default', $task->getClientName());

        $client = 'test';
        $task->setClientName($client);
        $this->assertEquals($client, $task->getClientName());
    }


    /**
     * Test that tasks run in foreground by default.
     * @test
     */
    public function runInForegroundByDefault()
    {
        $task = new Task;
        $this->assertFalse($task->isBackground());
    }


    /**
     * Test that we are able to set task to be run in background.
     * @test
     */
    public function canSetTaskToRunInBackground()
    {
        $task = new Task;
        $task->runInBackground();
        $this->assertTrue($task->isBackground());
    }


    /**
     * Test that we run tasks with 'normal' priority by default
     * @test
     */
    public function defaultPriorityIsNormal()
    {
        $task = new Task;
        $this->assertEquals('normal', $task->getPriority());
    }


    /**
     * Test that we can set task to run with high priority
     * @test
     */
    public function canSetHighPriority()
    {
        $task = new Task;
        $this->assertNotEquals('high', $task->getPriority());

        $task->priorityHigh();
        $this->assertEquals('high', $task->getPriority());
    }


    /**
     * Test that we can set task to run with normal priority
     * @test
     */
    public function canSetNormalPriority()
    {
        $task = new Task;
        $task->priorityHigh();
        $this->assertNotEquals('normal', $task->getPriority());

        $task->priorityNormal();
        $this->assertEquals('normal', $task->getPriority());
    }


    /**
     * Test that we can set task to run with low priority
     * @test
     */
    public function canSetLowPriority()
    {
        $task = new Task;
        $task->priorityHigh();
        $this->assertNotEquals('low', $task->getPriority());

        $task->priorityLow();
        $this->assertEquals('low', $task->getPriority());
    }


    /**
     * Test that we are able to set job name for a task.
     * @test
     */
    public function canSetJobName()
    {
        $job = 'example.job.name';
        $task = new Task;
        $task->setJobName($job);
        $this->assertEquals($job, $task->getJobName());
    }


    /**
     * Test that we can set workload for the job.
     * @test
     */
    public function testCanSetWorkload()
    {
        $workload = 'some-workload-for-the-job';
        $task = new Task;
        $task->setWorkload($workload);
        $this->assertEquals($workload, $task->getWorkload());
    }


    /**
     * Test that we can set start date for the task.
     * @test
     */
    public function canSetStartDate()
    {
        $start = new DateTime;
        $this->assertEquals('UTC', $start->getTimezone()->getName());

        $task = new Task;
        $task->setStart($start);
        $this->assertEquals($start, $task->getStart());
    }


    /**
     * Test that we do convert datetime to UTC timezone if its not.
     * @test
     */
    public function convertStartDateToUtcIfNot()
    {
        $start = new DateTime;
        $start->setTimezone(new DateTimeZone('Europe/Moscow'));
        $this->assertNotEquals('UTC', $start->getTimezone()->getName());

        $task = new Task;
        $task->setStart($start);

        $resultingStart = $task->getStart();
        $this->assertEquals('UTC', $resultingStart->getTimezone()->getName());
    }


    /**
     * Test that we can set task repetition options.
     * @test
     */
    public function canSetRepeat()
    {
        $times = 2;
        $interval = 'P2Y4DT6H8M';
        $task = new Task;
        $task->setRepeat($times, $interval);

        $this->assertEquals($times, $task->getRepeatTimes());
        $this->assertEquals($interval, $task->getRepeatInterval());
    }


    /**
     * Test that we throw an exception if impossible to schedule task for
     * repetition.
     *
     * @test
     * @expectedException \ShiftGearman\Exception\DomainException
     * @expectedExceptionMessage Task repetition settings invalid
     */
    public function throwExceptionIfBadRepetitionSettingsProvided()
    {
        $times = 2;
        $interval = 'no-a-valid-interval';
        $task = new Task;
        $task->setRepeat($times, $interval);
    }


    /**
     * Test that tasks are executed instantly by default.
     * @test
     */
    public function executeInstantlyByDefault()
    {
        $task = new Task;
        $this->assertFalse($task->isScheduled());
    }


    /**
     * Test that we put tasks in future to scheduler queue.
     * @test
     */
    public function futureTasksAreScheduled()
    {
        $task = new Task;
        $start = new DateTime;
        $start->add(new DateInterval('P2D')); //in two days
        $task->setStart($start);

        $this->assertTrue($task->isScheduled());
    }


    /**
     * Test that recurring tasks are put to scheduler queue.
     * @test
     */
    public function recurringTasksAreScheduled()
    {
        $task = new Task;
        $start = new DateTime;
        $start->sub(new DateInterval('P2D')); //two days in past
        $task->setStart($start);
        $task->setRepeat(2, 'P1D');
        $this->assertTrue($task->isScheduled());
    }


    /**
     * Test that we can mark task as repeated by decrementing repeat times
     * and adding interval to start date.
     * @test
     */
    public function canMarkTaskAsRepeatedOnce()
    {
        $now = new DateTime;
        $now->setTimezone(new DateTimeZone('UTC'));

        $task = new Task;
        $task->setStart($now)->setRepeat(2, 'P4D');

        $task->repeatOnce();
        $this->assertEquals(1, $task->getRepeatTimes());
    }




}//class ends here