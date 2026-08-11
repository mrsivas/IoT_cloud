-- -----------------------------------------------------
-- Sample Users
-- Password: Admin@123
-- -----------------------------------------------------

INSERT INTO users
(
    uuid,
    full_name,
    email,
    password,
    role,
    status
)
VALUES
(
    '11111111111111111111111111111111',
    'Super Administrator',
    'admin@mceiot.local',
    '$2y$10$W5Q4x8c6jM2z4NwM7H7FQuS2Vv3sYxQ5Qz5m5N5qVY5rM8O2vL5lK',
    1,
    1
),
(
    '22222222222222222222222222222222',
    'Administrator',
    'manager@mceiot.local',
    '$2y$10$W5Q4x8c6jM2z4NwM7H7FQuS2Vv3sYxQ5Qz5m5N5qVY5rM8O2vL5lK',
    2,
    1
),
(
    '33333333333333333333333333333333',
    'Device Owner',
    'owner@mceiot.local',
    '$2y$10$W5Q4x8c6jM2z4NwM7H7FQuS2Vv3sYxQ5Qz5m5N5qVY5rM8O2vL5lK',
    3,
    1
),
(
    '44444444444444444444444444444444',
    'Read Only User',
    'viewer@mceiot.local',
    '$2y$10$W5Q4x8c6jM2z4NwM7H7FQuS2Vv3sYxQ5Qz5m5N5qVY5rM8O2vL5lK',
    4,
    1
);

-- -----------------------------------------------------
-- Sample Devices
-- -----------------------------------------------------

INSERT INTO devices
(
    uuid,
    device_name,
    device_type,
    description,
    status,
    last_value,
    last_updated
)
VALUES
(
    'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
    'Boiler Temperature',
    3,
    'Factory Boiler Temperature Sensor',
    1,
    '78.60',
    NOW()
),
(
    'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
    'Water Tank Level',
    2,
    'Overhead Water Tank',
    1,
    '82',
    NOW()
),
(
    'cccccccccccccccccccccccccccccccc',
    'Pump Status',
    1,
    'Main Water Pump',
    1,
    '1',
    NOW()
);

-- -----------------------------------------------------
-- Device User Mapping
-- -----------------------------------------------------

INSERT INTO device_user_mapping
(
    device_id,
    user_id
)
VALUES
(1,1),
(1,2),
(1,3),

(2,1),
(2,3),

(3,1),
(3,4);

-- -----------------------------------------------------
-- Sample Device Logs
-- -----------------------------------------------------

INSERT INTO device_logs
(
    device_id,
    value,
    ip_address
)
VALUES
(1,'77.20','127.0.0.1'),
(1,'77.80','127.0.0.1'),
(1,'78.60','127.0.0.1'),

(2,'80','127.0.0.1'),
(2,'81','127.0.0.1'),
(2,'82','127.0.0.1'),

(3,'0','127.0.0.1'),
(3,'1','127.0.0.1');


/*

Default Password for all sample users: Admin@123


use this to get password hash for the above password and replace <PASSWORD_HASH> with the generated hash
<?php
echo password_hash('Admin@123', PASSWORD_DEFAULT);

*/