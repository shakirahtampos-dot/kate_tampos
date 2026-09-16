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

$cart_items = [];
$total = 0;

if ($cart) {
    $cart_id = $cart["id"];

  

    $stmt = mysqli_prepare(
        $conn,
        "SELECT 
            cart_items.id AS cart_item_id,
            cart_items.quantity,
            products.id AS product_id,
            products.name,
            products.price,
            products.image,
            products.stock
        FROM cart_items
        INNER JOIN products 
            ON cart_items.product_id = products.id
        WHERE cart_items.cart_id = ?
        ORDER BY cart_items.id DESC"
    );

    mysqli_stmt_bind_param($stmt, "i", $cart_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($item = mysqli_fetch_assoc($result)) {
        $item["subtotal"] = $item["price"] * $item["quantity"];
        $total += $item["subtotal"];

        $cart_items[] = $item;
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
<?php require_once __DIR__ . "/includes/navbar.php"; ?>
<div class="container">


<h1>My Cart</h1>

<?php if (empty($cart_items)): ?>

    <div class="empty-cart">
        <h2>Your cart is empty</h2>
        <p>You haven't added any products yet.</p>

        <a href="index.php" class="btn checkout">
            Continue Shopping
        </a>
    </div>

<?php else: ?>

    <div class="cart">

        <?php foreach ($cart_items as $item): ?>

            <div class="cart-item">

                <?php if (!empty($item["image"])): ?>

                    <img
                        src="<?= htmlspecialchars($item["image"]) ?>"
                        alt="<?= htmlspecialchars($item["name"]) ?>"
                        class="product-image"
                    >

                <?php else: ?>

                    <div class="product-image"></div>

                <?php endif; ?>

                <div class="product-info">

                    <h3>
                        <?= htmlspecialchars($item["name"]) ?>
                    </h3>

                    <div class="product-price">
                        ₱<?= number_format($item["price"], 2) ?>
                    </div>

                                <div class="quantity">
                        <form action="update_cart.php" method="POST">

                            <input
                                type="hidden"
                                name="cart_item_id"
                                value="<?= (int) $item["cart_item_id"] ?>"
                            >

                            <button
                                type="submit"
                                name="action"
                                value="decrease"
                                <?= $item["quantity"] <= 1 ? "disabled" : "" ?>
                            >
                                −
                            </button>

                            <span>
                                <?= (int) $item["quantity"] ?>
                            </span>

                            <button
                                type="submit"
                                name="action"
                                value="increase"
                            >
                                +
                            </button>

                        </form>
                    </div>

                </div>

                <div class="subtotal">
                    ₱<?= number_format($item["subtotal"], 2) ?>
                </div>

            <a
                href="remove_from_cart.php?id=<?= (int) $item["cart_item_id"] ?>"
                class="remove"
                onclick="return confirm('Remove this item from your cart?');"
            >
                Remove
            </a>
            </div>

        <?php endforeach; ?>

        <div class="cart-summary">

            <div class="total">
                Total:
                ₱<?= number_format($total, 2) ?>
            </div>

            <div class="buttons">

                <a href="index.php" class="btn continue">
                    Continue Shopping
                </a>

                <a href="checkout.php" class="btn checkout">
                    Proceed to Checkout
                </a>

            </div>

        </div>

    </div>

<?php endif; ?>


</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>

</body>
</html>
