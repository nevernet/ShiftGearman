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
 * @subpackage  Console
 */

/**
 * @namespace
 */
namespace ShiftGearman\Console\Command;

use ShiftGearman\Module;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Scheduler process
 * This command starts a scheduler process that monitors scheduled tasks queue
 * and with a configured interval passes tasks for execution to gearman.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Console
 */
class SchedulerProcess extends Command
{
    /**
     * Gearman module configuration
     * @var array
     */
    protected $config;


    /**
     * Set config
     * Allows you to inject arbitrary configuration into command.
     *
     * @param array $config
     * @return \ShiftGearman\Console\Command\SchedulerProcess
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }


    /**
     * Get config
     * Checks if we already have an injected config and returns that.
     * Otherwise retrieves configuration from gearman module.
     *
     * @return array
     */
    public function getConfig()
    {
        if(!$this->config)
            $this->config = Module::getModuleConfig();
        return $this->config;
    }



    /**
     * Configure
     * Sets console command properties.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('scheduler-process');
        $this->setDescription('Starts scheduler process.');
    }


    /**
     * Execute
     * Runs the command and produces output.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hr = '----------------------------------------';

        $config = $this->getConfig()->scheduler;
        if(!$config)
        {
            $message = "Scheduler configuration is missing.";
            $output->writeln($message);
            $output->writeln("<error>$message</error>");
        }

        $config = $config->toArray();
        $timeout = $config['timeoutSeconds'];
        $iterations = $config['maximumIterations'];

        $message = 'Running scheduler process';
        $output->writeln('');
        $message = "<info>$message</info>";
        $output->writeln($message);
        $output->writeln("<info>$hr</info>");

        $iteration = 1;
        $line = 1;
        $perLine = 40;
        while($iteration <= $iterations)
        {
            $runHelper = $GLOBALS['runHelper'];
            $locator = $runHelper->getApplication()->getLocator();
            $service = $locator->get('ShiftGearman\GearmanService');

            try
            {
                $service->runScheduledTasks();

                //mak progress
                $output->write('.');
                if(($iteration / $perLine) >= $line)
                {
                    $line++;
                    $output->writeln('');
                }

            }
            catch(\Exception $exception)
            {
                $error = $exception->getMessage();
                $message = 'Running scheduled tasks failed: ' . $error;
                $output->writeln($message);
                $output->writeln("<error>$message</error>");
            }

            $iteration++;
            sleep($timeout);
        }

        //exit after maximum iterations
        $total = $iteration - 1;

        $message = "Maximum number of iterations reached ($total)";
        $output->writeln('');
        $output->writeln("<info>$message</info>");
        $output->writeln('');
        exit;
    }


} //class ends here