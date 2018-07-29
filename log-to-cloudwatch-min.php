<?php
use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;

require __DIR__ . '/config.php';
require __DIR__ . '/vendor/autoload.php';

$logFile = "testapp_local.log";
$appName = "TestApp01";
$facility = "local0";

$cwClient = new CloudWatchLogsClient($awsCredentials);
// Log group name, will be created if none
$cwGroupName = 'php-app-logs';
// Log stream name, will be created if none
$cwStreamNameInstance = 'Test Pietro';
// Instance ID as log stream name
$cwStreamNameApp = "TestAuthenticationApp";
// Days to keep logs, 14 by default
$cwRetentionDays = 5;

$cwHandlerInstanceInfo = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameInstance, $cwRetentionDays, 10000, [ 'application' => 'php-testapp01' ],Logger::INFO);
$cwHandlerInstanceNotice = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameInstance, $cwRetentionDays, 10000, [ 'application' => 'php-testapp01' ],Logger::NOTICE);
$cwHandlerInstanceError = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameInstance, $cwRetentionDays, 10000, [ 'application' => 'php-testapp01' ],Logger::ERROR);
$cwHandlerAppNotice = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameApp, $cwRetentionDays, 10000, [ 'application' => 'php-testapp01' ],Logger::NOTICE);

$logger = new Logger('My PHP Logging');

$formatter = new LineFormatter(null, null, false, true);
$infoHandler = new StreamHandler(__DIR__."/".$logFile, Logger::INFO);
$infoHandler->setFormatter($formatter);

$cwHandlerInstanceInfo->setFormatter($formatter);
$cwHandlerInstanceNotice->setFormatter($formatter);
$cwHandlerInstanceError->setFormatter($formatter);
$cwHandlerAppNotice->setFormatter($formatter);

$logger->pushHandler($infoHandler);
$logger->pushHandler($cwHandlerInstanceInfo);   // comment out this line if you do not want to send INFO level to CloudWatch
$logger->pushHandler($cwHandlerInstanceNotice);
$logger->pushHandler($cwHandlerInstanceError);
$logger->pushHandler($cwHandlerAppNotice);

$logger->info('Initial test of application logging.');
$logger->warn('Test of the warning system logging.');
$logger->notice('Application Auth Event: ',[ 'function'=>'login-action','result'=>'login-success' ]);
$logger->notice('Application Auth Event: ',[ 'function'=>'login-action','result'=>'login-failure' ]);
$logger->error('Application ERROR: System Error');
