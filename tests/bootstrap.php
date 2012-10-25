<?php
chdir(dirname(__DIR__) . '/../../../');

/**
 * Set up application environment options
 */
$options = array();
$options['environment']         = 'testing';
$options['publicDirectory']     = 'public';

/**
 * Run the application
 */
require_once 'vendor/autoload.php';
$runHelper = new ShiftCommon\Application\RunHelper($options);
