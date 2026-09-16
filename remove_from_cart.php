<?php

require_once __DIR__ . "/db/config.php";
require_once __DIR__ . "/function.php";


if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];




$cart_item_id = $_GET["id"] ?? "";



if (!is_numeric($cart_item_id)) {
    header("Location: cart.php");
    exit;
}

$cart_item_id = (int) $cart_item_id;

if ($cart_item_id <= 0) {
    header("Location: cart.php");
    exit;
}




$stmt = mysqli_prepare(
    $conn,
    "DELETE cart_items
     FROM cart_items
     INNER JOIN cart
        ON cart_items.cart_id = cart.id
     WHERE cart_items.id = ?
     AND cart.user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $cart_item_id,
    $user_id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);




header("Location: cart.php");
exit;

?>
