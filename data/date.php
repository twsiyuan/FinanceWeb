<?php
require("../config.php");
header('Content-Type: application/json; charset=utf-8');

$conn = myUtf8Db($db_servername, $db_username, $db_password, $db_name);
if (!$conn) {
    header('X-Error: ' . 'Database connection error, ' . mysqli_connect_error());
    http_response_code(500);
    die();
}

echo '{';

$tz = new DateTimeZone('Europe/London');
$offset = getTimezone($conn);
$server_date = new DateTime(null, $tz);
$app_date = new DateTime(null, $tz);
$app_date->modify('+' . $offset . ' hours');

echo '"current_date":"' . $app_date->format('Y-m-d') . '"';
echo ',"current_time":"' . $app_date->format('H:i:s') . '"';
echo ',"server_date":"' . $server_date->format('Y-m-d') . '"';
echo ',"server_time":"' . $server_date->format('H:i:s') . '"';

echo '}';

$conn->close();
?>