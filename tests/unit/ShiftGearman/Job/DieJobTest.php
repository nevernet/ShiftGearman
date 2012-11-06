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
namespace ShiftTest\Unit\ShiftGearman\Job;
use Mockery;
use ShiftTest\TestCase;

use ShiftGearman\Job\DieJob;

/**
 * Die job test
 * This holds unit tests for DieJob
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Tests
 *
 * @group       unit
 */
class DieJobTest extends TestCase
{


    /**
     * Test that we are able to instantiate a job
     * @test
     */
    public function canInstantiateJob()
    {
        $job = new DieJob($this->getLocator());
        $this->assertInstanceOf('ShiftGearman\Job\DieJob', $job);
    }


    /**
     * Test that we can access job name
     * @test
     */
    public function canGetJobName()
    {
        $expected = 'shiftgearman.diejob';
        $job = new DieJob($this->getLocator());
        $this->assertEquals($expected, $job->getName());
    }


    /**
     * Test that we can access job description
     * @test
     */
    public function canGetJobDescription()
    {
        $expected = 'A job for testing different kinds of errors';
        $job = new DieJob($this->getLocator());
        $this->assertEquals($expected, $job->getDescription());
    }





}//class ends here