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

        //Check username if it exist
        if (!empty($username) && strlen($username) >= 4) {
            if ($user->existingUsername($username)) {
                $errors[] = "Username already exists!";
            }
        }

        //Input Valid
        if (empty($errors)) {
            $success = $user->register($username, $password);
            header("Location: login.php?registered=1");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Register</h1>
        <hr class="divider">

        <form method="POST" action="register.php" class="auth-form">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" placeholder="Enter your username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" placeholder="Create a password" name="password" required>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $error): ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn submit-btn">Register</button>
        </form>

        <div class="auth-footer">
            <h4>Already have an account?</h4>
            <a href="login.php" class="link">Login here</a>
        </div>
    </div>
</body>
</html>