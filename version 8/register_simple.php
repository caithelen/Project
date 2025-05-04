<?php
session_start();

// Initialize variables
$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    
    try {
        // Connect to database
        $pdo = new PDO(
            'mysql:host=localhost;dbname=euro;charset=utf8mb4',
            'root',
            'Ehw2019!',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Check if users table exists in euro database
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() === 0) {
            // Create users table if it doesn't exist
            $pdo->exec("CREATE TABLE users (
                user_id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                role ENUM('customer', 'admin') DEFAULT 'customer',
                address TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        }
        
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, address) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $username,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $address
        ]);
        
        header('Location: login.php?registered=1');
        exit;
        
    } catch (PDOException $e) {
        $errors[] = 'Registration failed: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EuroTours</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eaf5ea;
            margin: 0;
            padding: 20px;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #1565c0;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #0d47a1;
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Create Account</h2>
        
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required 
                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required 
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div>
            <label for="address">Address (optional):</label>
            <textarea id="address" name="address" rows="3"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
        </div>
        
        <button type="submit">Register</button>
        
        <p style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </form>
</body>
</html>
