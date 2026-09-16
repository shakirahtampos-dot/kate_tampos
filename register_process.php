```php
<?php

session_start();

require_once "function.php";
require_once "validation.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit;
}

$full_name = trim($_POST["full_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";

$validation = validate_registration(
    $full_name,
    $email,
    $password,
    $confirm_password
);

if ($validation !== true) {
    header("Location: register.php?error=" . urlencode($validation));
    exit;
}

$result = register_user($full_name, $email, $password);

if ($result === true) {
    header("Location: login.php?registered=1");
    exit;
}

header("Location: register.php?error=" . urlencode($result));
exit;

?>
```
