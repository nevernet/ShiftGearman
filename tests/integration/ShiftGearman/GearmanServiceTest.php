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
namespace ShiftTest\Integration\ShiftGearman;
use Mockery;
use ShiftTest\TestCase;

use ShiftGearman\GearmanService;
use ShiftGearman\Task;


/**
 * Gearman service tests
 * This holds integration tests for gearman service that does task dispatching.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Tests
 *
 * @group       integration
 */
class GearmanServiceTest extends TestCase
{
    /**
     * Entity manager instance
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;


    /**
     * Set up tests
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        //preserve entity manager
        $this->em = $this->getLocator()->get('Doctrine')->getEntityManager();

        //get db helper
        $helper = $this->getDbHelper();
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
     * Test that we can pass a task for gearman execution.
     * Note: this test requires a worker capable of execution of jobs.
     * @test
     */
    public function canPassTaskForExecutionToGearman()
    {
        //prepare config
        $config = array(
            'connections' => array(
                'high' => array(
                    'timeout' => null,
                    'servers' => array(
                        array('host' => '127.0.0.1', 'port' => 4730)
                    )
                ),
                'normal' => array(
                    'timeout' => null,
                    'servers' => array(
                        array('host' => '127.0.0.1', 'port' => 4730)
                    )
                ),
                'low' => array(
                    'timeout' => null,
                    'servers' => array(
                        array('host' => '127.0.0.1', 'port' => 4730)
                    )
                )
            ),
        );

        $task = new Task;
        $task->setJobName('shiftgearman.example');
        $task->setWorkload('quick');

        $high = clone $task;
        $high->setClientName('high')
            ->priorityHigh();

        $highBackground = clone $task;
        $highBackground->setClientName('high')
            ->priorityHigh()
            ->runInBackground();

        $normal = clone $task;
        $normal->setClientName('normal')
            ->priorityNormal();

        $normalBackground = clone $task;
        $normalBackground->setClientName('normal')
            ->priorityNormal()
            ->runInBackground();

        $low = clone $task;
        $low->setClientName('low')
            ->priorityLow();

        $lowBackground = clone $task;
        $lowBackground->setClientName('low')
            ->priorityLow()
            ->runInBackground();

        $tasks = array(
            $high,
            $highBackground,
            $normal,
            $normalBackground,
            $low,
            $lowBackground
        );

        $service = new GearmanService($this->getLocator());
        $service->setConfig($config);
        $service->add($tasks);
    }


    /**
     * Test that we can put a task into scheduler queue.
     * @test
     */
    public function canScheduleDelayedTasks()
    {
        $task = new Task;
        $task->setJobName('shiftgearman.example');
        $task->setWorkload('quick');
        $task->setRepeat(2, 'P2D');

        $repositoryName = 'ShiftGearman\Scheduler\SchedulerRepository';
        $repository = Mockery::mock($repositoryName);
        $repository->shouldReceive('save')
            ->with($task, true);

        $service = new GearmanService($this->getLocator());
        $service->setSchedulerRepository($repository);
        $service->add($task);
    }


    /**
     * Test that we can run scheduled tasks with a dedicated runner.
     * @test
     */
    public function canRunScheduledTasks()
    {
        $now = new \DateTime;
        $now->setTimezone(new \DateTimeZone('UTC'));
        $now->add(new \DateInterval('PT1S'));

        //once in future
        $once = new Task;
        $once->setId('once');
        $onceId = $once->getId();
        $once->setJobName('shiftgearman.example');
        $once->setWorkload('123');
        $once->setStart($now);
        $this->assertTrue($once->isScheduled());

        //recurring
        $recurring = new Task;
        $recurring->setId('recurring');
        $recurringId = $recurring->getId();
        $recurring->setJobName('shiftgearman.example');
        $recurring->setWorkload('quick');
        $recurring->setStart($now);
        $recurring->setRepeat(2, 'PT2S');

        $service = new GearmanService($this->getLocator());
        $service->add(array($once, $recurring));

        //sleep for some time
        sleep(2);

        //now run scheduled tasks
        $service->runScheduledTasks();
        $this->em->clear();

        //assert once executed and removed
        $this->assertNull($service->getSchedulerRepository()->findById(
            $onceId
        ));

        //assert recurring twice updated
        $recurringUpdated = $service->getSchedulerRepository()->findById(
            $recurringId
        );
        $this->assertEquals(1, $recurringUpdated->getRepeatTimes());

        //now sleep a bit more
        sleep(3);

        //now run scheduled tasks
        $service->runScheduledTasks();
        $this->em->clear();

        //assert recurring repeated for the last time and removed
        $this->assertNull($service->getSchedulerRepository()->findById(
            $recurringId
        ));
    }


    /**
     * Test that we are able to process job exceptions.
     * @test
     * @group zzz
     */
    public function canProcessErrorsAndExceptions()
    {
        $task = new Task;
        $task->setJobName('shiftgearman.example');
        $task->setWorkload('normal');
        $task->priorityHigh();
        $task->runInBackground();

        $service = $this->getLocator()->get('ShiftGearman\GearmanService');
        $service->add($task);
    }


}//class ends here