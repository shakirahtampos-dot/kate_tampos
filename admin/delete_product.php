<?php

require_once __DIR__ . "/../db/config.php";

$id = $_GET["id"] ?? "";

if (!is_numeric($id)) {
    die("Invalid product ID.");
}

$id = (int) $id;

$stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: products.php");
    exit;
}

mysqli_stmt_close($stmt);

die("Failed to delete product.");

?>

