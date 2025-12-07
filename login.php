<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $errors = [];
    
    if (empty($email) || empty($password)) {
        $errors[] = 'Խնդրում ենք լրացնել բոլոր դաշտերը';
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            echo json_encode(['success' => true, 'message' => 'Մուտքը հաջող էր', 'username' => $user['username']]);
            exit;
        } else {
            $errors[] = 'Սխալ էլ. հասցե կամ գաղտնաբառ';
        }
    }
    
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// If not POST request, redirect to home
header('Location: index.html');
exit;
?>
