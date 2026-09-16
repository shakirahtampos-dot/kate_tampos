<?php
require_once __DIR__ . "/db/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function register_user($full_name, $email, $password)
{
    global $conn;

    $full_name = trim($full_name);
    $email = trim($email);

    if ($full_name === "" || $email === "" || $password === "") {
        return "Please fill in all fields.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Please enter a valid email address.";
    }

    if (strlen($password) < 6) {
        return "Password must be at least 6 characters.";
    }

    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        mysqli_stmt_close($check);
        return "Email is already registered.";
    }

    mysqli_stmt_close($check);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $full_name, $email, $hashed_password);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    }

    mysqli_stmt_close($stmt);
    return "Registration failed. Please try again.";
}

function login_user($email, $password)
{
    global $conn;

    $email = trim($email);

    $stmt = mysqli_prepare($conn, "SELECT id, full_name, email, password, role FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$user) {
        return "Invalid email or password.";
    }

    if (!password_verify($password, $user['password'])) {
        return "Invalid email or password.";
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    return true;
}

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function logout_user()
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

function get_logged_in_user()
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
}
?>