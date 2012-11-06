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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ShiftGearman\Task;

/**
 * Run scheduled command
 * This is used to be run periodically either by a worker process or as cron
 * task and passes all the tasks scheduled to gearman for execution.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Console
 */
class RunScheduled extends Command
{
    /**
     * Configure
     * Sets console command properties.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('run-scheduled');
        $this->setDescription('Run scheduled tasks with gearman.');
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
        //output header
        $message = 'Processing scheduled tasks';
        $output->writeln('');
        $message = "<info>$message</info>";
        $output->writeln($message);

        //attempt execution
        try
        {
            $runHelper = $GLOBALS['runHelper'];
            $locator = $runHelper->getApplication()->getLocator();
            $service = $locator->get('ShiftGearman\GearmanService');
            $service->runScheduledTasks();
        }
        catch(\Exception $exception)
        {
            $error = $exception->getmessage();
            $message = "<error>Failed running scheduled tasks: ";
            $message .= "$error</error>";

            $output->writeln('');
            $output->writeln($message);
            $output->writeln('');
            return $output;
        }

        //return on success
        $message = '<comment>Success</comment>';
        $output->writeln($message);
        $output->writeln('');
        return $output;
    }


} //class ends here