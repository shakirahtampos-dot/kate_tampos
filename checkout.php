<?php
require_once __DIR__ . "/db/config.php";
require_once __DIR__ . "/function.php";

if (!is_logged_in()) {
    header("Location: login.php");
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
    "SELECT cart_items.product_id, cart_items.quantity, products.name, products.price, products.image, products.stock
    FROM cart_items
    INNER JOIN products ON cart_items.product_id = products.id
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

    $item["subtotal"] = $item["price"] * $item["quantity"];
    $total += $item["subtotal"];
    $cart_items[] = $item;
}

mysqli_stmt_close($stmt);

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Kates Goodies</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require_once __DIR__ . "/includes/navbar.php"; ?>

<main class="checkout-page">

    <div class="checkout-heading">
        <p class="eyebrow">Your Order</p>
        <h1>Checkout</h1>
        <p>Review your order and enter your delivery address.</p>
    </div>

    <div class="checkout-layout">

        <section class="checkout-products">

            <div class="checkout-section-title">
                <h2>Order Summary</h2>
                <span><?= count($cart_items) ?> item<?= count($cart_items) !== 1 ? 's' : '' ?></span>
            </div>

            <?php foreach ($cart_items as $item): ?>

                <div class="checkout-item">

                    <img
                        src="<?= htmlspecialchars($item["image"]) ?>"
                        alt="<?= htmlspecialchars($item["name"]) ?>"
                    >

                    <div class="checkout-item-info">
                        <h3><?= htmlspecialchars($item["name"]) ?></h3>

                        <p>
                            ₱<?= number_format($item["price"], 2) ?>
                            × <?= (int) $item["quantity"] ?>
                        </p>
                    </div>

                    <strong class="checkout-item-total">
                        ₱<?= number_format($item["subtotal"], 2) ?>
                    </strong>

                </div>

            <?php endforeach; ?>

        </section>


<aside class="checkout-summary">

    <p class="eyebrow">Delivery</p>
    <h2>Delivery Address</h2>

    <?php if (isset($_GET["error"]) && $_GET["error"] === "address_required"): ?>
        <p class="checkout-error">Please enter your delivery address.</p>
    <?php endif; ?>

    <form action="place_order.php" method="POST">

        <div class="address-form">
            <label for="delivery_address">Where should we deliver your order?</label>

            <textarea
                id="delivery_address"
                name="delivery_address"
                rows="4"
                placeholder="House/Unit No., Street, Barangay, City, Province"
                required
            ></textarea>

            <small>Make sure your address is complete and accurate.</small>
        </div>

        <div class="checkout-summary-row">
            <span>Subtotal</span>
            <span>₱<?= number_format($total, 2) ?></span>
        </div>

        <div class="checkout-summary-row">
            <span>Shipping</span>
            <span>Free</span>
        </div>

        <div class="checkout-divider"></div>

        <div class="checkout-total">
            <span>Total</span>
            <strong>₱<?= number_format($total, 2) ?></strong>
        </div>

        <button type="submit" class="checkout-button">
            Place Order
        </button>

    </form>

    <a href="cart.php" class="checkout-back">
        ← Back to Cart
    </a>

</aside>



    </div>

</main>

<?php require_once __DIR__ . "/includes/footer.php"; ?>

</body>
</html>