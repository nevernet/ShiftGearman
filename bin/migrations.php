<?php

/*
 * ShiftMedia module migrations tool
 * Generates migrations for current module only.
 * To generate migrations do:
 *
 * php migrations.php migrations:diff --configuration=vendor/projectshift/ShiftMedia/migrations/migrations.xml
 */


//set environment
ini_set("display_errors", 1);
chdir(realpath(__DIR__ . '/../../../../'));
require_once 'vendor/autoload.php';

//configure and bootstrap application
$options = array('environment' => 'cli', 'publicDirectory' => 'public');
$runHelper = new ShiftCommon\Application\RunHelper($options);
//load migrations config
$runHelper->addConfigPath(realpath(__DIR__ . '/../config/migrations/migrations.config.php'));
$runHelper->bootstrap();


//create CLI application
$name = 'ShiftMedia module migrations CLI';
$version = Doctrine\DBAL\Version::VERSION;
$cli = new \Symfony\Component\Console\Application($name, $version);
$cli->setCatchExceptions(true);


//Bootstrap console helpers
$helpers = array();
try
{
    //Get service locator
    $locator = $runHelper->getApplication()->getLocator();

    //Dialog helper
    $helpers['dialog'] = new \Symfony\Component\Console\Helper\DialogHelper();

    //Dbal connection helper
    $dbalConnection = $locator->get('DoctrineDbalContainer')->getConnection(getenv('CONN') ?: null);
    $helpers['db'] = new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($dbalConnection);

    //Entity manager helper
    $entityManager = $locator->get('DoctrineOrmContainer')->getEntityManager(getenv('EM') ?: null);
    $helpers['em'] = new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager);


}
catch(\Exception $exception)
{
    $cli->renderException(
        $exception,
        new \Symfony\Component\Console\Output\ConsoleOutput()
    );
}

//Set helpers to cli
$helperSet = new \Symfony\Component\Console\Helper\HelperSet($helpers);
$cli->setHelperSet($helperSet);

//Migrations commands
$cli->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand());
$cli->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand());
$cli->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand());
$cli->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand());
$cli->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand());
$cli->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand());

//Now run cli
$cli->run();
