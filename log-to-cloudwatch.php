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

// Get instance ID:
// $url = "http://169.254.169.254/latest/meta-data/instance-id";
// $instanceId = file_get_contents($url);
$instanceId = 'Test Pietro';

$cwClient = new CloudWatchLogsClient($awsCredentials);
// Log group name, will be created if none
$cwGroupName = 'php-app-logs';
// Log stream name, will be created if none
$cwStreamNameInstance = $instanceId;
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
$syslogFormatter = new LineFormatter("%channel%: %level_name%: %message% %context% %extra%",null,false,true);
$infoHandler = new StreamHandler(__DIR__."/".$logFile, Logger::INFO);
$infoHandler->setFormatter($formatter);

$warnHandler = new SyslogHandler($appName, $facility, Logger::WARNING);
$warnHandler->setFormatter($syslogFormatter);

$cwHandlerInstanceInfo->setFormatter($formatter);
$cwHandlerInstanceNotice->setFormatter($formatter);
$cwHandlerInstanceError->setFormatter($formatter);
$cwHandlerAppNotice->setFormatter($formatter);

$logger->pushHandler($warnHandler);
$logger->pushHandler($infoHandler);
$logger->pushHandler($cwHandlerInstanceNotice);
$logger->pushHandler($cwHandlerInstanceError);
$logger->pushHandler($cwHandlerAppNotice);

$logger->info('Initial test of application logging.');
$logger->warn('Test of the warning system logging.');
$logger->notice('Application Auth Event: ',[ 'function'=>'login-action','result'=>'login-success' ]);
$logger->notice('Application Auth Event: ',[ 'function'=>'login-action','result'=>'login-failure' ]);
$logger->error('Application ERROR: System Error');

$now = microtime(true);
$count = 100;

for ($i = 0; $i < $count; $i++) {
    $logger->warn('Benchmark logging');
}

$now = microtime(true) - $now;

echo "$count logs entries generated in $now ms";