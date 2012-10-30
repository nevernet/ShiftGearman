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
namespace ShiftTest\Integration\ShiftGearman\Scheduler;
use Mockery;
use ShiftTest\TestCase;

use ShiftGearman\Task;


/**
 * Scheduler repository tests
 * This holds integration tests for scheduler queue repository.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Tests
 *
 * @group       integration
 * @group z
 */
class SchedulerRepositoryTest extends TestCase
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
     * Test that we are able to get repository from doctrine.
     * @test
     */
    public function canGetRepositoryFromEntityManager()
    {
        $entityName = 'ShiftGearman\Task';
        $repository = $this->em->getRepository($entityName);
        $this->assertInstanceOf(
            'ShiftGearman\Scheduler\SchedulerRepository',
            $repository
        );
    }


    /**
     * Test that we are able to inject arbitrary entity manager
     * @test
     */
    public function canInjectEntityManager()
    {
        $repository = $this->em->getRepository('ShiftGearman\Task');
        $this->assertEquals($this->em, $repository->getEntityManager());

        $em = Mockery::mock('Doctrine\ORM\EntityManager');
        $repository->setEntityManager($em);
        $this->assertEquals($em, $repository->getEntityManager());
    }


    /**
     * Test that we do throw an exception when saving task that is
     * not scheduled.
     *
     * @test
     * @expectedException \ShiftGearman\Exception\DomainException
     * @expectedExceptionMessage This is not a scheduled task!
     */
    public function throwExceptionWhenSaveUnscheduledTask()
    {
        $task = new Task;
        $repository = $this->em->getRepository('ShiftGearman\Task');
        $repository->save($task);
    }


    /**
     * Test that we do throw an exception on database error when saving
     * tasks.
     *
     * @test
     * @expectedException \ShiftGearman\Exception\DatabaseException
     * @expectedExceptionMessage Database error:
     */
    public function throwExceptionOnDatabaseErrorWhenSavingTask()
    {
        $task = new Task;
        $task->setJobName('test.job');
        $task->setRepeat(2, 'P2D');

        $em = Mockery::mock('Doctrine\ORM\EntityManager');
        $em->shouldReceive('persist')->with($task)->andThrow('Exception');

        $repository = $this->em->getRepository('ShiftGearman\Task');
        $repository->setEntityManager($em);

        //explode!
        $repository->save($task);
    }


    /**
     * Test that we are able to save task.
     * @test
     */
    public function canSaveTask()
    {
        $repository = $this->em->getRepository('ShiftGearman\Task');

        $task = new Task;
        $task->setJobName('test.job');
        $task->setRepeat(2, 'P2D');
        $repository->save($task);

        $taskId = $task->getId();
        $this->assertNotNull($taskId);

        $this->em->clear();
        $resultingTask = $repository->findById($taskId);
        $this->assertInstanceOf('ShiftGearman\Task', $resultingTask);
    }


    /**
     * Test that we do throw an exception on database error when deleting
     * tasks.
     *
     * @test
     * @expectedException \ShiftGearman\Exception\DatabaseException
     * @expectedExceptionMessage Database error:
     */
    public function throwExceptionOnDatabaseErrorWhenDeletingTask()
    {
        $task = new Task;
        $task->setJobName('test.job');
        $task->setRepeat(2, 'P2D');

        $em = Mockery::mock('Doctrine\ORM\EntityManager');
        $em->shouldReceive('remove')->with($task)->andThrow('Exception');

        $repository = $this->em->getRepository('ShiftGearman\Task');
        $repository->setEntityManager($em);

        //explode!
        $repository->delete($task);
    }


    /**
     * Test that we are able to save task.
     * @test
     */
    public function canDeleteTask()
    {
        $repository = $this->em->getRepository('ShiftGearman\Task');

        $task = new Task;
        $task->setJobName('test.job');
        $task->setRepeat(2, 'P2D');
        $repository->save($task);

        $taskId = $task->getId();
        $this->assertNotNull($taskId);

        $this->em->clear();
        $resultingTask = $repository->findById($taskId);
        $this->assertInstanceOf('ShiftGearman\Task', $resultingTask);

        //now delete
        $repository->delete($resultingTask);
        $this->em->clear();

        $this->assertNull($repository->findById($taskId));
    }











}//class ends here