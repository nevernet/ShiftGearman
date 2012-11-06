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
 * @group z
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

    }


    /**
     * Test that we do throw an exception when adding nonexistent job to
     * worker by name.
     * @test
     */
    public function throwExceptionWhenAddingNonexistentJobByName()
    {

    }


    /**
     * Test that we are able to add job object to worker
     * @test
     */
    public function canAddJobObjectToWorker()
    {

    }


    /**
     * Test that we do throw proper exception when adding job object that does
     * not extend base abstract job.
     * @test
     */
    public function throwExceptionWhenAddingJobObjectOfBadType()
    {

    }


    /**
     * Test that we can add multiple jobs at once. Either by name or as
     * Job object.
     * @test
     */
    public function canAddMultipleJobsAtOnce()
    {

    }


    /**
     * Test that we can remove job from worker.
     * @test
     */
    public function canRemoveJob()
    {

    }









}//class ends here