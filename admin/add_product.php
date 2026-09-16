<?php

require_once __DIR__ . "/../db/config.php";

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
            "INSERT INTO products (name, description, price, tag, image, stock)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssdssi",
            $name,
            $description,
            $price,
            $tag,
            $image,
            $stock
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: products.php");
            exit;
        }

        $message = "Failed to add product: " . mysqli_error($conn);
        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Kates Goodies</title>
     <link rel="stylesheet" href="../css/add_product.css">
</head>
<body>

<div class="container">

    <h1>Add Product</h1>
    <p>Add a new product to Kates Goodies.</p>

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
                required
            >
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea
                id="description"
                name="description"
                required
            ></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price *</label>
            <input
                type="number"
                id="price"
                name="price"
                step="0.01"
                min="0"
                required
            >
        </div>

        <div class="form-group">
            <label for="tag">Tag</label>
            <input
                type="text"
                id="tag"
                name="tag"
                placeholder="Bestseller, Daily Pick, Limited..."
            >
        </div>

        <div class="form-group">
            <label for="image">Image URL</label>
            <input
                type="text"
                id="image"
                name="image"
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
                required
            >
        </div>

        <div class="buttons">
            <button type="submit">Add Product</button>
            <a href="products.php" class="back-button">Back</a>
        </div>

    </form>

</div>

</body>
</html>

