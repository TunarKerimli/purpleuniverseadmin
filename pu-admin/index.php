<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";

    if ($email === "admin@purpleuniverse.site" && $password === "PurpleAdmin2026!") {

        $_SESSION["admin_logged_in"] = true;

        header("Location: dashboard.php");
        exit;

    } else {

        $error = "Email və ya şifrə yanlışdır.";

    }
}
?>

<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Purple Universe — Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/admin.css">
</head>

<body>

<div class="login-page">

    <div class="login-card">

        <div class="logo">
            PURPLE<span>UNIVERSE</span>
        </div>

        <div class="admin-label">
            ADMIN PANEL
        </div>

        <h1>Welcome back.</h1>

        <p class="subtitle">
            Sign in to manage your store.
        </p>

        <?php if ($error): ?>

            <div style="
                background:#35151b;
                border:1px solid #6b2631;
                color:#ff8f9b;
                padding:12px;
                border-radius:10px;
                margin-bottom:20px;
                font-size:13px;
            ">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="admin@purpleuniverse.site"
                required
            >

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="••••••••"
                required
            >

            <button type="submit">
                Sign In
            </button>

        </form>

        <p class="security">
            🔒 Authorized access only
        </p>

    </div>

</div>

</body>
</html>