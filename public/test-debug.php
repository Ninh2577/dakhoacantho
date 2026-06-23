<?php
header('Content-Type: text/plain; charset=utf-8');

// Parse .env manually
$envFile = __DIR__.'/../.env';
if (!file_exists($envFile)) {
    die(".env file not found.");
}

$env = [];
$lines = file($envFile);
foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $env[trim($parts[0])] = trim($parts[1], '"\' ');
    }
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db   = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';

echo "Connecting to $db at $host:$port...\n";

try {
    $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "Connected successfully!\n\n";
    
    echo "=== ARTICLE 1222 DATABASE STATUS ===\n";
    $stmt = $pdo->prepare("SELECT id, title, slug, category_id, author_id, is_published, updated_at, LENGTH(content) as content_len, SUBSTRING(content, 1, 200) as content_start FROM articles WHERE id = 1222");
    $stmt->execute();
    $article = $stmt->fetch();
    
    if ($article) {
        foreach ($article as $key => $val) {
            echo "$key: " . var_export($val, true) . "\n";
        }
    } else {
        echo "Article 1222 not found.\n";
    }

} catch (\Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
