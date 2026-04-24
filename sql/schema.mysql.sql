-- =========================================================================
-- Conference Scheduler - MySQL schema (database name: conference_schedule)
-- Only use if you switch DATABASE_URL in .env to a MySQL DSN.
-- =========================================================================

CREATE DATABASE IF NOT EXISTS `conference_schedule`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `conference_schedule`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `session_attendee`;
DROP TABLE IF EXISTS `session_speaker`;
DROP TABLE IF EXISTS `attendees`;
DROP TABLE IF EXISTS `speakers`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `conferences`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------- users ----------
CREATE TABLE `users` (
    `id`         INT AUTO_INCREMENT NOT NULL,
    `email`      VARCHAR(180) NOT NULL,
    `roles`      LONGTEXT     NOT NULL,
    `password`   VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name`  VARCHAR(100) NOT NULL,
    `created_at` DATETIME     NOT NULL,
    UNIQUE INDEX `UNIQ_1483A5E9E7927C74` (`email`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- ---------- conferences ----------
CREATE TABLE `conferences` (
    `id`          INT AUTO_INCREMENT NOT NULL,
    `name`        VARCHAR(255) NOT NULL,
    `description` LONGTEXT     DEFAULT NULL,
    `start_date`  DATE         NOT NULL,
    `end_date`    DATE         NOT NULL,
    `location`    VARCHAR(255) NOT NULL,
    `capacity`    INT          NOT NULL,
    `created_at`  DATETIME     NOT NULL,
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- ---------- speakers ----------
CREATE TABLE `speakers` (
    `id`            INT AUTO_INCREMENT NOT NULL,
    `first_name`    VARCHAR(255) NOT NULL,
    `last_name`     VARCHAR(255) NOT NULL,
    `email`         VARCHAR(255) NOT NULL,
    `phone`         VARCHAR(255) DEFAULT NULL,
    `bio`           LONGTEXT     DEFAULT NULL,
    `affiliation`   VARCHAR(255) DEFAULT NULL,
    `profile_image` VARCHAR(255) DEFAULT NULL,
    `created_at`    DATETIME     NOT NULL,
    `conference_id` INT          NOT NULL,
    UNIQUE INDEX `UNIQ_21C01B1EE7927C74` (`email`),
    INDEX `IDX_21C01B1E604B8382` (`conference_id`),
    PRIMARY KEY (`id`),
    CONSTRAINT `FK_21C01B1E604B8382` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- ---------- sessions ----------
CREATE TABLE `sessions` (
    `id`            INT AUTO_INCREMENT NOT NULL,
    `title`         VARCHAR(255) NOT NULL,
    `description`   LONGTEXT     DEFAULT NULL,
    `start_time`    DATETIME     NOT NULL,
    `end_time`      DATETIME     NOT NULL,
    `room`          VARCHAR(255) NOT NULL,
    `track`         VARCHAR(100) NOT NULL,
    `capacity`      INT          NOT NULL,
    `conference_id` INT          NOT NULL,
    INDEX `IDX_9A609D13604B8382` (`conference_id`),
    PRIMARY KEY (`id`),
    CONSTRAINT `FK_9A609D13604B8382` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- ---------- attendees ----------
CREATE TABLE `attendees` (
    `id`            INT AUTO_INCREMENT NOT NULL,
    `first_name`    VARCHAR(255) NOT NULL,
    `last_name`     VARCHAR(255) NOT NULL,
    `email`         VARCHAR(255) NOT NULL,
    `phone`         VARCHAR(20)  DEFAULT NULL,
    `company`       VARCHAR(255) NOT NULL,
    `job_title`     VARCHAR(255) NOT NULL,
    `ticket_type`   VARCHAR(100) NOT NULL,
    `registered_at` DATETIME     NOT NULL,
    `checked_in`    TINYINT(1)   NOT NULL,
    `checked_in_at` DATETIME     DEFAULT NULL,
    `conference_id` INT          NOT NULL,
    UNIQUE INDEX `UNIQ_C8C96B25E7927C74` (`email`),
    INDEX `IDX_C8C96B25604B8382` (`conference_id`),
    PRIMARY KEY (`id`),
    CONSTRAINT `FK_C8C96B25604B8382` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- ---------- session_speaker ----------
CREATE TABLE `session_speaker` (
    `session_id` INT NOT NULL,
    `speaker_id` INT NOT NULL,
    INDEX `IDX_695D593B613FECDF` (`session_id`),
    INDEX `IDX_695D593BD04A0F27` (`speaker_id`),
    PRIMARY KEY (`session_id`, `speaker_id`),
    CONSTRAINT `FK_695D593B613FECDF` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_695D593BD04A0F27` FOREIGN KEY (`speaker_id`) REFERENCES `speakers` (`id`) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- ---------- session_attendee ----------
CREATE TABLE `session_attendee` (
    `session_id`  INT NOT NULL,
    `attendee_id` INT NOT NULL,
    INDEX `IDX_9AFCB50F613FECDF` (`session_id`),
    INDEX `IDX_9AFCB50FBCFD782A` (`attendee_id`),
    PRIMARY KEY (`session_id`, `attendee_id`),
    CONSTRAINT `FK_9AFCB50F613FECDF` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_9AFCB50FBCFD782A` FOREIGN KEY (`attendee_id`) REFERENCES `attendees` (`id`) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
