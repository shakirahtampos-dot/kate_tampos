<?php

require_once __DIR__ . "/../db/config.php";
require_once __DIR__ . "/../function.php";

$navLinks = ['Menu', 'About', 'Order'];

$logged_in = is_logged_in();
$user = get_logged_in_user();

$cart_count = 0;
$notification_orders = [];

if ($logged_in) {

    /* CART COUNT */
    $stmt = mysqli_prepare(
        $conn,
        "SELECT COALESCE(SUM(cart_items.quantity), 0) AS total
         FROM cart
         INNER JOIN cart_items ON cart.id = cart_items.cart_id
         WHERE cart.user_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $user['id']);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $cart_data = mysqli_fetch_assoc($result);

    $cart_count = (int) $cart_data['total'];

    mysqli_stmt_close($stmt);

    /* NOTIFICATIONS */
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, status, created_at
         FROM orders
         WHERE user_id = ?
         AND status IN (
             'Approved',
             'Ready for Pickup',
             'Picked Up',
             'Completed',
             'Cancelled'
         )
         ORDER BY created_at DESC
         LIMIT 10"
    );

    mysqli_stmt_bind_param($stmt, "i", $user['id']);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($notification = mysqli_fetch_assoc($result)) {
        $notification_orders[] = $notification;
    }

    mysqli_stmt_close($stmt);
}
?>

<nav class="navbar">

    <div class="nav-container">

        <a href="index.php" class="brand">
            <span class="logo-mark">Kates</span>
            <span class="brand-name">Kates <span>Goodies</span></span>
        </a>

        <div class="desktop-nav">

            <?php foreach ($navLinks as $link): ?>

                <a href="<?= $link === 'Menu' ? 'index.php#menu' : ($link === 'About' ? 'index.php#about' : ($link === 'Order' ? 'index.php#order' : 'login.php')) ?>">
                    <?= htmlspecialchars($link) ?>
                </a>

            <?php endforeach; ?>

            <?php if ($logged_in): ?>

                <!-- NOTIFICATIONS -->
                <div class="notification-wrapper">

                    <button
                        type="button"
                        class="nav-icon notification-button"
                        title="Notifications"
                    >
                        🔔

                        <?php if (!empty($notification_orders)): ?>
                            <span class="notification-badge">
                                <?= count($notification_orders) ?>
                            </span>
                        <?php endif; ?>

                    </button>

                    <div class="notification-panel">

                        <div class="notification-header">
                            <h3>Notifications</h3>
                        </div>

                        <div class="notification-list">

                            <?php if (empty($notification_orders)): ?>

                                <div class="no-notifications">
                                    <p>No notifications yet.</p>
                                </div>

                            <?php else: ?>

                                <?php foreach ($notification_orders as $notification): ?>

                                    <?php
                                    $status = $notification["status"];

                                    $messages = [
                                        "Approved" => "Your order has been approved.",
                                        "Ready for Pickup" => "Your order is ready for pickup.",
                                        "Picked Up" => "Your order has been picked up.",
                                        "Completed" => "Your order has been completed.",
                                        "Cancelled" => "Your order has been cancelled."
                                    ];

                                    $notification_message =
                                        $messages[$status] ??
                                        "Your order status has been updated.";
                                    ?>

                                    <a
                                        href="order_details.php?id=<?= (int) $notification["id"] ?>"
                                        class="notification-item"
                                    >

                                        <div class="notification-icon">
                                            🔔
                                        </div>

                                        <div class="notification-content">

                                            <strong>
                                                Order #<?= (int) $notification["id"] ?>
                                            </strong>

                                            <p>
                                                <?= htmlspecialchars($notification_message) ?>
                                            </p>

                                            <small>
                                                <?= htmlspecialchars($notification["created_at"]) ?>
                                            </small>

                                        </div>

                                    </a>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

                <!-- CART -->
                <a href="cart.php" class="nav-icon cart-icon" title="Cart">
                    🛒

                    <?php if ($cart_count > 0): ?>
                        <span class="cart-count">
                            <?= $cart_count ?>
                        </span>
                    <?php endif; ?>

                </a>

                <!-- PROFILE -->
                <div class="profile-dropdown">

                    <button type="button" class="profile-button">
                        <span class="profile-icon">👤</span>

                        <?= htmlspecialchars($user['name']) ?>

                        <span class="dropdown-arrow">▼</span>
                    </button>

                    <div class="profile-menu">
                        <a href="profile.php">My Profile</a>
                        <a href="orders.php">My Orders</a>
                        <a href="logout.php">Logout</a>
                    </div>

                </div>

            <?php else: ?>

                <a href="login.php">Login</a>
                <a href="register.php" class="nav-button">Order Now</a>

            <?php endif; ?>

        </div>

        <input type="checkbox" id="menu-toggle">

        <label for="menu-toggle" class="hamburger" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </label>

    </div>

    <!-- MOBILE NAV -->
    <div class="mobile-nav">

        <?php foreach ($navLinks as $link): ?>

            <a href="<?= $link === 'Menu' ? 'index.php#menu' : ($link === 'About' ? 'index.php#about' : ($link === 'Order' ? 'index.php#order' : 'login.php')) ?>">
                <?= htmlspecialchars($link) ?>
            </a>

        <?php endforeach; ?>

        <?php if ($logged_in): ?>

            <a href="notifications.php">
                🔔 Notifications
            </a>

            <a href="cart.php">
                🛒 Cart (<?= $cart_count ?>)
            </a>

            <a href="profile.php">
                👤 My Profile
            </a>

            <a href="orders.php">
                My Orders
            </a>

            <a href="logout.php">
                Logout
            </a>

        <?php else: ?>

            <a href="login.php">Login</a>

            <a href="register.php" class="nav-button">
                Order Now
            </a>

        <?php endif; ?>

    </div>

</nav>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const button = document.querySelector(".notification-button");
    const panel = document.querySelector(".notification-panel");

    if (!button || !panel) return;

    button.addEventListener("click", function (event) {
        event.stopPropagation();
        panel.classList.toggle("show");
    });

    document.addEventListener("click", function (event) {
        if (!event.target.closest(".notification-wrapper")) {
            panel.classList.remove("show");
        }
    });
});
</script>