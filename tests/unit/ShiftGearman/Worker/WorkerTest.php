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
namespace ShiftTest\Unit\ShiftGearman\Worker;
use Mockery;
use ShiftTest\TestCase;

use ShiftGearman\Worker\Worker;

/**
 * Worker test
 * This holds unit tests for gearman worker process.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Tests
 *
 * @group       unit
 */
class WorkerTest extends TestCase
{


    /**
     * Test that we are able to instantiate worker.
     * @test
     */
    public function canInstantiateWorker()
    {
        $worker = new Worker($this->getLocator());
        $this->assertInstanceOf('ShiftGearman\Worker\Worker', $worker);
    }


    /**
     * Test that we can retrieve locator from a worker.
     * @test
     */
    public function canGetLocator()
    {
        $locator = Mockery::mock('Zend\Di\Locator');
        $worker = new Worker($locator);
        $this->assertEquals($locator, $worker->getLocator());
    }


    /**
     * Test that we can inject a config.
     * @test
     */
    public function canInjectConfig()
    {
        $config = array('me-is-config');
        $worker = new Worker($this->getLocator());
        $worker->setConfig($config);
        $this->assertEquals($config, $worker->getConfig());
    }


    /**
     * Test that we are can inject connection config.
     * @test
     */
    public function canInjectConnectionConfig()
    {
        $connectionConfig = array('me-is-connection-config');
        $worker = new Worker($this->getLocator());
        $worker->setConnectionConfig($connectionConfig);
        $this->assertEquals($connectionConfig, $worker->getConnectionConfig());
    }


    /**
     * Test that we can add job to worker by name.
     * @test
     */
    public function canAddJobByName()
    {
        $job = 'ShiftGearman\Job\ExampleJob';
        $worker = new Worker($this->getLocator());

        //no jobs initially
        $jobs = $worker->getJobs();
        $this->assertTrue(empty($jobs));

        //now add job by name
        $worker->addJob($job);
        $jobs = $worker->getJobs();
        $this->assertFalse(empty($jobs));
        $this->assertInstanceOf($job, array_shift($jobs));
    }


    /**
     * Test that we do throw an exception when adding nonexistent job to
     * worker by name.
     *
     * @test
     * @expectedException \ShiftGearman\Exception\ConfigurationException
     * @expectedExceptionMessage Can't get NoSuchJob from service locator
     */
    public function throwExceptionWhenAddingNonexistentJobByName()
    {
        $worker = new Worker($this->getLocator());
        $worker->addJob('NoSuchJob');
    }


    /**
     * Test that we are able to add job object to worker
     * @test
     */
    public function canAddJobObjectToWorker()
    {
        $job = 'ShiftGearman\Job\ExampleJob';
        $job = $this->getLocator()->newInstance($job);

        $worker = new Worker($this->getLocator());

        //no jobs initially
        $jobs = $worker->getJobs();
        $this->assertTrue(empty($jobs));

        //now add job by name
        $worker->addJob($job);
        $jobs = $worker->getJobs();
        $this->assertFalse(empty($jobs));
        $this->assertEquals($job, array_shift($jobs));

    }


    /**
     * Test that we do throw proper exception when adding job object that does
     * not extend base abstract job.
     *
     * @test
     * @expectedException \ShiftGearman\Exception\RuntimeException
     * @expectedExceptionmessage Invalid job type
     */
    public function throwExceptionWhenAddingJobObjectOfBadType()
    {
        $job = 'DateTime';
        $job = $this->getLocator()->newInstance($job);

        $worker = new Worker($this->getLocator());
        $worker->addJob($job);
    }


    /**
     * Test that we can add multiple jobs at once. Either by name or as
     * Job object.
     *
     * @test
     */
    public function canAddMultipleJobsAtOnce()
    {
        $job1 = 'ShiftGearman\Job\ExampleJob';
        $job2 = $this->getLocator()->newInstance('ShiftGearman\Job\DieJob');
        $jobs = array($job1, $job2);

        $worker = new Worker($this->getLocator());
        $worker->setJobs($jobs);

        $resultingJobs = $worker->getJobs();
        $this->assertFalse(empty($resultingJobs));
        $this->assertEquals(2, count($resultingJobs));

        //added job by name
        $this->assertInstanceOf(
            'ShiftGearman\Job\ExampleJob',
            array_shift($resultingJobs)
        );

        //added job object
        $this->assertInstanceOf(
            'ShiftGearman\Job\DieJob',
            array_shift($resultingJobs)
        );
    }


    /**
     * Test that we can remove job from worker.
     * @test
     */
    public function canRemoveJob()
    {
        $job = 'ShiftGearman\Job\ExampleJob';
        $worker = new Worker($this->getLocator());
        $worker->addJob($job);

        $jobs = $worker->getJobs();
        $job = array_shift($jobs);
        $worker->removeJob($job);

        //assert removed
        $jobs = $worker->getJobs();
        $this->assertEmpty($jobs);
    }









}//class ends here