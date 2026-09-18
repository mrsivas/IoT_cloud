SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- Users
-- -----------------------------------------------------

CREATE TABLE `users` (

    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `uuid` CHAR(32) NOT NULL,

    `full_name` VARCHAR(150) NOT NULL,

    `email` VARCHAR(150) NOT NULL,

    `password` VARCHAR(255) NOT NULL,

    `role` TINYINT UNSIGNED NOT NULL DEFAULT 4,

    `status` TINYINT(1) NOT NULL DEFAULT 1,

    `last_login` DATETIME NULL,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    `deleted_at` DATETIME NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `uk_users_uuid` (`uuid`),

    UNIQUE KEY `uk_users_email` (`email`),

    KEY `idx_users_role` (`role`),

    KEY `idx_users_status` (`status`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Devices
-- -----------------------------------------------------

CREATE TABLE `devices` (

    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `uuid` CHAR(32) NOT NULL,

    `device_name` VARCHAR(150) NOT NULL,

    `device_type` TINYINT UNSIGNED NOT NULL,

    `description` VARCHAR(500) NULL,

    `status` TINYINT(1) NOT NULL DEFAULT 1,

    `last_value` VARCHAR(100) NULL,

    `last_updated` DATETIME NULL,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    UNIQUE KEY `uk_devices_uuid` (`uuid`),

    KEY `idx_devices_status` (`status`),

    KEY `idx_devices_type` (`device_type`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Device User Mapping
-- -----------------------------------------------------

CREATE TABLE `device_user_mapping` (

    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `device_id` BIGINT UNSIGNED NOT NULL,

    `user_id` BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `uk_device_user`
        (`device_id`, `user_id`),

    KEY `idx_mapping_device` (`device_id`),

    KEY `idx_mapping_user` (`user_id`),

    CONSTRAINT `fk_mapping_device`
        FOREIGN KEY (`device_id`)
        REFERENCES `devices` (`id`)
        ON DELETE CASCADE,

    CONSTRAINT `fk_mapping_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Device Logs
-- -----------------------------------------------------

CREATE TABLE `device_logs` (

    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `device_id` BIGINT UNSIGNED NOT NULL,

    `value` VARCHAR(100) NOT NULL,

    `ip_address` VARCHAR(45) NULL,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    KEY `idx_logs_device` (`device_id`),

    KEY `idx_logs_created` (`created_at`),

    CONSTRAINT `fk_logs_device`
        FOREIGN KEY (`device_id`)
        REFERENCES `devices` (`id`)
        ON DELETE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;