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



    public function schedule(Task $task, $andFlush = true)
    {

    }


    public function save(Task $task, $andFlush = true)
    {

    }


    public function delete(Task $task, $andFlush = true)
    {

    }



    public function findById()
    {

    }


    public function getDueTasks()
    {
        
    }






}// class ends here