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



}//class ends here