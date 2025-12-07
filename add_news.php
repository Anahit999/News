<?php
require_once 'config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Դուք պետք է մուտք գործեք']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['newsTitle']);
    $content = trim($_POST['newsContent']);
    $category = $_POST['newsCategory'];
    $imageUrl = trim($_POST['newsImage']);
    
    $errors = [];
    
    if (empty($title) || empty($content) || empty($category)) {
        $errors[] = 'Խնդրում ենք լրացնել բոլոր պարտադիր դաշտերը';
    }
    
    if (empty($errors)) {
        $pdo = getDB();
        $user = getCurrentUser();
        
        $stmt = $pdo->prepare("INSERT INTO news (title, content, category, image_url, author_id, author_name, created_at) VALUES (?, ?, ?, ?, ?, ?, datetime('now'))");
        
        if ($stmt->execute([$title, $content, $category, $imageUrl ?: null, $user['id'], $user['username']])) {
            echo json_encode(['success' => true, 'message' => 'Նորությունը հաջողությամբ հրապարակվել է']);
            exit;
        } else {
            $errors[] = 'Սխալ նորություն ավելացնելիս';
        }
    }
    
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// If not POST request, redirect to home
header('Location: index.html');
exit;
?>
