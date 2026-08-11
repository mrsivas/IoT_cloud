<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';


header('Content-Type: application/json');


$deviceUuid = $_GET['device'] ?? null;

$range = $_GET['range'] ?? 'All';



if (!$deviceUuid) {

    echo json_encode([
        'success' => false,
        'message' => 'Device UUID required'
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
        device_type

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
| Time Filter
|--------------------------------------------------------------------------
*/

switch ($range) {


    case '1h':

        $interval = '1 HOUR';

        break;



    case '6h':

        $interval = '6 HOUR';

        break;



    case 'today':

        $whereTime = "
            DATE(created_at) = CURDATE()
        ";

        break;



    case '7d':

        $interval = '7 DAY';

        break;


    case '24h':

        $interval = '24 HOUR';

        break;



    default:

       // $interval = '';

        break;

}



if (isset($interval)) {

    $whereTime = "
       AND created_at >= NOW() - INTERVAL $interval
    ";

}
else
{
   $whereTime = "";
}


/*
|--------------------------------------------------------------------------
| Fetch Logs
|--------------------------------------------------------------------------
*/

$sql = "

    SELECT

        value,

        created_at

    FROM device_logs

    WHERE

        device_id = ?


        $whereTime

    ORDER BY created_at ASC

";


$stmt = $pdo->prepare($sql);


$stmt->execute([
    $device['id']
]);


$data = $stmt->fetchAll();



echo json_encode([

    'success' => true,

    'device_type' => $device['device_type'],

    'range' => $range,

    'data' => $data

]);