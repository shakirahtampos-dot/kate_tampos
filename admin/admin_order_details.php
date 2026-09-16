<?php
require_once "./db/config.php";
require_once "/function.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user = get_logged_in_user();

if ($user['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($order_id <= 0) {
    header("Location: admin_dashboard.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT orders.*, users.email FROM orders INNER JOIN users ON orders.user_id = users.id WHERE orders.id = ?");
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$order) {
    header("Location: admin_dashboard.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT order_items.*, products.name, products.image FROM order_items INNER JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = ?");
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];

while ($item = mysqli_fetch_assoc($result)) {
    $items[] = $item;
}

mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';

    $allowed_statuses = [
        'Pending',
        'Approved',
        'Ready for Pickup',
        'Picked Up',
        'Completed',
        'Cancelled'
    ];

    if (in_array($status, $allowed_statuses, true)) {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: admin_order_details.php?id=$order_id&updated=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= $order_id ?> - Admin</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<div class="admin-container">

    <div class="admin-header">
        <div>
            <p class="eyebrow">Admin Panel</p>
            <h1>Order #<?= $order_id ?></h1>
            <p>View and manage this customer order.</p>
        </div>
        <a href="admin_dashboard.php" class="admin-back">← Back to Orders</a>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="admin-success">Order status updated successfully.</div>
    <?php endif; ?>

    <div class="admin-order-details">

        <div class="admin-detail-card">
            <p class="eyebrow">Customer</p>
            <h2>User #<?= (int) $order['user_id'] ?></h2>
            <p><?= htmlspecialchars($order['email']) ?></p>
        </div>

        <div class="admin-detail-card">
            <p class="eyebrow">Order Information</p>
            <h2>₱<?= number_format($order['total_amount'], 2) ?></h2>
            <p>Status: <strong><?= htmlspecialchars($order['status']) ?></strong></p>
            <p>Date: <?= htmlspecialchars($order['created_at']) ?></p>
        </div>

        <div class="admin-detail-card">
            <p class="eyebrow">Update Status</p>

            <form method="POST">
                <select name="status" required>
                    <?php foreach (['Pending', 'Approved', 'Ready for Pickup', 'Picked Up', 'Completed', 'Cancelled'] as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                            <?= htmlspecialchars($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="admin-update-button">Update Status</button>
            </form>
        </div>

    </div>

    <div class="admin-orders">
        <div class="admin-section-header">
            <h2>Ordered Products</h2>
            <span><?= count($items) ?> items</span>
        </div>

        <div class="admin-order-items">

            <?php foreach ($items as $item): ?>

                <div class="admin-order-item">

                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">

                    <div>
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p>₱<?= number_format($item['price'], 2) ?> × <?= (int) $item['quantity'] ?></p>
                    </div>

                    <strong>
                        ₱<?= number_format($item['price'] * $item['quantity'], 2) ?>
                    </strong>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="admin-order-total">
            <span>Total</span>
            <strong>₱<?= number_format($order['total_amount'], 2) ?></strong>
        </div>

    </div>

</div>

</body>
</html>