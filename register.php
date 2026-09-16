<?php

$error = $_GET["error"] ?? "";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | Kates Goodies</title>

    <link rel="stylesheet" href="css/register.css">
</head>

<body>

<div class="register-container">

    <div class="register-box">

        <div class="logo">
            <h1>Kates Goodies</h1>
            <p>FRESHLY BAKED EVERY DAY</p>
        </div>

        <div class="register-title">
            <h2>Create Account</h2>
            <p>Join us and start ordering</p>
        </div>

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register_process.php">

            <div class="form-group">
                <label for="full_name">Full Name</label>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    placeholder="Enter your full name"
                    required
                >
            </div>

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
                        placeholder="At least 8 characters"
                        required
                    >
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Re-enter your password"
                    required
                >
            </div>

            <button type="submit" class="register-btn">
                Create Account
            </button>

        </form>

        <div class="login-link">
            Already have an account?
            <a href="login.php">Login</a>
        </div>

        <a href="index.php" class="back-link">
            ← Back to Home
        </a>

    </div>

</div>

</body>
</html>

