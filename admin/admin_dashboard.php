<?php
session_start();
require_once  './db/config.php';
require_once  "/function.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user = get_logged_in_user();

if ($user['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$counts = [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'completed' => 0,
    'cancelled' => 0
];

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders");
$counts['total'] = mysqli_fetch_assoc($result)['total'];

foreach (['pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $status) {
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM orders WHERE status = ?");
    mysqli_stmt_bind_param($stmt, "s", $status);
    mysqli_stmt_execute($stmt);
    $counts[$key] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
    mysqli_stmt_close($stmt);
}

$query = "
    SELECT orders.id, orders.full_name, orders.email, orders.phone,
           orders.payment_method, orders.total_amount, orders.status,
           orders.created_at
    FROM orders
    ORDER BY orders.created_at DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Failed to retrieve orders: " . mysqli_error($conn));
}

$orders = [];

while ($order = mysqli_fetch_assoc($result)) {
    $orders[] = $order;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kates Goodies</title>
    <link rel="stylesheet" href="./css/admin.css">
</head>
<body>

<div class="admin-container">

    <div class="admin-header">
        <div>
            <p class="eyebrow">Admin Panel</p>
            <h1>Orders Dashboard</h1>
            <p>Manage customer orders for Kates Goodies.</p>
        </div>
        <a href="index.php" class="admin-back">Back to Website</a>
    </div>

    <div class="admin-stats">
        <div class="admin-stat">
            <span>Total Orders</span>
            <strong><?= $counts['total'] ?></strong>
        </div>
        <div class="admin-stat">
            <span>Pending</span>
            <strong><?= $counts['pending'] ?></strong>
        </div>
        <div class="admin-stat">
            <span>Approved</span>
            <strong><?= $counts['approved'] ?></strong>
        </div>
        <div class="admin-stat">
            <span>Completed</span>
            <strong><?= $counts['completed'] ?></strong>
        </div>
        <div class="admin-stat">
            <span>Cancelled</span>
            <strong><?= $counts['cancelled'] ?></strong>
        </div>
    </div>

    <div class="admin-orders">
        <div class="admin-section-header">
            <h2>Customer Orders</h2>
            <span><?= count($orders) ?> orders</span>
        </div>

        <?php if (empty($orders)): ?>

            <div class="admin-empty">
                <h2>No Orders Yet</h2>
                <p>Customer orders will appear here.</p>
            </div>

        <?php else: ?>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?= (int) $order['id'] ?></strong></td>

                                <td>
                                    <strong><?= htmlspecialchars($order['full_name']) ?></strong>
                                    <small><?= htmlspecialchars($order['email']) ?></small>
                                </td>

                                <td><?= htmlspecialchars($order['phone']) ?></td>

                                <td><?= htmlspecialchars($order['payment_method']) ?></td>

                                <td>
                                    <strong>₱<?= number_format($order['total_amount'], 2) ?></strong>
                                </td>

                                <td>
                                    <span class="status status-<?= strtolower(str_replace(' ', '-', $order['status'])) ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($order['created_at']) ?></td>

                                <td>
                                    <a href="admin_order_details.php?id=<?= (int) $order['id'] ?>" class="admin-view-button">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>

</div>

</body>
</html>