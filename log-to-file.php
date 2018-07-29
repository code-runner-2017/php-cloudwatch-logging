<?php
require "vendor/autoload.php";

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

$logFile = "testapp_local.log";

$logger = new Logger('TestApp01');
$formatter = new LineFormatter(null, null, false, true);
$infoHandler = new StreamHandler(__DIR__."/".$logFile, Logger::INFO);
$infoHandler->setFormatter($formatter);
$logger->pushHandler($infoHandler);
$logger->info('Initial test of application logging.');