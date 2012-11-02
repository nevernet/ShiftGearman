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
use ShiftGearman\Exception\ConsoleException;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Info command
 * This command prints out info on worker and it capabilities if you give it
 * a worker name. Otherwise displays info for all configured workers.
 *
 * @category    Projectshift
 * @package     ShiftGearman
 * @subpackage  Console
 */
class Info extends Command
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
     * @return \ShiftGearman\Console\Command\Info
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
        $this->setName('info');
        $this->setDescription(
            'Display workers configuration and capabilities.'
        );

        //worker name
        $this->addOption(
            'worker',
            'w',
            InputOption::VALUE_REQUIRED,
            'Worker name from configuration'
        );
    }


    /**
     * Print worker
     * Does worker data printing to output.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array $worker
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    protected function printWorker(OutputInterface $output, $worker)
    {
        $hr = '-------------------------------------------------------';
        $output->writeln("<comment>$worker[name]<comment>");
        $output->writeln("<comment>$hr<comment>");

        //description
        $description = 'no description';
        if(!empty($worker['description']))
            $description = $worker['description'];
        $output->writeln("Description: <info>$description</info>");

        //connection name
        $connection = $worker['connection'];
        $output->writeln("Connection: <info>$connection[name]</info>");

        //connection timeout
        if(!$connection['timeout'])
            $connection['timeout'] = 'no';
        $output->writeln(
            "Timeout: <info>$connection[timeout]</info>"
        );

        //connection servers
        $output->writeln(
            "Servers: <info>$connection[servers]</info>"
        );

        $output->writeln('');


        $style = new OutputFormatterStyle('cyan');
        $output->getFormatter()->setStyle('jobs', $style);
        $output->writeln('<jobs>Worker capabilities:</jobs>');
        $output->writeln('');

        $jobs = $worker['jobs'];
        if(empty($jobs))
            $output->writeln('<error>This worker has no jobs</error>');

        foreach($jobs as $job)
        {
            $name = $job['name'];
            $class = $job['class'];
            $description = 'no description';
            if(!empty($job['description']))
                $description = $job['description'];

            $output->writeln("<info>$name</info>");
            $output->writeln("$description ($class)");
            $output->writeln('');
        }

        $output->writeln('');
        $output->writeln('');
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


        //display info for worker
        if($worker)
        {
            $message = "<info>Displaying info for worker '$worker'</info>";
            $output->writeln('');
            $output->writeln($message);
            $output->writeln('');

            $info = $this->getWorkerInfo($worker);
            if(empty($info))
            {
                $error = "<error>Worker '$worker' is not configured.</error>";
                $output->writeln($error);
                return $output;
            }

            $this->printWorker($output, $info);
        }

        //display info for all workers
        if(!$worker)
        {
            $info = $this->getAllWorkersInfo();

            //output title
            $message = "<info>Displaying info for all workers</info>";
            $output->writeln('');
            $output->writeln($message);
            $output->writeln('');

            //no workers configured?
            if(empty($info))
            {
                $message = '<error>You have no configured workers...</error>';
                $output->writeln($message);
                return;
            }

            //otherwise display info for workers
            foreach($info as $worker)
                $this->printWorker($output, $worker);
        }

        //return
        return $output;
    }


    /**
     * Get all workers info
     * Returns info for all configured workers.
     *
     * @return array
     */
    protected function getAllWorkersInfo()
    {
        $config = $this->getConfig()->toArray();
        $workers = array_keys($config['workers']);

        $info = array();
        foreach($workers as $worker)
        {
            $workerInfo = $this->getWorkerInfo($worker);
            if(!empty($workerInfo))
                $info[$worker] = $workerInfo;
        }

        return $info;
    }


    /**
     * Get worker info
     * Returns info on worker and its capabilities.
     *
     * @param string $workerName
     * @return array | void
     */
    protected function getWorkerInfo($workerName)
    {
        $config = $this->getConfig()->toArray();
        $workers = $config['workers'];
        $connections = $config['connections'];

        if(!isset($workers[$workerName]))
            return;

        $worker = $workers[$workerName];

        //name
        $info = array();
        $info['name'] = $workerName;

        //description
        $description = null;
        if(isset($worker['description']))
            $description = $worker['description'];
        $info['description'] = $description;

        //connection
        $connection = null;
        if(isset($connections[$worker['connection']]))
            $connection = $connections[$worker['connection']];

        if($connection)
        {
            $servers = $connection['servers'];

            $connection = array(
                'name' => $worker['connection'],
                'timeout' => $connection['timeout'],
                'servers' => null,
            );

            foreach($servers as $server)
            {
                $connection['servers'] .= $server['host'] . ':';
                $connection['servers'] .= $server['port'] . ' ';
            }

            $info['connection'] = $connection;
            unset($connection);
        }

        //capabilities
        $jobs = null;
        if(isset($worker['jobs']))
        {
            $runHelper = $GLOBALS['runHelper'];
            $locator = $runHelper->getApplication()->getLocator();
            foreach($worker['jobs'] as $className)
            {
                try
                {
                    $job = $locator->newInstance($className);
                }
                catch(\Exception $e)
                {
                    $message = "Worker '$workerName' has a registered job ";
                    $message .= "'$className' that can not be auto loaded";
                    throw new ConsoleException($message);
                }


                $jobName = $job->getName();
                $jobs[] = array(
                    'name' => $jobName,
                    'description' => $job->getDescription(),
                    'class' => $className
                );
            }
        }
        $info['jobs'] = $jobs;

        //return
        return $info;
    }



}//class ends here