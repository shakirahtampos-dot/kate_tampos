<?php

/*
|--------------------------------------------------------------------------
| REQUIRED FILES
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/db/config.php";
require_once __DIR__ . "/function.php";


/*
|--------------------------------------------------------------------------
| USER ACCESS
|--------------------------------------------------------------------------
*/

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];


/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$cart_item_id = $_POST["cart_item_id"] ?? "";
$action = $_POST["action"] ?? "";


/*
|--------------------------------------------------------------------------
| VALIDATE FORM DATA
|--------------------------------------------------------------------------
*/

if (!is_numeric($cart_item_id)) {
    header("Location: cart.php");
    exit;
}

$cart_item_id = (int) $cart_item_id;

if ($cart_item_id <= 0) {
    header("Location: cart.php");
    exit;
}

if ($action !== "increase" && $action !== "decrease") {
    header("Location: cart.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| GET CART ITEM
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        cart_items.id,
        cart_items.quantity,
        products.stock
    FROM cart_items
    INNER JOIN cart
        ON cart_items.cart_id = cart.id
    INNER JOIN products
        ON cart_items.product_id = products.id
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

$result = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/*
|--------------------------------------------------------------------------
| CHECK IF CART ITEM EXISTS
|--------------------------------------------------------------------------
*/

if (!$item) {
    header("Location: cart.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| CALCULATE NEW QUANTITY
|--------------------------------------------------------------------------
*/

$current_quantity = (int) $item["quantity"];
$stock = (int) $item["stock"];

if ($action === "increase") {

    $new_quantity = $current_quantity + 1;

    if ($new_quantity > $stock) {
        $new_quantity = $stock;
    }

} else {

    $new_quantity = $current_quantity - 1;

    if ($new_quantity < 1) {
        $new_quantity = 1;
    }
}


/*
|--------------------------------------------------------------------------
| UPDATE CART ITEM
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "UPDATE cart_items
     SET quantity = ?
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $new_quantity,
    $cart_item_id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


/*
|--------------------------------------------------------------------------
| RETURN TO CART
|--------------------------------------------------------------------------
*/

header("Location: cart.php");
exit;

?>
