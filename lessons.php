<?php
// lessons.php
// Lessons dashboard with progress tracking.
 
session_start();
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
 
$user_id = $_SESSION['user_id'];
 
// Fetch lessons
$stmt = $pdo->prepare("SELECT * FROM lessons ORDER BY order_num");
$stmt->execute();
$lessons = $stmt->fetchAll();
 
// Fetch user progress
$stmt = $pdo->prepare("SELECT lesson_id, completed FROM user_progress WHERE user_id = ?");
$stmt->execute([$user_id]);
$progress = $stmt->fetchAll();
$completed = array_column($progress, 'lesson_id', 'lesson_id');
 
// Fetch badges
$stmt = $pdo->prepare("SELECT b.* FROM badges b JOIN user_badges ub ON b.id = ub.badge_id WHERE ub.user_id = ?");
$stmt->execute([$user_id]);
$badges = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lessons - Grasshopper Clone</title>
    <style>
        /* Internal CSS: Dashboard with cards, progress bar, badges section. Vibrant and motivating. */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #333; }
        header { background: #333; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        nav a { color: white; text-decoration: none; margin-right: 1rem; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .progress { background: #eee; height: 10px; border-radius: 5px; margin-bottom: 2rem; overflow: hidden; }
        .progress-bar { background: #4caf50; height: 100%; transition: width 0.3s; }
        .lessons-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
        .lesson-card { background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .lesson-card:hover { transform: translateY(-5px); }
        .lesson-card.completed { border-left: 5px solid #4caf50; }
        .lesson-card h3 { color: #333; margin-bottom: 0.5rem; }
        .lesson-card p { color: #666; margin-bottom: 1rem; }
        .btn { padding: 8px 16px; background: #ff6b6b; color: white; text-decoration: none; border-radius: 5px; }
        .badges { display: flex; flex-wrap: wrap; gap: 1rem; }
        .badge { background: #ffd700; padding: 10px; border-radius: 10px; font-weight: bold; }
        footer { text-align: center; padding: 1rem; background: #333; color: white; }
        @media (max-width: 768px) { header { flex-direction: column; gap: 1rem; } .lessons-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <h1>🏆 My Lessons</h1>
        <nav>
            <a href="challenge.php?id=1">Start Lesson</a>
            <a href="logout.php">Logout</a> <!-- Assume logout.php exists, simple session_destroy -->
        </nav>
    </header>
    <div class="container">
        <div class="progress">
            <div class="progress-bar" style="width: <?php echo count($completed) / count($lessons) * 100; ?>%;"></div>
        </div>
        <h2>Progress: <?php echo count($completed); ?>/<?php echo count($lessons); ?> Completed</h2>
        <div class="lessons-grid">
            <?php foreach ($lessons as $lesson): ?>
            <div class="lesson-card <?php echo in_array($lesson['id'], $completed) ? 'completed' : ''; ?>">
                <h3><?php echo htmlspecialchars($lesson['title']); ?></h3>
                <p><?php echo substr($lesson['description'], 0, 100); ?>...</p>
                <a href="challenge.php?id=<?php echo $lesson['id']; ?>" class="btn">Start / Continue</a>
            </div>
            <?php endforeach; ?>
        </div>
        <section>
            <h2>🏅 Your Badges</h2>
            <div class="badges">
                <?php foreach ($badges as $badge): ?>
                <div class="badge"><?php echo htmlspecialchars($badge['name']); ?></div>
                <?php endforeach; ?>
                <?php if (empty($badges)): ?><p>No badges yet! Complete lessons to earn them.</p><?php endif; ?>
            </div>
        </section>
    </div>
    <footer>
        <p>Keep hopping! 🚀</p>
    </footer>
    <script>
        // Internal JS: Dynamic progress update and card clicks.
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.lesson-card');
            cards.forEach(card => {
                card.addEventListener('click', (e) => {
                    if (e.target.tagName !== 'A') {
                        const link = card.querySelector('a');
                        if (link) window.location.href = link.href;
                    }
                });
            });
        });
    </script>
</body>
</html>
