<?php
session_start();

require_once __DIR__ . '/app/config/Database.php';

// Initialize variables
$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'customer'; // Default role
    $address = trim($_POST['address'] ?? '');
    
    // Validate input
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
    
    if (empty($errors)) {
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            // Check if username or email exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'Username or email already exists';
            } else {
                // Hash password and insert user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, address) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashedPassword, $role, $address]);
                
                $_SESSION['success_message'] = 'Registration successful! Please log in.';
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Registration error: ' . $e->getMessage());
            $errors[] = 'Registration failed. Please try again later.';
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
            background-color: #eaf5ea;
            font-family: Arial, sans-serif;
            color: #2e4a2e;
        }
        .register-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #2e4a2e;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #1565c0;
            outline: none;
            box-shadow: 0 0 0 2px rgba(21, 101, 192, 0.1);
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .submit-btn {
            background-color: #1565c0;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #0d47a1;
        }
        h2 {
            color: #2e7d32;
            text-align: center;
            margin-bottom: 30px;
        }
        a {
            color: #1565c0;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="register-container">
    <h2>Create an Account</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success_message']); ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <form method="POST" id="registrationForm">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required 
                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required 
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="address">Address (optional):</label>
            <textarea id="address" name="address" rows="3"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
        </div>

        <input type="hidden" name="role" value="customer">

        <button type="submit" class="submit-btn">Create Account</button>

        <p style="margin-top: 20px; text-align: center;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
