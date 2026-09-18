<?php

declare(strict_types=1);

header('Content-Type: application/json');


require_once '../config/database.php';

$pdo = getDatabaseConnection();


$deviceUuid = $_GET['device'] ?? '';



if ($deviceUuid === '') {

    echo json_encode([
        'success' => false,
        'message' => 'Device UUID required'
    ]);

    exit;

}



$stmt = $pdo->prepare("SELECT * FROM devices WHERE device_uuid = ? AND deleted_at IS NULL LIMIT 1");


$stmt->execute([
    $deviceUuid
]);


$device = $stmt->fetch(PDO::FETCH_ASSOC);



if (!$device) {

    echo json_encode([
        'success' => false,
        'message' => 'Device not found'
    ]);

    exit;

}



$status = null;


if ($device['device_type'] === 'BOOLEAN') {


    $status =
        ($device['last_value'] == '1')
        ? 'ON'
        : 'OFF';


}



echo json_encode([

    'success' => true,

    'device_name' => $device['device_name'],

    'device_type' => $device['device_type'],

    'value' => $device['last_value'],

    'status' => $status,

    'last_seen' => $device['last_seen']

]);

?>