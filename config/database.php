<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
|
| Update these values to match your cPanel MySQL database.
|
*/

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'mceioe21_iot');
define('DB_USER', 'mceioe21_iot');
define('DB_PASS', 'mceioe21_iot');

define('DB_CHARSET', 'utf8mb4');

/*
|--------------------------------------------------------------------------
| PDO Connection
|--------------------------------------------------------------------------
*/

function getDatabaseConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
        try {

        $pdo = new PDO(
            $dsn,
            DB_USER,
            DB_PASS,
            $options
        );

        $pdo->exec("
            SET time_zone = '+05:30'
        ");

        $pdo->exec("
            SET NAMES " . DB_CHARSET
        );

        return $pdo;

    } catch (PDOException $e) {

        http_response_code(500);

        exit(
            'Database connection failed.'
        );

    }

}
/*
|--------------------------------------------------------------------------
| End of File
|--------------------------------------------------------------------------
|
| Example:
|
| $pdo = getDatabaseConnection();
|
*/
