<?php

/*
 * ShiftKernel module application worker.
 * This must be run as server process probably controlled by some sort of
 * supervisor.
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

$worker = $locator->get('ShiftGearman\GearmanService')->getWorker('example');
$worker->run();

