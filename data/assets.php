<?php
require("../config.php");

$method = $_SERVER['REQUEST_METHOD'];

if ($method != 'GET') {
    http_response_code(404);
    die();
}


$date = null;
if (isset($_GET["date"])){
    $date = strtotime($_GET["date"] . '+ 1 days');
    if (!$date){
        http_response_code(400);
        die();
    }
    
    $date = date('Y-m-d', $date);
}

$conn = myUtf8Db($db_servername, $db_username, $db_password, $db_name);
if (!$conn) {
    header('X-Error: ' . 'Database connection error, ' . mysqli_connect_error());
    http_response_code(500);
    die();
}

$stmt = $conn->prepare('CALL `查詢 020 資產總覽`(?)');
$stmt->bind_param('s', $date);
if (!$stmt->execute()){
    header('X-Error: ' . 'Query error, ' . $stmt->error);
    http_response_code(500);
    die();
}

$result = $stmt->get_result();
$first = true;

header('Content-Type: application/json; charset=utf-8');
echo '[';
while($data = $result->fetch_assoc()){
    if ($data['priority'] == 0){
        if (!$first){
            echo ',';
        }else{
            $first = false;
        }

        echo '{';
        echo '"type":' . json_encode($data['typeName']) . ',';
        echo '"category":' . json_encode($data['category']) . ',';
        echo '"currency":' . json_encode($data['currency']) . ',';
        echo '"amount":' . json_encode($data['amount']);
        echo '}';
    }
}
echo ']';

$conn->close();
?>