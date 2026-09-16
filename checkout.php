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
| GET USER CART
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT id FROM cart WHERE user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$cart = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/*
|--------------------------------------------------------------------------
| CHECK CART
|--------------------------------------------------------------------------
*/

if (!$cart) {
    header("Location: cart.php");
    exit;
}

$cart_id = $cart["id"];


/*
|--------------------------------------------------------------------------
| GET CART ITEMS
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        cart_items.id AS cart_item_id,
        cart_items.product_id,
        cart_items.quantity,
        products.name,
        products.price,
        products.image,
        products.stock
    FROM cart_items
    INNER JOIN products
        ON cart_items.product_id = products.id
    WHERE cart_items.cart_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $cart_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$cart_items = [];
$total = 0;

while ($item = mysqli_fetch_assoc($result)) {

    $subtotal = $item["price"] * $item["quantity"];

    $item["subtotal"] = $subtotal;

    $total += $subtotal;

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


<title>Checkout</title>

</head>
<body>
<?php require_once __DIR__ . "/includes/navbar.php"; ?>
<h1>Checkout</h1>

<h2>Order Summary</h2>

<?php foreach ($cart_items as $item): ?>

    <div class="checkout-item">

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
<?php endforeach; ?>

<hr>
<h2>
    Total:
    ₱<?= number_format($total, 2) ?>
</h2>

<form action="place_order.php" method="POST">

    <input
        type="hidden"
        name="total_amount"
        value="<?= htmlspecialchars($total) ?>"
    >

    <button type="submit">
        Place Order
    </button>

</form>
<br>
<a href="cart.php">
    Back to Cart
</a>
<?php require_once __DIR__ . "/includes/footer.php"; ?>

</body>
</html>
