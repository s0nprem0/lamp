<?php
if (isset($_GET['phpinfo'])) {
    phpinfo(); exit;
}

$dbName = getenv('MYSQL_DATABASE');
$dbUser = getenv('MYSQL_USER');
$dbPass = getenv('MYSQL_PASSWORD');

$dbStatus = 'Not connected';
$isOnline = false;

try {
    $pdo = new PDO("mysql:host=db;dbname=$dbName", $dbUser, $dbPass);
    $dbStatus = 'Connected (MariaDB ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . ')';
    $isOnline = true;
} catch (Exception $e) {
    $dbStatus = 'Error: ' . $e->getMessage();
}

// Scan projects
$projects = [];
if (is_dir('/var/www/html/projects')) {
    $dirs = array_diff(scandir('/var/www/html/projects'), ['.', '..']);
    foreach ($dirs as $dir) {
        if (is_dir('/var/www/html/projects/' . $dir)) {
            $projects[] = $dir;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMP - Localhost</title>
    <style>
        :root {
            --primary: #0078d7;
            --success: #107c10;
            --danger: #d13438;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f2f1;
            color: #242424;
            margin: 0;
            padding: 20px;
        }
        .header {
            background: white;
            padding: 20px;
            border-bottom: 4px solid var(--primary);
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 { margin: 0; color: #0078d7; }
        .status { display: flex; gap: 30px; justify-content: center; margin: 20px 0; }
        .card {
            background: white;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .project-list a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #0078d7;
        }
        .project-list a:hover { background: #f3f2f1; }
        .green { color: var(--success); font-weight: bold; }
        .red { color: var(--danger); font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔥 LAMP Server</h1>
        <p>Your local development environment</p>
    </div>

    <div class="status">
        <div>Apache + PHP <?php echo phpversion(); ?> <span class="green">● Online</span></div>
        <div>Database: <span class="<?php echo $isOnline ? 'green' : 'red'; ?>"><?php echo $isOnline ? 'Online' : 'Offline'; ?></span></div>
    </div>

    <div class="card">
        <h2>📁 Your Projects</h2>
        <?php if (empty($projects)): ?>
            <p>No projects yet. Add folders inside the <strong>www/</strong> directory.</p>
        <?php else: ?>
            <div class="project-list">
                <?php foreach ($projects as $p): ?>
                    <a href="/projects/<?php echo htmlspecialchars($p); ?>" target="_blank">
                        📁 <?php echo htmlspecialchars($p); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <p><a href="/projects" style="color:#666;">→ Browse all files (Directory Listing)</a></p>
    </div>

    <div class="card">
        <h2>🔧 Quick Tools</h2>
        <p>
            <a href="http://localhost:8080" target="_blank">phpMyAdmin</a> |
            <a href="?phpinfo=1" target="_blank">phpinfo()</a> |
            <a href="/projects">Open Projects Folder</a>
        </p>
    </div>
</body>
</html>
