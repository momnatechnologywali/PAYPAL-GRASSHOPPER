<?php
// login.php
// Login page with PHP authentication.
 
session_start();
include 'db.php';
 
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'lessons.php';</script>";
    exit;
}
 
$error = '';
 
if ($_POST) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
 
    if (empty($email) || empty($password)) {
        $error = 'All fields required.';
    } else {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && verifyPassword($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            echo "<script>window.location.href = 'lessons.php';</script>";
            exit;
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Grasshopper Clone</title>
    <style>
        /* Internal CSS: Similar to signup, consistent design. */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .form-container { background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 400px; width: 100%; }
        h2 { text-align: center; color: #333; margin-bottom: 1.5rem; }
        input { width: 100%; padding: 12px; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; }
        input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 5px rgba(102, 126, 234, 0.5); }
        .btn { width: 100%; padding: 12px; background: #ff6b6b; color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: background 0.3s; }
        .btn:hover { background: #ff5252; }
        .error { color: #e74c3c; text-align: center; margin-bottom: 1rem; }
        .link { text-align: center; margin-top: 1rem; }
        .link a { color: #667eea; text-decoration: none; }
        @media (max-width: 480px) { .form-container { margin: 1rem; padding: 1.5rem; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Welcome Back</h2>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Log In</button>
        </form>
        <div class="link"><a href="index.php">Back to Home</a> | <a href="signup.php">Sign Up</a></div>
    </div>
    <script>
        // Internal JS: Simple focus on email.
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('input[type="email"]').focus();
        });
    </script>
</body>
</html>
