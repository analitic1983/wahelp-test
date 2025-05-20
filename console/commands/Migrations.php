<?php

namespace console\commands;

use common\LoggerInterface;
use PDO;

class Migrations implements CommandInterface
{
    protected LoggerInterface $logger;
    protected PDO $pdo;

    public function __construct(LoggerInterface $logger, PDO $pdo)
    {
        $this->logger = $logger;
        $this->pdo = $pdo;
    }

    public function runCommand(array $args): void
    {
        if (count($args) < 1) {
            $this->logger->error("Invalid arguments used with migrations command. Empty args.");
            throw new \InvalidArgumentException("Empty args");
        }
        if ($args[0] == 'up') {
            $this->up();
        }
        // Down and etc...
    }

    public function up(): void
    {
        $initMigration = file_get_contents(__DIR__ . '/../../database/migrations/init.sql');
        $this->pdo->exec($initMigration);
        $this->logger->info("Migrations done.");
    }
}