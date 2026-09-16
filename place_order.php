<?php
require_once __DIR__ . "/db/config.php";
require_once __DIR__ . "/function.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$delivery_address = trim($_POST["delivery_address"] ?? "");

if ($delivery_address === "") {
    header("Location: checkout.php?error=address_required");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = mysqli_prepare($conn, "SELECT id FROM cart WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cart = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$cart) {
    header("Location: cart.php");
    exit;
}

$cart_id = $cart["id"];

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        cart_items.product_id,
        cart_items.quantity,
        products.price,
        products.stock
    FROM cart_items
    INNER JOIN products
        ON cart_items.product_id = products.id
    WHERE cart_items.cart_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $cart_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$cart_items = [];
$total = 0;

while ($item = mysqli_fetch_assoc($result)) {

    if ($item["quantity"] > $item["stock"]) {
        mysqli_stmt_close($stmt);
        header("Location: cart.php?error=stock_limit");
        exit;
    }

    $subtotal = $item["price"] * $item["quantity"];
    $total += $subtotal;

    $cart_items[] = $item;
}

mysqli_stmt_close($stmt);

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

mysqli_begin_transaction($conn);

try {

$delivery_address = trim($_POST["delivery_address"] ?? "");

if ($delivery_address === "") {
    header("Location: checkout.php?error=address_required");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO orders
    (user_id, delivery_address, total_amount, status)
    VALUES (?, ?, ?, 'Pending')"
);

mysqli_stmt_bind_param(
    $stmt,
    "isd",
    $user_id,
    $delivery_address,
    $total
);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create order.");
    }

    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    foreach ($cart_items as $item) {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO order_items
            (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iiid",
            $order_id,
            $item["product_id"],
            $item["quantity"],
            $item["price"]
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to create order item.");
        }

        mysqli_stmt_close($stmt);

        $new_stock = $item["stock"] - $item["quantity"];

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE products
            SET stock = ?
            WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $new_stock,
            $item["product_id"]
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to update product stock.");
        }

        mysqli_stmt_close($stmt);
    }

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM cart_items
        WHERE cart_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $cart_id);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to clear cart.");
    }

    mysqli_stmt_close($stmt);

    mysqli_commit($conn);

    header("Location: order_success.php?id=" . $order_id);
    exit;

} catch (Exception $e) {

    mysqli_rollback($conn);

    die("Order failed: " . $e->getMessage());
}
?>