<?php
// challenge.php
// Interactive coding challenge page.
 
session_start();
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
 
$user_id = $_SESSION['user_id'];
$lesson_id = (int)$_GET['id'] ?? 1;
 
if ($lesson_id < 1) {
    echo "<script>window.location.href = 'lessons.php';</script>";
    exit;
}
 
// Fetch lesson
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch();
if (!$lesson) {
    echo "<script>window.location.href = 'lessons.php';</script>";
    exit;
}
 
// Check progress
$stmt = $pdo->prepare("SELECT * FROM user_progress WHERE user_id = ? AND lesson_id = ?");
$stmt->execute([$user_id, $lesson_id]);
$prog = $stmt->fetch();
 
// Update attempts
if (!$prog) {
    $stmt = $pdo->prepare("INSERT INTO user_progress (user_id, lesson_id, attempts) VALUES (?, ?, 1)");
    $stmt->execute([$user_id, $lesson_id]);
    $prog = ['attempts' => 1, 'completed' => false];
} else {
    $stmt = $pdo->prepare("UPDATE user_progress SET attempts = attempts + 1 WHERE id = ?");
    $stmt->execute([$prog['id']]);
}
 
// Handle code submission (via POST)
$feedback = '';
$completed = false;
if ($_POST && isset($_POST['user_code'])) {
    $user_code = $_POST['user_code'];
    // Simple JS eval simulation: Compare output or exact match for simplicity.
    // For real, we'd need a sandbox, but here use string compare to solution.
    if (trim($user_code) === trim($lesson['solution_code'])) {
        if (!$prog['completed']) {
            $stmt = $pdo->prepare("UPDATE user_progress SET completed = TRUE, score = 100, completed_at = NOW() WHERE user_id = ? AND lesson_id = ?");
            $stmt->execute([$user_id, $lesson_id]);
            // Award badge if applicable
            $total_completed = $pdo->prepare("SELECT COUNT(*) as cnt FROM user_progress WHERE user_id = ? AND completed = TRUE");
            $total_completed->execute([$user_id]);
            $cnt = $total_completed->fetch()['cnt'];
            $stmt = $pdo->prepare("SELECT id FROM badges WHERE required_lessons <= ? LIMIT 1");
            $stmt->execute([$cnt]);
            $new_badge = $stmt->fetch();
            if ($new_badge && !$pdo->query("SELECT 1 FROM user_badges WHERE user_id = $user_id AND badge_id = {$new_badge['id']}")->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $new_badge['id']]);
            }
        }
        $feedback = '🎉 Correct! Lesson completed.';
        $completed = true;
    } else {
        $feedback = 'Not quite! Try again. Hint: ' . $lesson['hints'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lesson['title']); ?> - Grasshopper Clone</title>
    <style>
        /* Internal CSS: Split layout for editor and output. Monaco-like editor sim with textarea. */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Consolas', monospace; background: #1e1e1e; color: #d4d4d4; }
        header { background: #252526; padding: 1rem; color: #fff; }
        .container { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding: 2rem; height: calc(100vh - 100px); }
        @media (max-width: 768px) { .container { grid-template-columns: 1fr; height: auto; } }
        .editor-section { background: #2d2d30; border-radius: 10px; padding: 1rem; }
        h2 { margin-bottom: 1rem; color: #fff; }
        textarea { width: 100%; height: 60%; background: #1e1e1e; color: #d4d4d4; border: 1px solid #404040; padding: 1rem; font-size: 14px; border-radius: 5px; resize: none; }
        .output { background: #0e639c; color: white; padding: 1rem; border-radius: 5px; min-height: 200px; overflow-y: auto; }
        .prompt { background: #f4f4f4; color: #333; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .btn { padding: 10px 20px; background: #0e639c; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 1rem; }
        .btn:hover { background: #1177bb; }
        .feedback { padding: 1rem; border-radius: 5px; margin-top: 1rem; }
        .feedback.success { background: #dff0d8; color: #3c763d; }
        .feedback.error { background: #f2dede; color: #a94442; }
        .hint { background: #fff3cd; color: #856404; padding: 1rem; border-radius: 5px; margin-top: 1rem; }
        nav { float: right; }
        nav a { color: #fff; text-decoration: none; margin-left: 1rem; }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($lesson['title']); ?></h1>
        <nav>
            <a href="lessons.php">Back to Lessons</a>
        </nav>
    </header>
    <div class="container">
        <div class="editor-section">
            <h2>Challenge</h2>
            <div class="prompt"><?php echo nl2br(htmlspecialchars($lesson['challenge_prompt'])); ?></div>
            <?php if ($lesson['hints']): ?>
            <div class="hint">💡 Hint: <?php echo htmlspecialchars($lesson['hints']); ?></div>
            <?php endif; ?>
            <form method="POST">
                <textarea name="user_code" placeholder="Write your code here..." required><?php echo htmlspecialchars($lesson['starter_code'] ?? ''); ?></textarea>
                <br>
                <button type="submit" class="btn">Run & Check</button>
                <button type="button" class="btn" onclick="showSolution()">Show Solution</button>
            </form>
            <?php if ($feedback): ?>
            <div class="feedback <?php echo $completed ? 'success' : 'error'; ?>"><?php echo $feedback; ?></div>
            <?php endif; ?>
        </div>
        <div class="output">
            <h2>Output / Console</h2>
            <div id="output">Run your code to see results here. Expected: Matches the solution.</div>
        </div>
    </div>
    <script>
        // Internal JS: Code editor simulation, run button (but submit is PHP), solution modal.
        let solutionShown = false;
        function showSolution() {
            if (!solutionShown) {
                const code = `<?php echo addslashes($lesson['solution_code']); ?>`;
                document.querySelector('textarea').value = code;
                solutionShown = true;
            }
        }
        // Simulate run: For demo, log to output on button, but actual check in PHP.
        document.querySelector('form').addEventListener('submit', (e) => {
            // Optional: Eval for preview (unsafe, but for learning).
            try {
                const code = document.querySelector('textarea').value;
                const output = document.getElementById('output');
                output.innerHTML = '<pre>' + eval(code) + '</pre>'; // Dangerous, use iframe in prod.
            } catch (err) {
                document.getElementById('output').innerHTML = '<pre>Error: ' + err.message + '</pre>';
            }
        });
        // Auto-resize textarea
        document.querySelector('textarea').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    </script>
</body>
</html>
