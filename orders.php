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
| GET USER ORDERS
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        total_amount,
        status,
        created_at
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$orders = [];

while ($order = mysqli_fetch_assoc($result)) {
    $orders[] = $order;
}

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="./css/style.css">
<title>My Orders</title>

</head>

<body>

<?php require_once __DIR__ . "/includes/navbar.php"; ?>
<div class="orders-container">

    <h1>My Orders</h1>

    <?php if (empty($orders)): ?>

        <div class="empty-orders">

            <h2>No Orders Yet</h2>

            <p>
                You haven't placed any orders yet.
            </p>

            <a href="index.php">
                Start Shopping
            </a>

        </div>

    <?php else: ?>

        <div class="orders-list">

            <?php foreach ($orders as $order): ?>

                <div class="order-card">

                    <div class="order-header">

                        <h2>
                            Order #<?= (int) $order["id"] ?>
                        </h2>

                        <span>
                            <?= htmlspecialchars($order["status"]) ?>
                        </span>

                    </div>

                    <div class="order-details">

                        <p>
                            <strong>Total:</strong>
                            ₱<?= number_format($order["total_amount"], 2) ?>
                        </p>

                        <p>
                            <strong>Date:</strong>
                            <?= htmlspecialchars($order["created_at"]) ?>
                        </p>

                    </div>

                    <a
                        href="order_details.php?id=<?= (int) $order["id"] ?>"
                    >
                        View Order
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>


</body>

</html>
