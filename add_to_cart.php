```php
<?php

require_once __DIR__ . "/db/config.php";
require_once __DIR__ . "/function.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$product_id = $_POST["product_id"] ?? "";

if (!is_numeric($product_id)) {
    header("Location: index.php");
    exit;
}

$product_id = (int) $product_id;
$user_id = $_SESSION["user_id"];

/*
|--------------------------------------------------------------------------
| CHECK PRODUCT
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare($conn, "SELECT id, stock FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$product) {
    header("Location: index.php");
    exit;
}

if ($product["stock"] <= 0) {
    header("Location: index.php?error=out_of_stock");
    exit;
}

/*
|--------------------------------------------------------------------------
| FIND OR CREATE USER CART
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare($conn, "SELECT id FROM cart WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$cart = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if ($cart) {
    $cart_id = $cart["id"];
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO cart (user_id) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $cart_id = mysqli_insert_id($conn);

    mysqli_stmt_close($stmt);
}

/*
|--------------------------------------------------------------------------
| CHECK IF PRODUCT IS ALREADY IN CART
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, quantity FROM cart_items
     WHERE cart_id = ? AND product_id = ?"
);

mysqli_stmt_bind_param($stmt, "ii", $cart_id, $product_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$cart_item = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if ($cart_item) {
    $new_quantity = $cart_item["quantity"] + 1;

    if ($new_quantity > $product["stock"]) {
        header("Location: index.php?error=stock_limit");
        exit;
    }

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE cart_items SET quantity = ? WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $new_quantity,
        $cart_item["id"]
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

} else {
    $quantity = 1;

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO cart_items (cart_id, product_id, quantity)
         VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iii",
        $cart_id,
        $product_id,
        $quantity
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: cart.php");
exit;

?>
```
