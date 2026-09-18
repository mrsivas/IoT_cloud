<?php

declare(strict_types=1);

header('Content-Type: application/json');


require_once '../config/database.php';


$pdo = getDatabaseConnection();



$deviceUuid = $_POST['device'] ?? $_GET['device'] ?? '';



if ($deviceUuid === '') {

    echo json_encode([
        'success' => false,
        'message' => 'Device UUID required'
    ]);

    exit;

}



/*
|--------------------------------------------------------------------------
| Get Device
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

    SELECT *

    FROM devices

    WHERE device_uuid = ?

    AND deleted_at IS NULL

    LIMIT 1

");


$stmt->execute([
    $deviceUuid
]);


$device = $stmt->fetch();



if (!$device) {

    echo json_encode([
        'success' => false,
        'message' => 'Device not found'
    ]);

    exit;

}



/*
|--------------------------------------------------------------------------
| Check Device Type
|--------------------------------------------------------------------------
*/

if ($device['device_type'] !== 'BOOLEAN') {

    echo json_encode([
        'success' => false,
        'message' => 'Only BOOLEAN devices can be toggled'
    ]);

    exit;

}



/*
|--------------------------------------------------------------------------
| Toggle Value
|--------------------------------------------------------------------------
*/

$newValue =
    ($device['last_value'] == '1')
    ? '0'
    : '1';



/*
|--------------------------------------------------------------------------
| Update Device
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

    UPDATE devices

    SET

        `last_value` = ?,

        `last_seen` = NOW()

    WHERE id = ?

");


$stmt->execute([

    $newValue,

    $device['id']

]);



/*
|--------------------------------------------------------------------------
| Insert Log
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

    INSERT INTO device_logs

    (
        device_id,
        value,
        created_at
    )

    VALUES

    (
        ?,
        ?,
        NOW()
    )

");


$stmt->execute([

    $device['id'],

    $newValue

]);



echo json_encode([

    'success' => true,

    'value' => $newValue,

    'status' =>
        $newValue == '1'
        ? 'ON'
        : 'OFF'

]);