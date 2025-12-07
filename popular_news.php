<?php
require_once 'config.php';

$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM news ORDER BY RANDOM() LIMIT 4");
$popularNews = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($popularNews as $news) {
    $categoryName = getCategoryName($news['category']);
    echo <<<HTML
    <div class="popular-item" style="margin-top: 20px; padding: 15px 0; border-bottom: 1px solid #eee;">
        <h3 style="font-size: 16px; margin-bottom: 5px;">{$news['title']}</h3>
        <p style="font-size: 14px; color: #888;">{$categoryName}</p>
    </div>
HTML;
}
?>
