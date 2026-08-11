<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| MCEIoT Configuration
|--------------------------------------------------------------------------
*/

define('APP_NAME', 'MCEIoT');
define('APP_VERSION', '1.0.0');
define('COMPANY_NAME', 'MCEIoT');

define('BASE_URL', 'https://mceiot.com');

define(
    'APP_LOGO',
    'https://www.mookambigai.ac.in/img/logo1.png'
);

/*
|--------------------------------------------------------------------------
| Timezone
|--------------------------------------------------------------------------
*/

date_default_timezone_set('Asia/Kolkata');

/*
|--------------------------------------------------------------------------
| Paths
|--------------------------------------------------------------------------
*/

define('ROOT_PATH', dirname(__DIR__));

define('CONFIG_PATH', ROOT_PATH . '/config');

define('INCLUDE_PATH', ROOT_PATH . '/includes');

define('ASSET_PATH', ROOT_PATH . '/assets');

define('UPLOAD_PATH', ROOT_PATH . '/uploads');

define('API_PATH', ROOT_PATH . '/api');

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

define('RECORDS_PER_PAGE', 10);

/*
|--------------------------------------------------------------------------
| Session
|--------------------------------------------------------------------------
*/

define('SESSION_NAME', 'MCEIOT_SESSION');

define('SESSION_TIMEOUT', 3600);

/*
|--------------------------------------------------------------------------
| Password Policy
|--------------------------------------------------------------------------
*/

define('PASSWORD_MIN_LENGTH', 8);

/*
|--------------------------------------------------------------------------
| Roles
|--------------------------------------------------------------------------
*/

define('ROLE_SUPER_ADMIN', 1);
define('ROLE_ADMIN', 2);
define('ROLE_DEVICE_OWNER', 3);
define('ROLE_READ_ONLY', 4);

const ROLES = [

    ROLE_SUPER_ADMIN => 'Super Admin',

    ROLE_ADMIN => 'Admin',

    ROLE_DEVICE_OWNER => 'Device Owner',

    ROLE_READ_ONLY => 'Read Only'

];

/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

define('STATUS_ACTIVE', 1);

define('STATUS_INACTIVE', 0);

const STATUS = [

    STATUS_ACTIVE => 'Active',

    STATUS_INACTIVE => 'Inactive'

];

/*
|--------------------------------------------------------------------------
| Device Types
|--------------------------------------------------------------------------
*/

define('TYPE_BINARY', 1);

define('TYPE_INTEGER', 2);

define('TYPE_DECIMAL', 3);

const DEVICE_TYPES = [

    TYPE_BINARY => 'Binary',

    TYPE_INTEGER => 'Integer',

    TYPE_DECIMAL => 'Decimal'

];