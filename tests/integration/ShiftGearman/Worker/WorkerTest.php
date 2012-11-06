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
namespace ShiftTest\Integration\ShiftGearman\Worker;
use Mockery;
use ShiftTest\TestCase;

use ShiftGearman\Worker\Worker;

/**
 * Worker test
 * This holds integrational tests for gearman worker process.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Tests
 *
 * @group       integration
 */
class WorkerTest extends TestCase
{

    /**
     * Test that we can inject arbitrary gearman worker.
     * @test
     */
    public function canInjectGearmanWorker()
    {
        $gearmanWorker = new \GearmanWorker;
        $worker = new Worker($this->getLocator());
        $worker->setGearmanWorker($gearmanWorker);
        $this->assertEquals($gearmanWorker, $worker->getGearmanWorker());
    }


    /**
     * Test that we do throw an exception when trying to instantiate gearman
     * worker without configuration.
     *
     * @test
     * @expectedException \ShiftGearman\Exception\ConfigurationException
     * @expectedExceptionMessage Can't start worker. Either configuration or
     */
    public function throwExceptionIfConfigurationMissingWhenCreatingWorker()
    {
        $worker = new Worker($this->getLocator());
        $worker->getGearmanWorker();
    }


    /**
     * Test that we can configure and instantiate gearman worker.
     * @test
     */
    public function canConfigureAndInstantiateGearmanWorker()
    {
        if(!class_exists('GearmanWorker'))
            $this->markTestIncomplete();

        //prepare connection config
        $connectionConfig = array(
            'timeout' => 1000,
            'servers' => array(
                array('host' => '127.0.0.1', 'port' => 4730)
            )
        );

        //prepare config
        $config = array(
            'description' => 'This is an example worker used for testing',
            'connection' => 'default',
            'jobs' => array(
                'ShiftGearman\Job\ExampleJob',
                'ShiftGearman\Job\DieJob',
            )
        );

        //create worker
        $worker = new Worker($this->getLocator());
        $worker->setConfig($config);
        $worker->setConnectionConfig($connectionConfig);

        //worker created
        $gearmanWorker = $worker->getGearmanWorker();
        $this->assertInstanceOf('GearmanWorker', $gearmanWorker);

        //timeout set
        $this->assertEquals(
            $connectionConfig['timeout'],
            $gearmanWorker->timeout()
        );

    }









}//class ends here