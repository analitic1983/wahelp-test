<?php

declare(strict_types=1);

require "bootstrap.php";

use common\Config;

global $pdo; // Replace with Db class

$dsn = 'mysql:host='.Config::getEnv('MYSQL_HOST').';dbname='.Config::getEnv('MYSQL_DB');
$pdo = new PDO($dsn, Config::getEnv('MYSQL_USER'), Config::getEnv('MYSQL_PASS'));
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
