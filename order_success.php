<?php
require_once __DIR__ . "/db/config.php";
require_once __DIR__ . "/function.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET["id"] ?? "";

if (!is_numeric($order_id)) {
    header("Location: orders.php");
    exit;
}

$order_id = (int) $order_id;

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, total_amount, status, delivery_address, created_at
    FROM orders
    WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($stmt, "ii", $order_id, $_SESSION["user_id"]);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$order) {
    header("Location: orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed | Kates Goodies</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require_once __DIR__ . "/includes/navbar.php"; ?>

<main class="order-success-page">

    <div class="order-success-card">

        <div class="order-success-icon">✓</div>

        <p class="eyebrow">Order Confirmed</p>

        <h1>Thank You<br>For Your Order!</h1>

        <p class="order-success-message">
            Your order has been successfully placed.
            We'll prepare your goodies with care.
        </p>

        <div class="success-order-number">
            <span>Order Number</span>
            <strong>#<?= (int) $order["id"] ?></strong>
        </div>

        <div class="success-details">

            <div>
                <span>Status</span>
                <strong><?= htmlspecialchars($order["status"]) ?></strong>
            </div>

            <div>
                <span>Total</span>
                <strong>₱<?= number_format($order["total_amount"], 2) ?></strong>
            </div>

            <div>
                <span>Delivery Address</span>
                <strong>
                    <?= nl2br(htmlspecialchars($order["delivery_address"])) ?>
                </strong>
            </div>

        </div>

        <div class="order-success-actions">
            <a href="order_details.php?id=<?= (int) $order["id"] ?>" class="success-primary-button">
                View Order
            </a>

            <a href="index.php" class="success-secondary-button">
                Continue Shopping
            </a>
        </div>

    </div>

</main>

<?php require_once __DIR__ . "/includes/footer.php"; ?>

</body>
</html>

