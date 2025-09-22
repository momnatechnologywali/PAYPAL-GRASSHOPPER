<?php
// signup.php
// Signup page with PHP backend for registration.
 
session_start();
include 'db.php';
 
$error = '';
$success = '';
 
if ($_POST) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
 
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Username or email already exists.';
        } else {
            // Insert user
            $hash = hashPassword($password);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hash])) {
                $success = 'Account created! Redirecting to login...';
                $_SESSION['signup_success'] = true;
                echo "<script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>";
            } else {
                $error = 'Signup failed. Try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Grasshopper Clone</title>
    <style>
        /* Internal CSS: Clean form design, responsive, with gradients. */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .form-container { background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 400px; width: 100%; }
        h2 { text-align: center; color: #333; margin-bottom: 1.5rem; }
        input { width: 100%; padding: 12px; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; }
        input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 5px rgba(102, 126, 234, 0.5); }
        .btn { width: 100%; padding: 12px; background: #ff6b6b; color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: background 0.3s; }
        .btn:hover { background: #ff5252; }
        .error { color: #e74c3c; text-align: center; margin-bottom: 1rem; }
        .success { color: #27ae60; text-align: center; margin-bottom: 1rem; }
        .link { text-align: center; margin-top: 1rem; }
        .link a { color: #667eea; text-decoration: none; }
        @media (max-width: 480px) { .form-container { margin: 1rem; padding: 1.5rem; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create Your Account</h2>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
            <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Sign Up</button>
        </form>
        <div class="link"><a href="index.php">Back to Home</a> | <a href="login.php">Login</a></div>
    </div>
    <script>
        // Internal JS: Form validation and redirect.
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            form.addEventListener('submit', (e) => {
                const pass = document.querySelector('input[type="password"]').value;
                if (pass.length < 6) {
                    e.preventDefault();
                    alert('Password too short!');
                }
            });
        });
    </script>
</body>
</html>
