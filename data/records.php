<?php
require("../config.php");

$method = $_SERVER['REQUEST_METHOD'];

if ($method != 'GET') {
    http_response_code(404);
    die();
}

if($method == 'GET' && !isset($_GET["start_date"])){
    http_response_code(400);
    die();
}

$start_date = strtotime($_GET["start_date"]);
if (!$start_date){
    http_response_code(400);
    die();
}

if (isset($_GET["end_date"])){
    $end_date = strtotime($_GET["end_date"]);
    if (!$end_date){
        http_response_code(400);
        die();
    }
}else{
    $end_date = strtotime($_GET["start_date"] . ' + 1 days');
}

$start_date = date('Y-m-d', $start_date);
$end_date = date('Y-m-d', $end_date);

function printJsonObject($id, $arrObj){
    if ($arrObj == null){
        echo 'null';
    }else{
        echo '{';
        echo '"id":' . json_encode($id);

        foreach($arrObj as $key => $value){
            if ($key != 'id'){
                echo ',' . json_encode($key) . ':' . json_encode($value);
            }
        }

        echo '}';
    }
}

$conn = myUtf8Db($db_servername, $db_username, $db_password, $db_name);
if (!$conn) {
    header('X-Error: ' . 'Database connection error, ' . mysqli_connect_error());
    http_response_code(500);
    die();
}

$stmt = $conn->prepare('SELECT `id`, `from`, `fromAmount`, `fromCurrency`, `to`, `toAmount`, `toCurrency`, `text`, `location`, `date`, `reference` FROM `record` WHERE `date` >= ? AND `date` < ?');
$stmt->bind_param('ss', $start_date, $end_date);
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
    if (!$first){
        echo ',';
    }else{
        $first = false;
    }
    
    printJsonObject($data['id'], $data);
}
echo ']';

$conn->close();
?>