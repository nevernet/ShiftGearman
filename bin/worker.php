<?php
//set environment
ini_set("display_errors", 1);
chdir(realpath(__DIR__ . '/../../../../'));
require_once 'vendor/autoload.php';

//configure and bootstrap application
$options = array('environment' => 'cli', 'publicDirectory' => 'public');
$runHelper = new ShiftCommon\Application\RunHelper($options);
$runHelper->bootstrap();

//configure console app
$application = new Symfony\Component\Console\Application;
$application->add(new ShiftGearman\Console\Command\Run);
$application->add(new ShiftGearman\Console\Command\RunScheduled);
$application->add(new ShiftGearman\Console\Command\Info);
$application->run();
