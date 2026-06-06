<?php
if (isset($_GET['phpinfo'])) {
    phpinfo(); exit;
}

$dbName = getenv('MYSQL_DATABASE') ?: 'app';
$dbUser = getenv('MYSQL_USER') ?: 'appuser';
$dbPass = getenv('MYSQL_PASSWORD') ?: 'apppass';

$pmaUrl = getenv('PMA_URL') ?: 'http://localhost:8080';
$projectUrl = getenv('PROJECT_URL') ?: 'http://localhost';

$isOnline = false;
try {
    $pdo = new PDO("mysql:host=db;dbname=$dbName", $dbUser, $dbPass);
    $isOnline = true;
} catch (Exception $e) {
    // silent
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LAMP Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f3f2f1; margin: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .green { color: #107c10; font-weight: bold; }
        .red { color: #d13438; font-weight: bold; }
        a { color: #0078d7; text-decoration: none; margin-right: 15px; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔥 LAMP Dashboard</h1>
        <p>Apache + PHP <?php echo phpversion(); ?> | <span class="green">● Online</span></p>
        <p>Database: <span class="<?php echo $isOnline ? 'green' : 'red'; ?>"><?php echo $isOnline ? 'Connected' : 'Offline'; ?></span></p>
    </div>
    <div class="card">
        <h2>🔧 Tools</h2>
        <a href="<?= htmlspecialchars($pmaUrl) ?>" target="_blank">phpMyAdmin</a>
        <a href="?phpinfo=1" target="_blank">phpinfo()</a>
        <a href="<?= htmlspecialchars($projectUrl) ?>" target="_blank">Browse Projects (port 80)</a>
    </div>
</body>
</html>
