<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define(
    'BASE_URL',
    ''
);

define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDE_PATH', ROOT_PATH . '/includes');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');

require_once CONFIG_PATH . '/database.php';
require_once INCLUDE_PATH . '/constants.php';
require_once INCLUDE_PATH . '/functions.php';
require_once INCLUDE_PATH . '/auth.php';



/*
|--------------------------------------------------------------------------
| PDO
|--------------------------------------------------------------------------
*/

$pdo = getDatabaseConnection();

/*
|--------------------------------------------------------------------------
| Default Timezone
|--------------------------------------------------------------------------
*/

date_default_timezone_set('Asia/Kolkata');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

$currentUser = $_SESSION['user'] ?? null;

/*
|--------------------------------------------------------------------------
| Flash Messages
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['flash'])) {
    $_SESSION['flash'] = null;
}

if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

/*
|--------------------------------------------------------------------------
| Security Headers
|--------------------------------------------------------------------------
*/

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

/*
|--------------------------------------------------------------------------
| Helper Constants
|--------------------------------------------------------------------------
*/


if (!defined('RECORDS_PER_PAGE')) {

    define(
        'RECORDS_PER_PAGE',
        20
    );

}


if (!defined('PASSWORD_MIN_LENGTH')) {
    define('PASSWORD_MIN_LENGTH', 8);
}

if (!defined('STATUS_ACTIVE')) {
    define('STATUS_ACTIVE', 1);
}

if (!defined('STATUS_INACTIVE')) {
    define('STATUS_INACTIVE', 0);
}

if (!defined('ROLE_SUPER_ADMIN')) {
    define('ROLE_SUPER_ADMIN', 1);
}

if (!defined('ROLE_ADMIN')) {
    define('ROLE_ADMIN', 2);
}

if (!defined('ROLE_DEVICE_OWNER')) {
    define('ROLE_DEVICE_OWNER', 3);
}

if (!defined('ROLE_READ_ONLY')) {
    define('ROLE_READ_ONLY', 4);
}
/*
|--------------------------------------------------------------------------
| Global Application Settings
|--------------------------------------------------------------------------
*/

if (!defined('APP_NAME')) {
    define('APP_NAME', 'MCEIoT');
}

if (!defined('APP_VERSION')) {
    define('APP_VERSION', '1.0.0');
}

if (!defined('APP_URL')) {
    define('APP_URL', '');
}

if (!defined('UPLOAD_DEVICE_PATH')) {
    define(
        'UPLOAD_DEVICE_PATH',
        UPLOAD_PATH . '/devices'
    );
}

if (!is_dir(UPLOAD_DEVICE_PATH)) {

    @mkdir(
        UPLOAD_DEVICE_PATH,
        0755,
        true
    );

}

/*
|--------------------------------------------------------------------------
| CSRF Token
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_token'])) {

    $_SESSION['csrf_token'] = bin2hex(
        random_bytes(32)
    );

}

$csrfToken = $_SESSION['csrf_token'];

/*
|--------------------------------------------------------------------------
| Current Request
|--------------------------------------------------------------------------
*/

$currentPage = basename(
    $_SERVER['PHP_SELF']
);

$requestMethod = strtoupper(
    $_SERVER['REQUEST_METHOD']
);

/*
|--------------------------------------------------------------------------
| Logged-in User
|--------------------------------------------------------------------------
*/

$currentUserId = $currentUser['id'] ?? null;

$currentUserRole = $currentUser['role'] ?? null;

$currentUserName = $currentUser['full_name'] ?? '';

/*
|--------------------------------------------------------------------------
| Navigation
|--------------------------------------------------------------------------
*/

$menu = [

    [
        'title' => 'Dashboard',
        'icon'  => 'cil-speedometer',
        'url'   => ROOT_PATH . '/index.php'
    ],

    [
        'title' => 'Devices',
        'icon'  => 'cil-devices',
        'url'   => ROOT_PATH . '/devices/index.php'
    ]

];

if (
    $currentUserRole === ROLE_SUPER_ADMIN
    ||
    $currentUserRole === ROLE_ADMIN
) {

    $menu[] = [

        'title' => 'Users',
        'icon'  => 'cil-user',
        'url'   => ROOT_PATH . '/users/index.php'

    ];

}
/*
|--------------------------------------------------------------------------
| Common View Data
|--------------------------------------------------------------------------
*/

$viewData = [

    'appName'        => APP_NAME,
    'appVersion'     => APP_VERSION,
    'pageTitle'      => $pageTitle ?? APP_NAME,
    'currentUser'    => $currentUser,
    'currentUserId'  => $currentUserId,
    'currentUserRole'=> $currentUserRole,
    'currentUserName'=> $currentUserName,
    'csrfToken'      => $csrfToken,
    'menu'           => $menu

];

/*
|--------------------------------------------------------------------------
| Session Timeout
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['last_activity'])) {

    $inactiveTime = time() - (int)$_SESSION['last_activity'];

    if ($inactiveTime > (60 * 60)) {

        session_unset();

        session_destroy();

        session_start();

        $_SESSION['flash'] = [

            'type' => 'warning',
            'message' => 'Your session has expired. Please login again.'

        ];

        header('Location: ' . ROOT_PATH . '/login.php');

        exit;

    }

}

$_SESSION['last_activity'] = time();

/*
|--------------------------------------------------------------------------
| Cache Control
|--------------------------------------------------------------------------
*/

if (!defined('API_REQUEST')) {

    header(
        'Cache-Control: no-store, no-cache, must-revalidate'
    );

    header(
        'Pragma: no-cache'
    );

}
header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');

/*
|--------------------------------------------------------------------------
| Request Validation
|--------------------------------------------------------------------------
*/

if (
    $requestMethod === 'POST'
    &&
    isset($_POST['csrf_token'])
) {

    if (
        !hash_equals(
            $_SESSION['csrf_token'],
            (string)$_POST['csrf_token']
        )
    ) {

        die('Invalid CSRF token.');

    }

}
/*
|--------------------------------------------------------------------------
| Error Reporting
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);

ini_set('display_errors', '1');

ini_set('log_errors', '1');

/*
|--------------------------------------------------------------------------
| Common Utility Variables
|--------------------------------------------------------------------------
*/

$isLoggedIn = !empty($currentUser);

$isSuperAdmin = ($currentUserRole === ROLE_SUPER_ADMIN);

$isAdmin = ($currentUserRole === ROLE_ADMIN);

$isDeviceOwner = ($currentUserRole === ROLE_DEVICE_OWNER);

$isReadOnly = ($currentUserRole === ROLE_READ_ONLY);

/*
|--------------------------------------------------------------------------
| Breadcrumb
|--------------------------------------------------------------------------
*/

$breadcrumbs = [];

$breadcrumbs[] = [
    'title' => 'Home',
    'url'   => ROOT_PATH . '/index.php'
];

switch ($currentPage) {

    case 'index.php':

        if (strpos($_SERVER['PHP_SELF'], '/users/') !== false) {

            $breadcrumbs[] = [
                'title' => 'Users',
                'url' => ROOT_PATH . '/users/index.php'
            ];

        } elseif (strpos($_SERVER['PHP_SELF'], '/devices/') !== false) {

            $breadcrumbs[] = [
                'title' => 'Devices',
                'url' => ROOT_PATH . '/devices/index.php'
            ];

        }

        break;

    case 'change-password.php':

        $breadcrumbs[] = [
            'title' => 'Change Password',
            'url' => ROOT_PATH . '/change-password.php'
        ];

        break;

}

/*
|--------------------------------------------------------------------------
| Auto Cleanup
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['flash_keep'])) {

    $_SESSION['flash_keep'] = false;

}

if (
    $_SESSION['flash_keep'] === false
    &&
    isset($_SESSION['old'])
) {

    unset($_SESSION['old']);

}
/*
|--------------------------------------------------------------------------
| Environment
|--------------------------------------------------------------------------
*/

if (!defined('APP_ENV')) {
    define('APP_ENV', 'production');
}

if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}

/*
|--------------------------------------------------------------------------
| Default Pagination
|--------------------------------------------------------------------------
*/

if (!defined('DEFAULT_PER_PAGE')) {
    define('DEFAULT_PER_PAGE', 20);
}

/*
|--------------------------------------------------------------------------
| Application Settings
|--------------------------------------------------------------------------
*/

$appSettings = [

    'company_name' => APP_NAME,
    'timezone'     => 'Asia/Kolkata',
    'version'      => APP_VERSION,
    'environment'  => APP_ENV

];

/*
|--------------------------------------------------------------------------
| Default Response Headers
|--------------------------------------------------------------------------
*/

header('X-Powered-By: MCEIoT');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

/*
|--------------------------------------------------------------------------
| Request Information
|--------------------------------------------------------------------------
*/

$requestUri = $_SERVER['REQUEST_URI'] ?? '';

$basePath = dirname($_SERVER['SCRIPT_NAME']);

$scriptName = basename($_SERVER['SCRIPT_NAME']);

$isAjaxRequest =
    (
        $_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''
    ) === 'XMLHttpRequest';

/*
|--------------------------------------------------------------------------
| Application Ready
|--------------------------------------------------------------------------
*/

$GLOBALS['pdo'] = $pdo;
$GLOBALS['currentUser'] = $currentUser;
$GLOBALS['viewData'] = $viewData;
$GLOBALS['appSettings'] = $appSettings;
$GLOBALS['breadcrumbs'] = $breadcrumbs;
/*
|--------------------------------------------------------------------------
| Shutdown Handler
|--------------------------------------------------------------------------
|
| Perform any cleanup work before PHP finishes execution.
|
*/

register_shutdown_function(function (): void {

    if (session_status() === PHP_SESSION_ACTIVE) {

        session_write_close();

    }

});

/*
|--------------------------------------------------------------------------
| Bootstrap Complete
|--------------------------------------------------------------------------
*/

return true;