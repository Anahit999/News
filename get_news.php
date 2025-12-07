<?php
require_once 'config.php';

$pdo = getDB();
$category = $_GET['category'] ?? 'all';

if ($category === 'all') {
    $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 10");
} else {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE category = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$category]);
}

$news = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($news)) {
    echo '<p style="text-align: center; padding: 40px; color: #666;">Նորություններ չեն գտնվել</p>';
    exit;
}

foreach ($news as $item) {
    $categoryName = getCategoryName($item['category']);
    $date = date('Y-m-d', strtotime($item['created_at']));
    $imageUrl = $item['image_url'] ?: 'https://images.unsplash.com/photo-1588681664899-f142ff2dc9b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80';
    $excerpt = strlen($item['content']) > 150 ? substr($item['content'], 0, 150) . '...' : $item['content'];
    
    echo <<<HTML
    <div class="news-card">
        <div class="news-image" style="background-image: url('{$imageUrl}')"></div>
        <div class="news-content">
            <span class="news-category">{$categoryName}</span>
            <h3 class="news-title">{$item['title']}</h3>
            <p class="news-excerpt">{$excerpt}</p>
            <div class="news-meta">
                <span>{$item['author_name']}</span>
                <span>{$date}</span>
            </div>
        </div>
    </div>
HTML;
}
?>
