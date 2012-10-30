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
 * @subpackage  Scheduler
 */

/**
 * @namespace
 */
namespace ShiftGearman\Scheduler;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use ShiftGearman\Task;
use ShiftGearman\Exception\DomainException;
use ShiftGearman\Exception\DatabaseException;

/**
 * Scheduler repository
 * This repository handles delayed tasks queue and retrieval.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Scheduler
 */
class SchedulerRepository extends EntityRepository
{
    /**
     * Entity manager instance
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_em;


    /**
     * Set entity manager
     * Allows you to inject arbitrary entity manager to be used within
     * repository. This is quite useful for testing.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return SchedulerRepository
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_em = $entityManager;
        return $this;
    }


    /**
     * Get entity manager
     * Returns entity manager currently set for this repository.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_em;
    }


    /**
     * Save
     * Save changes to task. May optionally flush transaction.
     *
     * @param \ShiftGearman\Task $task
     * @param bool $andFlush
     * @throws \ShiftGearman\Exception\DomainException
     * @throws \ShiftGearman\Exception\DatabaseException
     * @return \ShiftGearman\Task
     */
    public function save(Task $task, $andFlush = true)
    {
        if(!$task->isScheduled())
            throw new DomainException('This is not a scheduled task!');

        try
        {
            $this->_em->persist($task);

            if($andFlush)
                $this->_em->flush();
        }
        catch(\Exception $exception)
        {
            $message = 'Database error: ' . $exception->getMessage();
            throw new DatabaseException($message);
        }

        //return saved task on success
        return $task;
    }


    /**
     * Delete
     * Removes task from scheduler queue. May optionally flush transaction.
     *
     * @param \ShiftGearman\Task $task
     * @param bool $andFlush
     * @throws \ShiftGearman\Exception\DatabaseException
     * @return void
     */
    public function delete(Task $task, $andFlush = true)
    {
        try
        {
            $deleteResult = $this->_em->remove($task);

            if($andFlush)
                $this->_em->flush();
        }
        catch(\Exception $exception)
        {
            $message = 'Database error: ' . $exception->getMessage();
            throw new DatabaseException($message);
        }

        //return saved task on success
        return $deleteResult;
    }


    /**
     * Find by id
     * Returns task by its numeric id.
     *
     * @param int $id
     * @return \ShiftGearman\Task | null
     */
    public function findById($id)
    {
        return $this->findOneBy(array('id' => $id));
    }


    /**
     * Get due tasks
     * Returns an array of tasks that are to passed to gearman for execution.
     * @return array
     */
    public function getDueTasks()
    {
        $now = new \DateTime;
        $now->setTimezone(new \DateTimeZone('UTC'));

        $builder = $this->_em->createQueryBuilder();
        $builder->add('select', 'task');
        $builder->add('from', 'ShiftGearman\Task task');
        $builder->add('where', 'task.start <= :thisVeryMoment');
        $builder->setParameter('thisVeryMoment', $now);


        return $builder->getQuery()->getResult();
    }






}// class ends here