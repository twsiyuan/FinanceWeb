<?php
require("../config.php");
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

if ($method != 'POST' && $method != 'GET' && $method != 'PUT') {
    http_response_code(404);
    die();
}

if ($method == 'POST' || $method == 'PUT') {
    $json = file_get_contents('php://input');
    $req = json_decode($json, true);
    
    if ($req == null){
        $err = '';
        if (json_last_error() == JSON_ERROR_NONE){
            $err = 'Json data is not sent.';
        }else{
            $err = 'Json data parsing error, ' . json_last_error_msg();
        }
        
        header('X-Error: ' . $err);
        http_response_code(400);
        die();
    }else{
        if (isset($req['text']) && $req['text'] == "NULL") $req['text'] = null;
        if (isset($req['location']) && $req['location'] == "NULL") $req['location'] = null;
    }
}

if(($method == 'GET' && !isset($_GET["id"])) || 
   ($method == 'PUT' && !isset($req['id']))){
    header('X-Error: ' . 'Id is not sent');
    http_response_code(400);
    die();
}

$conn = myUtf8Db($db_servername, $db_username, $db_password, $db_name);
if (!$conn) {
    header('X-Error: ' . 'Database connection error, ' . mysqli_connect_error());
    http_response_code(500);
    die();
}

function printJsonObject($id, $arrObj){
    if ($arrObj == null){
        echo 'null';
    }else{
        echo '{';
        echo '"id":' . json_encode($id);

        foreach($arrObj as $key => $value){
            echo ',' . json_encode($key) . ':' . json_encode($value);
        }

        echo '}';
    }
}

function getRecord($conn, $id){
    if ($id != 0){
        $stmt = $conn->prepare('SELECT `from`, `fromAmount`, `fromCurrency`, `to`, `toAmount`, `toCurrency`, `text`, `location`, `date`, `reference` FROM `record` WHERE `id` = ?');
        $stmt->bind_param('i', $id);
        if (!$stmt->execute()){
            header('X-Error: ' . 'Query error, ' . $stmt->error);
            http_response_code(500);
            die();
        }
    
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }else{
        $tz = new DateTimeZone('Europe/London');
        $offset = getTimezone($conn);
        $date = new DateTime(null, $tz);
        $date->modify('+' . $offset . ' hours');
        
        $data = (object)[
            'from' => '現金-錢包',
            'fromAmount' => 0,
            'fromCurrency' => 'TWD',
            
            'to' => '現金-錢包',
            'toAmount' => null,
            'toCurrency' => null,
            
            'text' => '',
            'location' => '',
            'date' => $date->format('Y-m-d'),
            
            'reference' => null,
        ];
    }
    
    return $data;
}

function doPUT($conn, $req){
    $id = $req['id'];
    $data = getRecord($conn, $id);
    if ($data){
        foreach($data as $key => $value){
            if (!isset($req[$key])){
                $req[$key] = $value;
            }
        }
    }else{
        header('X-Error: ' . 'Id is not valid');
        http_response_code(400);
        die();
    }

    $sql = 'UPDATE `record` SET `from` = ?, `fromAmount` = ?, `fromCurrency` = ?, `to` = ?, `toAmount` = ?, `toCurrency` = ?, `text`=?, `location` = ?, `date` = ? WHERE `id` = ?';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssssssi', 
                      $req['from'],
                      $req['fromAmount'],
                      $req['fromCurrency'],
                      $req['to'],
                      $req['toAmount'],
                      $req['toCurrency'],

                      $req['text'],
                      $req['location'],
                      $req['date'],
                      $req['id']);
    
    if (!$stmt->execute()){
        header('X-Error: ' . 'Update error, ' . $stmt->error);
        http_response_code(500);
        die();
    }else{
        echo "null";
    }
}

function doPOST($conn, $req){
    
    $req['from']            = isset($req['from'])? $req['from'] : null;
    $req['fromAmount']      = isset($req['fromAmount'])? $req['fromAmount'] : 0;
    $req['fromCurrency']    = isset($req['fromCurrency'])? $req['fromCurrency'] : null;
    $req['to']              = isset($req['to'])? $req['to'] : null;
    $req['toAmount']        = isset($req['toAmount'])? $req['toAmount'] : null;
    $req['toCurrency']      = isset($req['toCurrency'])? $req['toCurrency'] : null;
    
    $req['text']            = isset($req['text'])? $req['text'] : null;
    $req['location']        = isset($req['location'])? $req['location'] : null;
    $req['date']            = isset($req['date'])? $req['date'] : null;
    
    $sql = 'INSERT INTO `record`(`from`, `fromAmount`, `fromCurrency`, `to`, `toAmount`, `toCurrency`, `text`, `location`, `date`)VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssssss', 
                      $req['from'],
                      $req['fromAmount'],
                      $req['fromCurrency'],
                      $req['to'],
                      $req['toAmount'],
                      $req['toCurrency'],

                      $req['text'],
                      $req['location'],
                      $req['date']);

    if (!$stmt->execute()){
        header('X-Error: ' . 'Insert error, ' . $stmt->error);
        http_response_code(500);
        die();
    }else{
        $id = $stmt->insert_id;
        $data = getRecord($conn, $id);
        printJsonObject($id, $data);
        http_response_code(201);
    }
}

function doGET($conn, $id){
    $data = getRecord($conn, $id);
    printJsonObject($id, $data);
}

switch ($method){
    case 'PUT':
        doPUT($conn, $req);
        break;
    case 'POST':
        session_start();
        
        doPOST($conn, $req);
        
        saveConfigValue($conn, 'add_from', $req['from']);
        saveConfigValue($conn, 'add_to', $req['to']);
        saveConfigValue($conn, 'add_from_currency', $req['fromCurrency']);
        session_write_close();
        
        break;
    case 'GET':
        doGET($conn, $_GET["id"]);        
        break;
}


$conn->close();
?>