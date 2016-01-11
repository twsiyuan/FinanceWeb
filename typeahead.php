<?php
require("config.php");
header('Content-Type: application/json; charset=utf-8');

$conn = myUtf8Db($db_servername, $db_username, $db_password, $db_name);
if (!$conn) {
    http_response_code(500);
    die();
}

if (isset($_GET['q']) && isset($_GET['t'])){
    $q = $_GET['q'];
    $t = $_GET['t'];
    
    if ($t == "location" || $t == 'text'){
        echo '[';
        
        $q = $q . '%';
        $field = $t == "location" ? '`location`' : '`text`';
        
        $stmt = $conn->prepare("SELECT DISTINCT " . $field . " AS `r`, MAX(`date`) FROM `record` WHERE " . $field . " LIKE ? GROUP BY " . $field . " ORDER BY MAX(`date`) DESC LIMIT 10;");
        $stmt->bind_param('s', $q);
        $stmt->execute();
        $result = $stmt->get_result();
        $first = true;

        if ($result->num_rows > 0){
            $id = 1;
            while($row = $result->fetch_assoc()){
                if (!$first){
                    echo ',';
                }
                echo '' . json_encode($row['r']) . '';
                $first = false;
                $id = $id + 1;
            }
        }

        $stmt->close();
        
        echo ']';
        
    }else if($t == "recent"){
        echo '{';
        
        $stmt = $conn->prepare("SELECT `from`, `to`, `fromAmount`,`location` FROM `record` WHERE `text` = ? ORDER BY `date` DESC LIMIT 1;");
        $stmt->bind_param('s', $q);
        $stmt->execute();
        $result = $stmt->get_result();

        $row = $result->fetch_assoc();
        if ($row){
            echo '"from":' . json_encode($row['from']) . ',';
            echo '"to":' . json_encode($row['to']) . ',';
            echo '"fromAmount":' . $row['fromAmount'] . ',';
            echo '"location":' . json_encode($row['location']) . '';
        }
        
        $stmt->close();
        
        echo '}';
    }
}else{
    http_response_code(400);
}

$conn->close();
?>