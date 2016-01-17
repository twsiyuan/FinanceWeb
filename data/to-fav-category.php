<?php
require("../config.php");
header('Content-Type: application/json; charset=utf-8');

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
array_push($data, (object)[ 'name' => '交通費', 'icon' => 'fa-train', ]);
array_push($data, (object)[ 'name' => '早餐', 'icon' => 'fa-cutlery', ]);
array_push($data, (object)[ 'name' => '午餐', 'icon' => 'fa-cutlery', ]);
array_push($data, (object)[ 'name' => '下午茶', 'icon' => 'fa-cutlery', ]);
array_push($data, (object)[ 'name' => '晚餐', 'icon' => 'fa-glass', ]);
array_push($data, (object)[ 'name' => '點心', 'icon' => 'fa-meh-o', ]);

$stmt = $conn->prepare("SELECT DISTINCT `to` AS `category` FROM `record` GROUP BY `from` ORDER BY MAX(`date`) DESC, MAX(`createTime`) DESC LIMIT 8;");

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

echo json_encode($data);
?>