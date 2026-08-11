-- ============================================================
-- MCEIoT Database Schema
-- Version: 1.0
-- Database: MySQL 8+
-- ============================================================


CREATE DATABASE IF NOT EXISTS mceiot
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;


USE mceiot;


-- ============================================================
-- Users Table
-- ============================================================

CREATE TABLE users (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    full_name VARCHAR(150) NOT NULL,

    email VARCHAR(150) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    role TINYINT UNSIGNED NOT NULL DEFAULT 3,

    status TINYINT UNSIGNED NOT NULL DEFAULT 1,

    failed_attempts INT UNSIGNED NOT NULL DEFAULT 0,

    locked_until DATETIME NULL,

    last_login DATETIME NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    deleted_at DATETIME NULL,


    INDEX idx_users_email(email),

    INDEX idx_users_role(role),

    INDEX idx_users_status(status)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;



-- ============================================================
-- Devices Table
-- ============================================================

CREATE TABLE devices (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    device_uuid CHAR(36) NOT NULL UNIQUE,

    device_name VARCHAR(150) NOT NULL,

    device_type VARCHAR(100) NOT NULL,

    description TEXT NULL,

    owner_id INT UNSIGNED NOT NULL,

    status TINYINT UNSIGNED NOT NULL DEFAULT 1,

    last_value VARCHAR(255) NULL,

    last_seen DATETIME NULL,


    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    deleted_at DATETIME NULL,


    CONSTRAINT fk_devices_owner

        FOREIGN KEY (owner_id)

        REFERENCES users(id)

        ON DELETE RESTRICT,


    INDEX idx_devices_uuid(device_uuid),

    INDEX idx_devices_owner(owner_id),

    INDEX idx_devices_status(status)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Device Logs Table
-- ============================================================

CREATE TABLE device_logs (

    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    device_id INT UNSIGNED NOT NULL,

    value VARCHAR(255) NOT NULL,

    ip_address VARCHAR(45) NULL,

    user_agent TEXT NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,


    CONSTRAINT fk_device_logs_device

        FOREIGN KEY (device_id)

        REFERENCES devices(id)

        ON DELETE CASCADE,


    INDEX idx_device_logs_device(device_id),

    INDEX idx_device_logs_created(created_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;



-- ============================================================
-- API Access Logs Table
-- ============================================================

CREATE TABLE api_logs (

    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    device_id INT UNSIGNED NULL,

    request_method VARCHAR(10) NOT NULL DEFAULT 'GET',

    request_data TEXT NULL,

    ip_address VARCHAR(45) NULL,

    response_status SMALLINT UNSIGNED NOT NULL DEFAULT 200,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,


    CONSTRAINT fk_api_logs_device

        FOREIGN KEY (device_id)

        REFERENCES devices(id)

        ON DELETE SET NULL,


    INDEX idx_api_logs_device(device_id),

    INDEX idx_api_logs_created(created_at)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;



-- ============================================================
-- System Settings Table
-- ============================================================

CREATE TABLE settings (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    setting_key VARCHAR(100) NOT NULL UNIQUE,

    setting_value TEXT NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Default Roles Reference
-- ============================================================
--
-- Role Values:
--
-- 1 = Super Admin
-- 2 = Admin
-- 3 = Device Owner
-- 4 = Read Only
--
-- ============================================================



-- ============================================================
-- Default Super Admin User
-- ============================================================
--
-- Password:
-- admin@123
--
-- IMPORTANT:
-- Change this password after first login.
--
-- Generated using password_hash()
--
-- ============================================================


INSERT INTO users
(
    full_name,
    email,
    password,
    role,
    status,
    created_at,
    updated_at
)
VALUES
(
    'Super Admin',
    'admin@mceiot.local',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9ll3hW2l8qRjFJ5Z7hQe5S',
    1,
    1,
    NOW(),
    NOW()
);



-- ============================================================
-- Default Application Settings
-- ============================================================


INSERT INTO settings
(
    setting_key,
    setting_value,
    created_at,
    updated_at
)
VALUES

(
    'app_name',
    'MCEIoT',
    NOW(),
    NOW()
),

(
    'timezone',
    'Asia/Kolkata',
    NOW(),
    NOW()
),

(
    'api_authentication',
    'disabled',
    NOW(),
    NOW()
),

(
    'api_method',
    'GET',
    NOW(),
    NOW()
);

-- ============================================================
-- Sample Device Owner User
-- ============================================================
--
-- Password:
-- owner@123
--
-- Change after first login.
--
-- ============================================================


INSERT INTO users
(
    full_name,
    email,
    password,
    role,
    status,
    created_at,
    updated_at
)
VALUES
(
    'Device Owner',
    'owner@mceiot.local',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9ll3hW2l8qRjFJ5Z7hQe5S',
    3,
    1,
    NOW(),
    NOW()
);



-- ============================================================
-- Sample Read Only User
-- ============================================================
--
-- Password:
-- readonly@123
--
-- ============================================================


INSERT INTO users
(
    full_name,
    email,
    password,
    role,
    status,
    created_at,
    updated_at
)
VALUES
(
    'Read Only User',
    'readonly@mceiot.local',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9ll3hW2l8qRjFJ5Z7hQe5S',
    4,
    1,
    NOW(),
    NOW()
);



-- ============================================================
-- Sample Device
-- ============================================================


INSERT INTO devices
(
    device_uuid,
    device_name,
    device_type,
    description,
    owner_id,
    status,
    created_at,
    updated_at
)
VALUES
(
    '550e8400-e29b-41d4-a716-446655440000',
    'Demo Temperature Sensor',
    'ESP32',
    'Sample IoT temperature monitoring device',
    2,
    1,
    NOW(),
    NOW()
);

-- ============================================================
-- Sample Device Logs
-- ============================================================


INSERT INTO device_logs
(
    device_id,
    value,
    ip_address,
    user_agent,
    created_at
)
VALUES

(
    1,
    '25.5',
    '192.168.1.100',
    'ESP32 HTTP Client',
    NOW()
),

(
    1,
    '26.1',
    '192.168.1.100',
    'ESP32 HTTP Client',
    DATE_SUB(NOW(), INTERVAL 5 MINUTE)
),

(
    1,
    '24.8',
    '192.168.1.100',
    'ESP32 HTTP Client',
    DATE_SUB(NOW(), INTERVAL 10 MINUTE)
);



-- ============================================================
-- Additional Index Optimization
-- ============================================================


ALTER TABLE device_logs

ADD INDEX idx_device_time
(
    device_id,
    created_at
);



ALTER TABLE devices

ADD INDEX idx_device_owner_status
(
    owner_id,
    status
);



ALTER TABLE users

ADD INDEX idx_user_active
(
    status,
    deleted_at
);

-- ============================================================
-- Database Maintenance Notes
-- ============================================================
--
-- Soft Delete:
--   Users and devices use deleted_at column.
--
-- API:
--   No API key authentication enabled.
--   Devices send data using:
--
--   GET /api/device.php?device=<UUID>&value=<DATA>
--
-- Security:
--   Change default passwords immediately.
--
-- Backup:
--   Recommended daily mysqldump backup.
--
-- ============================================================


-- ============================================================
-- End of MCEIoT Database Schema
-- ============================================================
