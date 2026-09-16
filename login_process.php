```php
<?php

session_start();

require_once "function.php";
require_once "validation.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

$validation = validate_login($email, $password);

if ($validation !== true) {
    header("Location: login.php?error=" . urlencode($validation));
    exit;
}

$result = login_user($email, $password);

if ($result === true) {
    header("Location: index.php");
    exit;
}

header("Location: login.php?error=" . urlencode($result));
exit;

?>
```
