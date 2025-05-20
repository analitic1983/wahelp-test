CREATE TABLE `users`
(
    `uuid`   CHAR(36)     NOT NULL,
    `num` DECIMAL(13)  NOT NULL,
    `name`   VARCHAR(255) NOT NULL,
    PRIMARY KEY (`uuid`)
);

ALTER TABLE `test`.`users`
    ADD INDEX `Num` (`num` ASC) VISIBLE;

CREATE TABLE `notifications`
(
    `uuid`      CHAR(36) NOT NULL,
    `user_uuid` CHAR(36) NOT NULL,
    `status`    ENUM('new', 'failed', 'completed') NOT NULL,
    PRIMARY KEY (`uuid`),
    INDEX       `fk_notifications_1_idx` (`user_uuid` ASC) VISIBLE,
    CONSTRAINT `fk_notifications_1`
        FOREIGN KEY (`user_uuid`)
            REFERENCES `test`.`users` (`uuid`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);