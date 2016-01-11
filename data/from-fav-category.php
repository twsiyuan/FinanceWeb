<?php
require("../config.php");

$method = $_SERVER['REQUEST_METHOD'];

if ($method != 'GET') {
    http_response_code(404);
    die();
}

$conn = myUtf8Db($db_servername, $db_username, $db_password, $db_name);
if (!$conn) {
    header('X-Error: ' . 'Database connection error, ' . mysqli_connect_error());
    http_response_code(500);
    die();
}

$data = [];
array_push($data, (object)['name' => '現金-錢包', 'icon' => 'fa-money',]);
array_push($data, (object)['name' => '悠悠卡', 'icon' => 'fa-credit-card',]);
array_push($data, (object)['name' => '兆豐銀行聯名卡', 'icon' => 'fa-credit-card-alt',]);

$stmt = $conn->prepare("SELECT DISTINCT `from` AS `category` FROM `record` GROUP BY `from` ORDER BY MAX(`date`) DESC, MAX(`createTime`) DESC LIMIT 6;");

if (!$stmt->execute()){
    header('X-Error: ' . 'Query error, ' . $stmt->error);
    http_response_code(500);
    die();
}

$result = $stmt->get_result();
while($row = $result->fetch_assoc()){
    $category = new stdClass();
	$category->name = $row['category'];
	$category->icon = null;
    
    array_push($data, $category);
}

$stmt->close();
$conn->close();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
?>