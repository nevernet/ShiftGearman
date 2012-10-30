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

use ShiftGearman\GearmanService;


/**
 * Gearman service tests
 * This holds unit tests for gearman service that does task dispatching.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Tests
 *
 * @group       unit
 */
class GearmanServiceTest extends TestCase
{

    /**
     * Test that we can get gearman service from locator.
     * @test
     */
    public function canGetServiceFromLocator()
    {
        $serviceName = 'ShiftGearman\GearmanService';
        $service = $this->getLocator()->get($serviceName);
        $this->assertInstanceOf($serviceName, $service);
    }


    /**
     * Test that we are able to inject arbitrary config.
     * @test
     */
    public function canInjectConfig()
    {
        $config = array('me-is-config');
        $service = new GearmanService($this->getLocator());
        $service->setConfig($config);
        $this->assertEquals($config, $service->getConfig());
    }


    /**
     * Test that we are able to retrieve default configuration from module
     * if none is injected.
     * @test
     */
    public function canGetDefaultConfigFromModule()
    {
        $service = new GearmanService($this->getLocator());
        $config = $service->getConfig();

        $this->assertTrue(is_array($config));
        $this->assertFalse(empty($config));
    }


    /**
     * Test that we can inject arbitrary scheduler repository.
     * @test
     */
    public function canInjectSchedulerRepository()
    {
        $repository = 'ShiftGearman\Scheduler\SchedulerRepository';
        $repository = Mockery::mock($repository);

        $service = new GearmanService($this->getLocator());
        $service->setSchedulerRepository($repository);
        $this->assertEquals($repository, $service->getSchedulerRepository());
    }


    /**
     * Test that we can get scheduler repository from doctrine if none
     * is injected.
     * @test
     */
    public function canGetSchedulerRepositoryFromDoctrine()
    {
        $service = new GearmanService($this->getLocator());
        $repository = $service->getSchedulerRepository();
        $this->assertInstanceOf(
            'ShiftGearman\Scheduler\SchedulerRepository',
            $repository
        );
    }


    /**
     * Test that we do throw an exception when requesting worker without
     * configuration.
     *
     * @test
     * @expectedException \ShiftGearman\Exception\ConfigurationException
     * @expectedExceptionMessage Worker 'me-not configured' is not configured
     */
    public function throwExceptionWhenRequestingWorkerWithoutConfig()
    {
        $service = new GearmanService($this->getLocator());
        $service->setConfig(array());
        $service->getWorker('me-not configured');
    }

    /**
     * Test that we do throw an exception if worker requests connection that
     * is not configured.
     *
     * @test
     * @expectedException \ShiftGearman\Exception\ConfigurationException
     * @expectedExceptionMessage Connection 'not-configured' requested by worker
     * 'worka' is not configured
     */
    public function throwExceptionWhenWorkerHasNonexistentConnectionConfigured()
    {
        $config = array(
            'connections' => array(),
            'workers' => array(
                'worka' => array(
                    'connection' => 'not-configured',
                    'jobs' => array()
                )
            )
        );


        $service = new GearmanService($this->getLocator());
        $service->setConfig($config);
        $service->getWorker('worka');
    }


    /**
     * Test that we are able to get configured worker by name.
     * @test
     */
    public function canGetWorker()
    {
        //prepare config
        $config = array(
            'connections' => array(
                'default' => array(
                    'timeout' => null,
                    'servers' => array(
                        array('host' => '127.0.0.1', 'port' => 4730)
                    )
                )
            ),
            'workers' => array(
                'worka' => array(
                    'connection' => 'default',
                    'jobs' => array()
                )
            )
        );

        //mock worker
        $worker = Mockery::mock('ShiftGearman\Worker\Worker');
        $worker->shouldReceive('setConfig')
            ->with($config['workers']['worka']);
        $worker->shouldReceive('setConnectionConfig')
            ->with($config['connections']['default']);

        //mock locator
        $locator = Mockery::mock('Zend\Di\Locator');
        $locator->shouldReceive('newInstance')
            ->with('ShiftGearman\Worker\Worker')
            ->andReturn($worker);

        $service = new GearmanService($locator);
        $service->setConfig($config);
        $this->assertEquals($worker, $service->getWorker('worka'));
    }


    /**
     * Test that we do throw an exception when requesting client connection
     * with invalid connection data configured.
     *
     * @test
     * @expectedException \ShiftGearman\Exception\ConfigurationException
     * @expectedExceptionMessage Can't create client 'me-not-configured'.
     * Connection configuration is missing.
     */
    public function throwExceptionWhenRequestingClientWithBadConnectionConfig()
    {
        $service = new GearmanService($this->getLocator());
        $service->setConfig(array());
        $service->getClient('me-not-configured');
    }


    /**
     * Test that we are able to get client connection by name.
     * @test
     */
    public function canGetClientConnection()
    {
        if(!class_exists('GearmanClient'))
            $this->markTestIncomplete();


        //prepare config
        $config = array(
            'connections' => array(
                'default' => array(
                    'timeout' => 1000,
                    'servers' => array(
                        array('host' => '127.0.0.1', 'port' => 4730)
                    )
                )
            ),
        );

        $service = new GearmanService($this->getLocator());
        $client = $service->getClient();
        $this->assertInstanceOf('GearmanClient', $client);
    }


    /**
     * Test that we preserve connected clients within service.
     * @test
     */
    public function preserveConnectedClients()
    {
        if(!class_exists('GearmanClient'))
            $this->markTestIncomplete();


        //prepare config
        $config = array(
            'connections' => array(
                'default' => array(
                    'timeout' => 1000,
                    'servers' => array(
                        array('host' => '127.0.0.1', 'port' => 4730)
                    )
                )
            ),
        );

        $service = new GearmanService($this->getLocator());
        $service->getClient();
        $clients = $service->getClients();

        $this->assertTrue(is_array($clients));
        $this->assertFalse(empty($clients));
    }



}//class ends here