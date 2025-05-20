<?php

namespace console\commands;

use common\LoggerInterface;
use common\Uuid;
use models\Notification;
use models\User;
use PDO;

/**
 * We separate the commands into creation and the actual sending.
 * This way, we ensure guaranteed delivery.
 * If a failure occurs during the sending process, it can be restarted.
 */
class Notifications implements CommandInterface
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
            $this->logger->error("Invalid arguments used with notifications command. Empty args.");
            throw new \InvalidArgumentException("Empty args");
        }
        $notificationCommand = $args[0];
        switch ($notificationCommand) {
            case 'create':
                $this->createNotifications();
                break;
            case 'send':
                $this->sendNotifications();
                break;
            default:
                $error = "Unknown notification argument: " . $notificationCommand;
                $this->logger->error($error);
                throw new \InvalidArgumentException($error);
        }
    }

    protected function createNotifications(): void
    {
        // Clean old if exists
        $this->pdo->exec("DELETE FROM notifications");
        $stmt = $this->pdo->query("SELECT * FROM users");

        $this->logger->info("Creating notifications...");
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $totalCount = 0;
        while ($user = $stmt->fetch()) {
            $this->createNotification($user);
            $totalCount++;
        }
        $this->logger->info("Notifications created, ready for send. Total count: " . $totalCount);
    }

    protected function createNotification(User $user): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO notifications (uuid, user_uuid, status) VALUES (:uuid, :user_uuid, 'new')");
        $uuid = Uuid::v4();
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':user_uuid', $user->uuid);
        $stmt->execute();
    }

    protected function sendNotifications(): void
    {
        $stmt = $this->pdo->query("SELECT * FROM notifications WHERE notifications.status in ('new', 'failed') ");

        $stmt->setFetchMode(PDO::FETCH_CLASS, Notification::class);
        $this->logger->info("Sending notifications...");
        $successCount = 0;
        $failedCount = 0;
        while ($notification = $stmt->fetch()) {
            if ($this->sendNotification($notification)) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }
        $this->logger->info("Finished. Success count: " . $successCount . " Failed count: " . $failedCount);
    }

    protected function sendNotification(Notification $notification): bool
    {
        try {
            $this->sendStubNotification($notification);
        } catch (\Exception $exception) {
            $this->changeNotificationStatus($notification, 'failed');
            $this->logger->error("Failed to send notification to user: " . $notification->user_uuid);
            return false;
        }
        $this->changeNotificationStatus($notification, 'completed');
        return true;
    }

    protected function changeNotificationStatus(Notification $notification, string $status): void
    {
        $stmt = $this->pdo->prepare("UPDATE notifications SET status=:status WHERE uuid=:uuid");
        $stmt->bindParam(':uuid', $notification->uuid);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
    }

    protected function sendStubNotification(Notification $notification): void
    {
        if (rand(0, 500) == 1) {
            // Fatal emulator
            throw new \LogicException("Send failed");
        }
        // Do nothing
        sleep(0);
    }


}