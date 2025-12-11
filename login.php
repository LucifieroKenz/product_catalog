<?php
require "user.php";
$user = new User();
$errors = [];
$username = '';
$password = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';

    //Input Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters long.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    //Authentication
    if (!empty($username) && strlen($username) >= 4 && !empty($password) && strlen($password) >= 6) {
        $userData = $user->getUsername($username);
        if (!$userData) {
            $errors[] = "Invalid username or password.";
        } elseif (!password_verify($password, $userData['password'])) {
            $errors[] = "Invalid username or password.";
        }
    }

    //Valid Login
    if (empty($errors)) {
        session_start();
        $_SESSION['username'] = $username;
        header("Location: dashboard.php?logged_in=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Login</h1>
        <hr class="divider">

        <form method="POST" action="login.php" class="auth-form">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       value="<?php echo htmlspecialchars($username); ?>" 
                       placeholder="Enter your username" 
                       required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Enter your password" 
                       required>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $error): ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
                <div class="alert success">Registration successful! Please Log In.</div>
            <?php endif; ?>

            <button type="submit" class="btn submit-btn">Login</button>
        </form>

        <div class="auth-footer">
            <h4>Don't have an account?</h4>
            <a href="register.php" class="link">Register here</a>
        </div>
    </div>
</body>
</html>