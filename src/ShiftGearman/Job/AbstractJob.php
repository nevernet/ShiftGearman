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
 * @subpackage  Job
 */

/**
 * @namespace
 */
namespace ShiftGearman\Job;

use GearmanJob;
use Zend\Di\Locator;

/**
 * Abstract job
 * All your gearman jobs must extend from this base job to be properly
 * included into worker as its functions.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Job
 */
abstract class AbstractJob
{

    /**
     * Name
     * The job will be available to workers by that name.
     * @var string
     */
    protected $name;

    /**
     * Job description
     * @var string
     */
    protected $description = '';

    /**
     * Service locator instance
     * @var \Zend\Di\Locator
     */
    protected $locator;


    /**
     * Construct
     * Creates an instance of job.
     *
     * @param \Zend\Di\Locator $locator
     * @return void
     */
    public function __construct(Locator $locator)
    {
        //set locator
        $this->locator = $locator;

        //initialize extending job
        $this->init();
    }


    /**
     * Initialize job
     * Configures job parameters.
     *
     * @return \ShiftGearman\Job\AbstractJob
     */
    abstract public function init();


    /**
     * Execute job
     * This will get triggered by workers to execute job. Implement
     * in your custom job classes.
     *
     * @param \GearmanJob $job
     * @return mixed
     */
    abstract public function execute(GearmanJob $job);


    /**
     * Set name
     * Sets name to be used within gearman to address this job.
     *
     * @param $name
     * @return \ShiftGearman\Job\AbstractJob
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     * Get name
     * Returns currently set job name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set description
     * Sets description for this job.
     *
     * @param string $description
     * @return \ShiftGearman\Job\AbstractJob
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }


    /**
     * Get name
     * Returns currently set job name.
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


} //class ends here