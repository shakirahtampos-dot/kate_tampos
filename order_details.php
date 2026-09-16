```php
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

$stmt = mysqli_prepare($conn, "SELECT id, delivery_address, total_amount, status, created_at FROM orders WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$order) {
    header("Location: orders.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT order_items.product_id, order_items.quantity, order_items.price, products.name, products.image FROM order_items INNER JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = ? ORDER BY order_items.id ASC");
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$order_items = [];

while ($item = mysqli_fetch_assoc($result)) {
    $item["subtotal"] = $item["price"] * $item["quantity"];
    $order_items[] = $item;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= (int) $order["id"] ?> | Kates Goodies</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require_once __DIR__ . "/includes/navbar.php"; ?>

<main class="order-details-page">
    <div class="order-details-heading">
        <p class="eyebrow">My Order</p>
        <div class="order-heading-row">
            <div>
                <h1>Order #<?= (int) $order["id"] ?></h1>
                <p>Thank you for ordering from Kates Goodies.</p>
            </div>
            <a href="orders.php" class="order-back-button">← My Orders</a>
        </div>
    </div>

    <div class="order-details-grid">
        <section class="order-main-card">
            <div class="order-card-header">
                <div>
                    <p class="eyebrow">Order Items</p>
                    <h2>Your Order</h2>
                </div>
                <span class="order-status status-<?= strtolower(str_replace(' ', '-', $order["status"])) ?>">
                    <?= htmlspecialchars($order["status"]) ?>
                </span>
            </div>

            <div class="customer-order-items">
                <?php foreach ($order_items as $item): ?>
                    <div class="customer-order-item">
                        <div class="customer-order-image">
                            <?php if (!empty($item["image"])): ?>
                                <img src="<?= htmlspecialchars($item["image"]) ?>" alt="<?= htmlspecialchars($item["name"]) ?>">
                            <?php endif; ?>
                        </div>

                        <div class="customer-order-info">
                            <h3><?= htmlspecialchars($item["name"]) ?></h3>
                            <p>₱<?= number_format($item["price"], 2) ?> × <?= (int) $item["quantity"] ?></p>
                        </div>

                        <strong class="customer-order-subtotal">
                            ₱<?= number_format($item["subtotal"], 2) ?>
                        </strong>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="customer-order-total">
                <span>Total</span>
                <strong>₱<?= number_format($order["total_amount"], 2) ?></strong>
            </div>
        </section>

        <aside class="order-side-card">
            <div class="order-info-block">
                <p class="eyebrow">Delivery</p>
                <h2>Delivery Address</h2>
                <p class="delivery-address">
                    <?= nl2br(htmlspecialchars($order["delivery_address"])) ?>
                </p>
            </div>

            <div class="order-divider"></div>

            <div class="order-info-block">
                <p class="eyebrow">Order Information</p>

                <div class="order-info-row">
                    <span>Order Number</span>
                    <strong>#<?= (int) $order["id"] ?></strong>
                </div>

                <div class="order-info-row">
                    <span>Status</span>
                    <strong><?= htmlspecialchars($order["status"]) ?></strong>
                </div>

                <div class="order-info-row">
                    <span>Order Date</span>
                    <strong><?= htmlspecialchars($order["created_at"]) ?></strong>
                </div>
            </div>

            <div class="order-divider"></div>

            <a href="orders.php" class="order-back-link">← Back to My Orders</a>
        </aside>
    </div>
</main>

<?php require_once __DIR__ . "/includes/footer.php"; ?>

</body>
</html>
```
