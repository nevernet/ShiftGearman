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

/**
 * Run command
 * This is used to start workers from console. Just give it a name of
 * configured worker.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Console
 */
class Run extends Command
{
    /**
     * Configure
     * Sets console command properties.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('run');
        $this->setDescription('Start configured worker process by name.');

        //worker name
        $this->addOption(
            'worker',
            'w',
            InputOption::VALUE_REQUIRED,
            'Worker name from configuration'
        );
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
        $worker = $input->getOption('worker');
        if(!$worker)
        {
            $message = '<error>Please provide worker name.</error>';
            $output->writeln($message);
            $output->writeln('');
            return $output;
        }



        $message = "<info>Running worker '$worker'</info>";
        $output->writeln('');
        $output->writeln($message);
        $output->writeln('');

        //get worker
        $runHelper = $GLOBALS['runHelper'];
        $locator = $runHelper->getApplication()->getLocator();
        try
        {
            $service = $locator->get('ShiftGearman\GearmanService');
            $worker = $service->getWorker($worker);
        }
        catch(\Exception $exception)
        {
            $message = $exception->getMessage();
            $message = "<error>$message</error>";
            $output->writeln($message);
            $output->writeln('');
            return $output;
        }

        //run worker
        $worker->run();

        //return
        return $output;
    }


} //class ends here