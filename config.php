<?php
session_start();

// Database configuration
define('DB_FILE', 'database/news.db');

// Create database directory if it doesn't exist
if (!file_exists('database')) {
    mkdir('database', 0777, true);
}

// Initialize database
function initDatabase() {
    if (!file_exists(DB_FILE)) {
        $pdo = new PDO('sqlite:' . DB_FILE);
        
        // Create users table
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create news table
        $pdo->exec("CREATE TABLE IF NOT EXISTS news (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            category TEXT NOT NULL,
            image_url TEXT,
            author_id INTEGER,
            author_name TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (author_id) REFERENCES users(id)
        )");
        
        // Insert default admin user (password: admin123)
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute(['admin', 'admin@example.com', $hashedPassword]);
        
        // Insert sample news
        $sampleNews = [
            [
                'title' => 'Բենինում հեղաշրջման փորձը կանխվել է',
                'content' => 'Բենինի ներքին գործերի նախարարությունը հայտարարել է, որ հավատարիմ զորքերը կանխել են հեղաշրջման փորձ: Իշխանությունները ձերբակալել են մի քանի կասկածյալների և սկսել են հետաքննություն:',
                'category' => 'world',
                'image_url' => 'https://images.unsplash.com/photo-1588681664899-f142ff2dc9b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                'author_name' => 'Admin'
            ],
            [
                'title' => 'Արցախի հիմնախնդիրը ՄԱԿ-ում',
                'content' => 'Արցախի հիմնախնդիրը քննարկվել է Միավորված ազգերի կազմակերպությունում: Հայաստանի ներկայացուցիչները բողոք են ներկայացրել Ադրբեջանի գործողությունների դեմ:',
                'category' => 'politics',
                'image_url' => 'https://images.unsplash.com/photo-1542744095-fcf48d80b0fd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                'author_name' => 'Admin'
            ]
        ];
        
        foreach ($sampleNews as $news) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO news (title, content, category, image_url, author_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$news['title'], $news['content'], $news['category'], $news['image_url'], $news['author_name']]);
        }
        
        return $pdo;
    }
    
    return new PDO('sqlite:' . DB_FILE);
}

// Get database connection
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = initDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user
function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}

// Category names in Armenian
function getCategoryName($category) {
    $categories = [
        'politics' => 'Քաղաքականություն',
        'sport' => 'Սպորտ',
        'business' => 'Բիզնես',
        'culture' => 'Մշակույթ',
        'technology' => 'Տեխնոլոգիա',
        'world' => 'Աշխարհ',
        'health' => 'Առողջություն'
    ];
    
    return $categories[$category] ?? $category;
}
?>
