<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=eurotours;charset=utf8mb4',
                'root',
                'Ehw2019!',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            // Check if username exists
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Username or email already exists.';
            } else {
                // Insert new user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, address) VALUES (?, ?, ?, ?)');
                $stmt->execute([$username, $email, $hashedPassword, $address]);
                
                $_SESSION['success_message'] = 'Registration successful! Please log in.';
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Registration failed. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eaf5ea;
            margin: 0;
            padding: 20px;
            color: #2e4a2e;
        }
        .container {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #2e4a2e;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #1565c0;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0d47a1;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h2>Create an Account</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="address">Address (optional):</label>
                <textarea id="address" name="address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
            </div>

            <button type="submit">Create Account</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php" style="color: #1565c0;">Login here</a>
        </p>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
