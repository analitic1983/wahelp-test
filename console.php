<?php

namespace console;

use console\commands\CommandInterface;

require __DIR__ . '/common/common.php';

$log = new ConsoleLogger();

if (count($argv)<2) {
    $message = <<<EOF
Usage: php console.php [options]

Options:
  import        Import cvs data, argument is path for csv file
  migrations    Database migrations, only one argument now: up
  notifications Start send notifications, no arguments
  
EOF;
    echo $message;
    exit(0);
}

$commandName = $argv[1];
if (!preg_match('/^[a-zA-Z]+$/', $commandName)) {
    echo "Invalid command name format";
    exit(0);
}

$commandClassName = 'console\\commands\\'.ucfirst($commandName);
$commandArgs = array_slice($argv, 2);
/** @var CommandInterface $command */
$command = new $commandClassName($log, $pdo);
$command->runCommand($commandArgs);


