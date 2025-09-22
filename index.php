<?php
// index.php
// Homepage / Landing Page for Grasshopper Clone
// Includes internal CSS and JS. Uses PHP for session start.
 
session_start();
include 'db.php';
 
if (isset($_SESSION['user_id'])) {
    // Redirect to lessons if logged in (using JS, but check here)
    echo "<script>window.location.href = 'lessons.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grasshopper Clone - Learn Coding Fun!</title>
    <style>
        /* Internal CSS: Modern, responsive, gamified design. Vibrant colors like Google Grasshopper. */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #333; min-height: 100vh; display: flex; flex-direction: column; }
        header { background: rgba(255,255,255,0.1); padding: 1rem; text-align: center; backdrop-filter: blur(10px); }
        h1 { color: white; font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        .hero { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 2rem; color: white; }
        .hero h2 { font-size: 1.8rem; margin-bottom: 1rem; }
        .hero p { font-size: 1.1rem; max-width: 600px; line-height: 1.6; margin-bottom: 2rem; }
        .btn { display: inline-block; padding: 12px 24px; background: #ff6b6b; color: white; text-decoration: none; border-radius: 50px; font-weight: bold; transition: transform 0.3s, box-shadow 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; padding: 3rem 2rem; background: rgba(255,255,255,0.1); }
        .feature { text-align: center; color: white; }
        .feature i { font-size: 3rem; margin-bottom: 1rem; display: block; } /* Assume icons via CSS or font-awesome if added */
        footer { text-align: center; padding: 1rem; background: rgba(0,0,0,0.2); color: white; }
        @media (max-width: 768px) { h1 { font-size: 2rem; } .hero { padding: 1rem; } }
    </style>
</head>
<body>
    <header>
        <h1>🌟 Grasshopper Clone</h1>
        <p>Code like a pro, one hop at a time!</p>
    </header>
    <section class="hero">
        <h2>Unlock the Power of Coding</h2>
        <p>Interactive lessons, fun challenges, and rewards to make learning JavaScript a breeze. Join thousands mastering code today!</p>
        <a href="signup.php" class="btn">Start Learning Free</a>
        <p style="margin-top: 1rem; font-size: 0.9rem;">Already have an account? <a href="login.php" style="color: #ffd700;">Log In</a></p>
    </section>
    <section class="features">
        <div class="feature">
            <i>🎯</i>
            <h3>Interactive Challenges</h3>
            <p>Hands-on coding in your browser.</p>
        </div>
        <div class="feature">
            <i>🏆</i>
            <h3>Gamified Progress</h3>
            <p>Earn badges and level up!</p>
        </div>
        <div class="feature">
            <i>📱</i>
            <h3>Mobile Ready</h3>
            <p>Learn anywhere, anytime.</p>
        </div>
    </section>
    <footer>
        <p>&copy; 2025 Grasshopper Clone. Built with ❤️ for coders.</p>
    </footer>
    <script>
        // Internal JS: Simple animations and redirects.
        document.addEventListener('DOMContentLoaded', () => {
            // Animate hero entrance
            const hero = document.querySelector('.hero');
            hero.style.opacity = '0';
            hero.style.transform = 'translateY(20px)';
            hero.animate([{ opacity: 0, transform: 'translateY(20px)' }, { opacity: 1, transform: 'translateY(0)' }], { duration: 800 });
        });
    </script>
</body>
</html>
