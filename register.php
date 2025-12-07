<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Validation
    $errors = [];
    
    if (empty($username) || strlen($username) < 3) {
        $errors[] = 'Օգտանունը պետք է լինի առնվազն 3 նիշ';
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Անվավեր էլ. հասցե';
    }
    
    if (strlen($password) < 6) {
        $errors[] = 'Գաղտնաբառը պետք է լինի առնվազն 6 նիշ';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Գաղտնաբառերը չեն համընկնում';
    }
    
    if (empty($errors)) {
        $pdo = getDB();
        
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $errors[] = 'Այս էլ. հասցեն արդեն գրանցված է';
        } else {
            // Create new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $hashedPassword])) {
                // Auto login after registration
                $userId = $pdo->lastInsertId();
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                
                echo json_encode(['success' => true, 'message' => 'Գրանցումը հաջող էր', 'username' => $username]);
                exit;
            } else {
                $errors[] = 'Սխալ գրանցման ժամանակ';
            }
        }
    }
    
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// If not POST request, redirect to home
header('Location: index.html');
exit;
?>
