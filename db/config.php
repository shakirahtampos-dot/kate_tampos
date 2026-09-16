<?php

$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "kates_goodies";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>