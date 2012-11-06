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

use ShiftGearman\Job\ExampleJob;

/**
 * Example job test
 * This holds unit tests for AbstractJob and its concrete implementation of
 * ExampleJob
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Tests
 *
 * @group       unit
 */
class ExampleJobTest extends TestCase
{


    /**
     * Test that we are able to instantiate a job
     * @test
     */
    public function canInstantiateJob()
    {
        $job = new ExampleJob($this->getLocator());
        $this->assertInstanceOf('ShiftGearman\Job\ExampleJob', $job);
    }


    /**
     * Test that we can access job name
     * @test
     */
    public function canGetJobName()
    {
        $expected = 'shiftgearman.example';
        $job = new ExampleJob($this->getLocator());
        $this->assertEquals($expected, $job->getName());
    }


    /**
     * Test that we can access job description
     * @test
     */
    public function canGetJobDescription()
    {
        $expected = 'An example job used for testing';
        $job = new ExampleJob($this->getLocator());
        $this->assertEquals($expected, $job->getDescription());
    }





}//class ends here