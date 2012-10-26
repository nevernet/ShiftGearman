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
 * @package     ShiftKernel
 * @subpackage  Controller
 */

/**
 * @namespace
 */
namespace ShiftGearman\Controller;

use Zend\Mvc\Controller\ActionController;
use Zend\View\Model\ViewModel;


/**
 * Default home controller
 * This is the default fallback controller that just displays home screen.
 *
 * @category    Projectshift
 * @package     ShiftKernel
 * @subpackage  Controller
 */
class Gearman extends ActionController
{




    /**
     * Gearman index action
     * Here we are going to create an run a gearman job.
     *
     * @return array|void
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        echo '<div style="height: 100px"></div>';
        echo '<div class="container">';
        echo '<h3>Submitting a Job to Gearman</h3>';

        $task = new \ShiftGearman\Task;
        $task->setJobName('shiftgearman.example')
            ->setTaskId(crc32(microtime()))
            ->setWorkload('Pass this data to task')
            ->runInBackground();

        $service = $this->locator->get('ShiftGearman\GearmanService');
        $service->runTask($task);



        echo '</div>';
    }

} //class ends here