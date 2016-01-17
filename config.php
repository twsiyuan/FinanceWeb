<?php
$title = "Finance";

$db_servername = "finance.cd0esl2fpzqa.ap-northeast-1.rds.amazonaws.com";
$db_username = "app";
$db_password = "app2016";
$db_name = "finance";

$started_at = microtime(true);

ini_set('default_charset','utf-8');
header('Content-type: text/html; charset=utf-8');

function myUtf8Db($host, $user, $pw, $db){
    $conn = mysqli_connect($host, $user, $pw, $db);
    return $conn;
}

function getConfigValue($conn, $key, $default){
    $return = $default;
    $stmt = $conn->prepare("SELECT `value` FROM `config` WHERE `key` = ?");
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $return = $row["value"];
    }
    $stmt->close();
    return $return;
}

function saveConfigValue($conn, $key, $value){
    $stmt = $conn->prepare("INSERT INTO `config`(`key`, `value`)VALUES(?, ?) ON DUPLICATE KEY UPDATE `value`=?");
    $stmt->bind_param('sss', $key, $value, $value);
    $stmt->execute();
}

function getTimezone($conn){
    return $timezone = intval(getConfigValue($conn, "timezone", +8));
}

function getCurrencies($conn){
    $options = array();
    $stmt = $conn->prepare("SELECT * FROM `currency` ORDER BY `code`;");
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $code = $row["code"];
        $name = $row["name"];
        
        $option = (object) ['text' => ($name . ' ' . $code), 'value' => $code];
        array_push($options, $option);
    }
    $stmt->close();
    return $options;
}

?>