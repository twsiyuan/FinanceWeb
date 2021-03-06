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

$stmt = $conn->prepare("SELECT DISTINCT `category`, `type` FROM `category`;");

if (!$stmt->execute()){
    header('X-Error: ' . 'Query error, ' . $stmt->error);
    http_response_code(500);
    die();
}

$data = [];
$result = $stmt->get_result();
while($row = $result->fetch_assoc()){
	$category = new stdClass();
	$category->name = $row['category'];
	$category->type = $row['type'];
	
    array_push($data, $category);
}

$stmt->close();
$conn->close();

echo json_encode($data);
?>