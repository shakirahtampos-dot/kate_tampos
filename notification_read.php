<?php

require_once __DIR__ . "/db/config.php";
require_once __DIR__ . "/function.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user = get_logged_in_user();

$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($order_id > 0) {

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE orders
         SET notification_read = 1
         WHERE id = ?
         AND user_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "ii", $order_id, $user['id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: order_details.php?id=" . $order_id);
exit;