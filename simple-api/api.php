<?php
header("Content-Type: application/json");

$host = "localhost";
$username = "mceioe21_iot";
$password = "mceioe21_iot";
$database = "mceioe21_iot";


$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);


if ($conn->connect_error) {

    die(
        "Database connection failed"
    );

}




$device = $_GET['device'] ?? '';

$value = $_GET['value'] ?? '';



if ($device == '' || $value == '') {


    echo json_encode([

        "status" => "error",

        "message" => "Missing data"

    ]);


    exit;

}



$stmt = $conn->prepare("

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



$stmt->bind_param(

    "ss",

    $device,

    $value

);



if ($stmt->execute()) {


    echo json_encode([

        "status" => "success",

        "message" => "Data received"

    ]);


}
else {


    echo json_encode([

        "status" => "error",

        "message" => "Insert failed"

    ]);


}



$stmt->close();

$conn->close();


?>
