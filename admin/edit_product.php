<?php

require_once __DIR__ . "/../db/config.php";

$id = $_GET["id"] ?? "";

if (!is_numeric($id)) {
    die("Invalid product ID.");
}

$id = (int) $id;

$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$product) {
    die("Product not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";
    $tag = trim($_POST["tag"] ?? "");
    $image = trim($_POST["image"] ?? "");
    $stock = $_POST["stock"] ?? "";

    if ($name === "" || $description === "" || $price === "" || $stock === "") {
        $message = "Please fill in all required fields.";
    } elseif (!is_numeric($price) || $price < 0) {
        $message = "Please enter a valid price.";
    } elseif (!is_numeric($stock) || $stock < 0) {
        $message = "Please enter a valid stock quantity.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE products
             SET name = ?, description = ?, price = ?, tag = ?, image = ?, stock = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssdssii",
            $name,
            $description,
            $price,
            $tag,
            $image,
            $stock,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: products.php");
            exit;
        }

        $message = "Failed to update product: " . mysqli_error($conn);
        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Kates Goodies</title>
    <link rel="stylesheet" href="../css/edit_product.css">

</head>
<body>

<div class="container">

    <h1>Edit Product</h1>
    <p>Update the information for this product.</p>

    <?php if ($message !== ""): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label for="name">Product Name *</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?= htmlspecialchars($product['name']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea
                id="description"
                name="description"
                required
            ><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price *</label>
            <input
                type="number"
                id="price"
                name="price"
                step="0.01"
                min="0"
                value="<?= htmlspecialchars($product['price']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="tag">Tag</label>
            <input
                type="text"
                id="tag"
                name="tag"
                value="<?= htmlspecialchars($product['tag']) ?>"
                placeholder="Bestseller, Daily Pick, Limited..."
            >
        </div>

        <div class="form-group">
            <label for="image">Image URL</label>
            <input
                type="text"
                id="image"
                name="image"
                value="<?= htmlspecialchars($product['image']) ?>"
                placeholder="https://..."
            >
        </div>

        <div class="form-group">
            <label for="stock">Stock *</label>
            <input
                type="number"
                id="stock"
                name="stock"
                min="0"
                value="<?= htmlspecialchars($product['stock']) ?>"
                required
            >
        </div>

        <div class="buttons">
            <button type="submit">Save Changes</button>
            <a href="products.php" class="back-button">Cancel</a>
        </div>

    </form>

</div>

</body>
</html>

