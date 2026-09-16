
<?php
require_once __DIR__ . "/db/config.php";
require_once __DIR__ . "/function.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$message = "";
$message_type = "";

$stmt = mysqli_prepare($conn, "SELECT id, full_name, email FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    logout_user();
    header("Location: login.php");
    exit;
}

$full_name = $user["full_name"] ?? "";
$email = $user["email"] ?? "";

/* UPDATE PROFILE */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($full_name === "" || $email === "") {
        $message = "Full name and email are required.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $message_type = "error";
    } elseif ($new_password !== "" && strlen($new_password) < 6) {
        $message = "New password must be at least 6 characters.";
        $message_type = "error";
    } elseif ($new_password !== "" && $new_password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = "error";
    } else {
        /* CHECK EMAIL */
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existing_user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($existing_user) {
            $message = "That email address is already being used.";
            $message_type = "error";
        } else {
            /* UPDATE USER */
            if ($new_password !== "") {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE users SET full_name = ?, email = ?, password = ? WHERE id = ?"
                );
                mysqli_stmt_bind_param($stmt, "sssi", $full_name, $email, $hashed_password, $user_id);
            } else {
                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE users SET full_name = ?, email = ? WHERE id = ?"
                );
                mysqli_stmt_bind_param($stmt, "ssi", $full_name, $email, $user_id);
            }

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION["user_name"] = $full_name;
                $_SESSION["user_email"] = $email;
                $message = "Profile updated successfully.";
                $message_type = "success";
            } else {
                $message = "Failed to update your profile. Please try again.";
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>My Profile</title>
</head>
<body>

<?php require_once __DIR__ . "/includes/navbar.php"; ?>

<main class="profile-container">

    <div class="profile-header">
        <p class="profile-label">ACCOUNT</p>
        <h1>My Profile</h1>
        <p class="profile-subtitle">Manage your personal information and account settings.</p>
    </div>

    <?php if ($message !== ""): ?>
        <div class="profile-message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="profile-card">

        <div class="profile-card-header">
            <div class="profile-avatar">
                <?= strtoupper(substr($full_name, 0, 1)) ?>
            </div>

            <div>
                <h2><?= htmlspecialchars($full_name) ?></h2>
                <p><?= htmlspecialchars($email) ?></p>
            </div>
        </div>

        <form method="POST" action="profile.php">

            <div class="profile-section">
                <h3>Personal Information</h3>

                <div class="profile-form-group">
                    <label for="full_name">Full Name</label>
                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        value="<?= htmlspecialchars($full_name) ?>"
                        required
                    >
                </div>

                <div class="profile-form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($email) ?>"
                        required
                    >
                </div>
            </div>

            <div class="profile-section">
                <h3>Change Password</h3>
                <p class="profile-hint">Leave these fields blank if you do not want to change your password.</p>

                <div class="profile-form-group">
                    <label for="new_password">New Password</label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="Enter new password"
                    >
                </div>

                <div class="profile-form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm new password"
                    >
                </div>
            </div>

            <div class="profile-actions">
    <a href="index.php" class="profile-cancel">Back to Home</a>
    <button type="submit" class="profile-save">Save Changes</button>
</div>

        </form>

    </div>

</main>

<?php require_once __DIR__ . "/includes/footer.php"; ?>

</body>
</html>



