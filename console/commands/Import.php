<?php

namespace console\commands;

use common\LoggerInterface;
use common\Uuid;
use PDO;

/**
 *  Source data has page numbers. It will be skipped.
 */
class Import implements CommandInterface
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
        $filePath = $args[0];
        if (!file_exists($filePath)) {
            $this->logger->error("Invalid import. File path not exists:" . $filePath);
            throw new \InvalidArgumentException("File not exists");
        }
        $this->cleanOldUsers();
        $this->importFile($filePath);
    }

    protected function importFile(string $filePath): void
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \LogicException("Can`t open file: " . $filePath);
        }
        $totalImported = 0;
        $startTime=time();
        while (($line = fgets($handle)) !== false) {
            $line=trim($line);
            [$num, $name] = explode(' ', $line.' ', 2); // Add space to $line, protect from notice, undefined
            $name = $name ?? '';
            if (!$name) {
                // Skip line
                $this->logger->info("Skip line: ".$line);
                continue;
            }
            $this->addToDb($num, $name);
            $this->logger->info("Imported: ".$num.' '.$name);
            $totalImported++;
        }
        fclose($handle);
        $totalTime=time()-$startTime;
        $this->logger->info("Total imported: ".$totalImported.".  Total time: ".$totalTime." sec.  Speed (records/sec): ".$totalImported/$totalTime).".";
    }

    // Allow now reimport users
    protected function cleanOldUsers(): void
    {
        $this->pdo->exec('DELETE FROM `users`');
    }

    protected function addToDb(string $num, string $name)
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (uuid, num, name) VALUES (:uuid, :num, :name)");

        $uuid = Uuid::v4();
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':num', $num);
        $stmt->bindParam(':name', $name);

        $stmt->execute();
    }
}