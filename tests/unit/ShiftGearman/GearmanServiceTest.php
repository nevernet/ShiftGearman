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



}//class ends here