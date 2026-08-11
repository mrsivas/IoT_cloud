<?php
declare(strict_types=1);

define(
    'API_REQUEST',
    true
);

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

date_default_timezone_set('Asia/Kolkata');

try {

    $pdo = getDatabaseConnection();

    $deviceUuid = trim(
        (string)($_GET['device'] ?? '')
    );

    $value = trim(
        (string)($_GET['value'] ?? '')
    );


    if ($deviceUuid === '' || $value === '') {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'device and value parameters are required.'
        ]);

        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | Find Device
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT
            id,
            device_uuid,
            device_type,
            device_name,
            status
        FROM devices
        WHERE
            device_uuid = ?
            AND deleted_at IS NULL
        LIMIT 1
    ");

    $stmt->execute([
        $deviceUuid
    ]);

    $device = $stmt->fetch();

    $value = $_GET['value'] ?? null;


if ($value === null) {

    echo json_encode([
        'success' => false,
        'message' => 'Value is required'
    ]);

    exit;

}



switch ($device['device_type']) {


    case 'BOOLEAN':

        if ($value !== '0' && $value !== '1') {

            echo json_encode([
                'success' => false,
                'message' => 'Boolean device accepts only 0 or 1'
            ]);

            exit;

        }

        break;



    case 'INTEGER':

        if (
            filter_var(
                $value,
                FILTER_VALIDATE_INT
            ) === false
        ) {

            echo json_encode([
                'success' => false,
                'message' => 'Integer value required'
            ]);

            exit;

        }

        break;



    case 'DECIMAL':

        if (!is_numeric($value)) {

            echo json_encode([
                'success' => false,
                'message' => 'Decimal value required'
            ]);

            exit;

        }

        break;



    case 'TEXT':

        break;


}

    if (!$device) {

$value = $_GET['value'] ?? null;


if ($value === null) {

    echo json_encode([
        'success' => false,
        'message' => 'Value is required'
    ]);

    exit;

}



switch ($device['device_type']) {


    case 'BOOLEAN':

        if (!in_array($value, ['0','1'], true)) {

            echo json_encode([
                'success' => false,
                'message' => 'Only 0 or 1 allowed'
            ]);

            exit;

        }

        break;



    case 'INTEGER':

        if (filter_var($value, FILTER_VALIDATE_INT) === false) {

            echo json_encode([
                'success' => false,
                'message' => 'Integer value required'
            ]);

            exit;

        }

        break;



    case 'DECIMAL':

        if (!is_numeric($value)) {

            echo json_encode([
                'success' => false,
                'message' => 'Decimal value required'
            ]);

            exit;

        }

        break;



    case 'TEXT':

        // Accept any text

        break;


}

    }


    if ((int)$device['status'] !== 1) {

        http_response_code(403);

        echo json_encode([
            'success' => false,
            'message' => 'Device is inactive.'
        ]);

        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | Store Device Data
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
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
            ?,
            ?,
            ?,
            ?,
            NOW()
        )
    ");

    $stmt->execute([
        $device['id'],
        $value,
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);


    /*
    |--------------------------------------------------------------------------
    | Update Device Status
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE devices
        SET
            `last_value` = ?,
            last_seen = NOW(),
            updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute([
        $value,
        $device['id']
    ]);


    echo json_encode([

        'success' => true,

        'message' => 'Data received successfully.',

        'device' => [

            'id' => (int)$device['id'],

            'uuid' => $device['device_uuid'],

            'name' => $device['device_name']

        ],

        'value' => $value,

        'time' => date(
            'Y-m-d H:i:s'
        )

    ]);


} catch (Throwable $e) {


    http_response_code(500);


    echo json_encode([

        'success' => false,

        'message' => 'Server error.'

    ]);

}