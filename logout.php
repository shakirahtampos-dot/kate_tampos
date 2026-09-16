<?php
require_once __DIR__ . "/function.php";

logout_user();

header("Location: index.php");
exit;
?>