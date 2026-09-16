<?php


require_once __DIR__ . "/db/config.php";
require_once __DIR__ . "/function.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];


$order_id = $_GET["id"] ?? "";

if (!is_numeric($order_id)) {
    header("Location: orders.php");
    exit;
}

$order_id = (int) $order_id;



$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        total_amount,
        status,
        created_at
    FROM orders
    WHERE id = ?
    AND user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $order_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$order) {
    header("Location: orders.php");
    exit;
}


$stmt = mysqli_prepare(
    $conn,
    "SELECT
        order_items.product_id,
        order_items.quantity,
        order_items.price,
        products.name,
        products.image
    FROM order_items
    INNER JOIN products
        ON order_items.product_id = products.id
    WHERE order_items.order_id = ?
    ORDER BY order_items.id ASC"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $order_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$order_items = [];

while ($item = mysqli_fetch_assoc($result)) {

    $item["subtotal"] =
        $item["price"] * $item["quantity"];

    $order_items[] = $item;
}

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Order #<?= (int) $order["id"] ?>
</title>


</head>

<body>

<div class="order-container">

    <h1>
        Order #<?= (int) $order["id"] ?>
    </h1>


    <div class="order-information">

        <p>
            <strong>Status:</strong>
            <?= htmlspecialchars($order["status"]) ?>
        </p>

        <p>
            <strong>Date:</strong>
            <?= htmlspecialchars($order["created_at"]) ?>
        </p>

    </div>


    <h2>Order Items</h2>


    <?php foreach ($order_items as $item): ?>

        <div class="order-item">

            <?php if (!empty($item["image"])): ?>

                <img
                    src="<?= htmlspecialchars($item["image"]) ?>"
                    alt="<?= htmlspecialchars($item["name"]) ?>"
                    width="100"
                >

            <?php endif; ?>


            <div>

                <h3>
                    <?= htmlspecialchars($item["name"]) ?>
                </h3>

                <p>
                    Price:
                    ₱<?= number_format($item["price"], 2) ?>
                </p>

                <p>
                    Quantity:
                    <?= (int) $item["quantity"] ?>
                </p>

                <p>
                    Subtotal:
                    ₱<?= number_format($item["subtotal"], 2) ?>
                </p>

            </div>

        </div>

    <?php endforeach; ?>


    <hr>


    <h2>
        Total:
        ₱<?= number_format($order["total_amount"], 2) ?>
    </h2>


    <br>


    <a href="orders.php">
        Back to My Orders
    </a>

</div>


</body>

</html>
