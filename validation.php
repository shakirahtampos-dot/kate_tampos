```php
<?php

function validate_registration($full_name, $email, $password, $confirm_password)
{
    $full_name = trim($full_name);
    $email = trim($email);

    if ($full_name === "" || $email === "" || $password === "" || $confirm_password === "") {
        return "Please fill in all fields.";
    }

    if (!preg_match("/^[a-zA-Z\s.'-]+$/", $full_name)) {
        return "Full name can only contain letters, spaces, periods, hyphens, and apostrophes.";
    }

    if (strlen($full_name) < 2) {
        return "Full name must be at least 2 characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Please enter a valid email address.";
    }

    if (strlen($password) < 8) {
        return "Password must be at least 8 characters.";
    }

    if (!preg_match("/[A-Z]/", $password)) {
        return "Password must contain at least one uppercase letter.";
    }

    if (!preg_match("/[a-z]/", $password)) {
        return "Password must contain at least one lowercase letter.";
    }

    if (!preg_match("/[0-9]/", $password)) {
        return "Password must contain at least one number.";
    }

    if (!preg_match("/[^a-zA-Z0-9]/", $password)) {
        return "Password must contain at least one special character.";
    }

    if ($password !== $confirm_password) {
        return "Passwords do not match.";
    }

    return true;
}

function validate_login($email, $password)
{
    $email = trim($email);

    if ($email === "" || $password === "") {
        return "Please enter your email and password.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Please enter a valid email address.";
    }

    return true;
}

?>
```
