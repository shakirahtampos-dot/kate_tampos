```php
<?php

$error = $_GET["error"] ?? "";
$registered = isset($_GET["registered"]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Kates Goodies</title>

    <link rel="stylesheet" href="css/login.css">
</head>

<body>

<div class="login-container">

    <div class="login-box">

        <div class="logo">
            <h1>Kates Goodies</h1>
            <p>FRESHLY BAKED EVERY DAY</p>
        </div>

        <div class="login-title">
            <h2>Welcome Back</h2>
            <p>Login to your account</p>
        </div>

        <?php if ($registered): ?>
            <div class="success">
                Account created successfully. You can now log in.
            </div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login_process.php">

            <div class="form-group">
                <label for="email">Email Address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <button type="submit" class="login-btn">
                Login
            </button>

        </form>

        <div class="register-link">
            Don't have an account?
            <a href="register.php">Create one</a>
        </div>

        <a href="index.php" class="back-link">
            ← Back to Home
        </a>

    </div>

</div>

</body>
</html>
```
