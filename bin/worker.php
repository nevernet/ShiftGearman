<?php

/*
 * ShiftKernel module application worker.
 * This must be run as server process probably controlled by some sort of
 * supervisor.
 * php migrations.php migrations:diff --configuration=vendor/projectshift/ShiftKernel/migrations/migrations.yml
 */


//set environment
ini_set("display_errors", 1);
chdir(realpath(__DIR__ . '/../../../../'));
require_once 'vendor/autoload.php';

//configure and bootstrap application
$options = array('environment' => 'cli', 'publicDirectory' => 'public');
$runHelper = new ShiftCommon\Application\RunHelper($options);
$runHelper->bootstrap();
$locator = $runHelper->getApplication()->getLocator();

$worker = new Worker;
$worker->startWork();



function doJob($job) {
    $workload = $job->workload();
    echo $workload;
}


class Worker
{
    public function startWork()
    {
        $gearman = new \mwGearman\Worker\Pecl;
        $gearman->addServer('localhost');
        $gearman->connect();
        $gearman->register('myJob', 'doJob');

        echo 'starting work' . PHP_EOL;
        while($gearman->work());
    }


}


