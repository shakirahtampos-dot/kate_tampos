<?php

require_once __DIR__ . "/../db/config.php";

$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Failed to retrieve products: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Kates Goodies</title>
<link rel="stylesheet" href="../css/admin-products.css">
</head>
<body>

<div class="container">

    <div class="top-bar">
        <div>
            <h1>Manage Products</h1>
            <p>Add, edit, or delete Kates Goodies products.</p>
        </div>

        <a href="add_product.php" class="add-button">+ Add Product</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Price</th>
                <th>Tag</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (mysqli_num_rows($result) > 0): ?>

            <?php while ($product = mysqli_fetch_assoc($result)): ?>

                <tr>
                    <td>
                        <img
                            src="<?= htmlspecialchars($product['image']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                            class="product-image"
                        >
                    </td>

                    <td>
                        <?= htmlspecialchars($product['name']) ?>
                    </td>

                    <td>
                        $<?= number_format($product['price'], 2) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($product['tag']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($product['stock']) ?>
                    </td>

                    <td>
                        <a
                            href="edit_product.php?id=<?= $product['id'] ?>"
                            class="edit-button"
                        >
                            Edit
                        </a>

                        <a
                            href="delete_product.php?id=<?= $product['id'] ?>"
                            class="delete-button"
                            onclick="return confirm('Are you sure you want to delete this product?');"
                        >
                            Delete
                        </a>
                    </td>
                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="6">No products found.</td>
            </tr>

        <?php endif; ?>

        </tbody>
    </table>

</div>

</body>
</html>
```
