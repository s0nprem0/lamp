<?php
// Handle phpinfo request immediately to prevent layout breaking
if (isset($_GET['phpinfo'])) {
    phpinfo();
    exit;
}

// Database Connection Logic
$dbName = getenv('MYSQL_DATABASE');
$dbUser = getenv('MYSQL_USER');
$dbPass = getenv('MYSQL_PASSWORD');

$dbStatus = 'Not connected';
$isOnline = false;

if ($dbName && $dbUser && $dbPass !== false) {
    try {
        $pdo = new PDO("mysql:host=db;dbname=$dbName", $dbUser, $dbPass);
        $dbStatus = 'Connected (MariaDB ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . ')';
        $isOnline = true;
    } catch (Exception $e) {
        $dbStatus = 'Error: ' . $e->getMessage();
    }
} else {
    $dbStatus = 'Missing DB environment variables';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMP Hub Dashboard</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --danger-bg: #fee2e2;
            --danger-text: #991b1b;
            --border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            line-height: 1.6;
            padding: 2rem;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        header {
            margin-bottom: 2.5rem;
            text-align: center;
        }

        header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        header p {
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-size: 1.1rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }

        .card h2 {
            font-size: 1.25rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .data-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
        }

        .data-row:not(:last-child) {
            border-bottom: 1px dashed var(--border);
        }

        .data-label {
            font-weight: 600;
            color: var(--text-muted);
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .badge.online {
            background-color: var(--success-bg);
            color: var(--success-text);
        }

        .badge.offline {
            background-color: var(--danger-bg);
            color: var(--danger-text);
        }

        .code-block {
            background: #f1f5f9;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.875rem;
            color: #334155;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--primary);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .btn:hover {
            background-color: var(--primary-hover);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-outline:hover {
            background-color: #eef2ff;
        }

        .quick-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .quick-links li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .extensions-box {
            max-height: 120px;
            overflow-y: auto;
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.8;
        }

        @media (max-width: 600px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            body {
                padding: 1rem;
            }
            .quick-links li {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🔧 LAMP Hub</h1>
            <p>Local Development Environment Dashboard</p>
        </header>

        <div class="dashboard-grid">
            <div class="card">
                <h2>🖥️ Services Status</h2>
                <div class="data-row">
                    <span class="data-label">Web Server</span>
                    <span class="badge online">Apache / PHP <?php echo phpversion(); ?></span>
                </div>
                <div class="data-row">
                    <span class="data-label">Database</span>
                    <span class="badge <?php echo $isOnline ? 'online' : 'offline'; ?>">
                        <?php echo $isOnline ? 'Connected' : 'Offline'; ?>
                    </span>
                </div>
                <div class="data-row" style="flex-direction: column; align-items: flex-start; gap: 0.5rem; border-bottom: none;">
                    <span class="data-label">Database Details</span>
                    <span style="font-size: 0.875rem; color: var(--text-muted);">
                        <?php echo htmlspecialchars($dbStatus); ?>
                    </span>
                    <?php if ($dbName): ?>
                        <div style="margin-top: 0.25rem;">
                            <span class="data-label" style="font-size: 0.875rem;">Name:</span> <span class="code-block"><?php echo htmlspecialchars($dbName); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h2>📁 Environment Setup</h2>
                <div class="data-row">
                    <span class="data-label">Document Root</span>
                    <span class="code-block">/var/www/html</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Architecture</span>
                    <span class="code-block">Docker Compose</span>
                </div>
                <div class="data-row">
                    <span class="data-label">System User</span>
                    <span class="code-block"><?php echo exec('whoami'); ?></span>
                </div>
            </div>

            <div class="card">
                <h2>🔗 Quick Tools</h2>
                <ul class="quick-links">
                    <li>
                        <div>
                            <strong>phpMyAdmin</strong>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Login with DB credentials</div>
                        </div>
                        <a href="http://localhost:8080" target="_blank" class="btn">Launch 🚀</a>
                    </li>
                    <li>
                        <div>
                            <strong>Website Root</strong>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Navigate to public folder</div>
                        </div>
                        <a href="/" class="btn btn-outline">Open 🌐</a>
                    </li>
                </ul>
            </div>

            <div class="card">
                <h2>⚙️ PHP Configuration</h2>
                <div style="margin-bottom: 1.5rem;">
                    <span class="data-label" style="display: block; margin-bottom: 0.5rem;">Loaded Extensions:</span>
                    <div class="extensions-box">
                        <?php echo implode(', ', get_loaded_extensions()); ?>
                    </div>
                </div>
                <div>
                    <a href="?phpinfo=1" target="_blank" class="btn btn-outline" style="width: 100%; justify-content: center;">
                        📄 View Full phpinfo()
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>