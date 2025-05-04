<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/app/config/Database.php';

$error = '';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die('Sorry, there was a problem connecting to the database. Please try again later.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData && password_verify($password, $userData['password_hash'])) {
                // Debug user data
                error_log('User data from DB: ' . print_r($userData, true));
                
                // Preserve cart before session changes
                $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : null;
                
                // Store only essential user data in session
                $_SESSION['user'] = [
                    'user_id' => (int)$userData['user_id'],  // Ensure it's an integer
                    'username' => $userData['username'],
                    'email' => $userData['email']
                ];
                
                // Restore cart
                $_SESSION['cart'] = $cart;
                
                // Debug session
                error_log('Login successful - User ID: ' . $_SESSION['user']['user_id']);
                error_log('Cart preserved - Items: ' . count($_SESSION['cart']));
                
                // Debug session after setting
                error_log('Session after login: ' . print_r($_SESSION, true));

                if (isset($_POST['remember_me']) && $_POST['remember_me'] === 'on') {
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

                    $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
                    $stmt->execute([$userData['user_id']]);

                    $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires) VALUES (?, ?, ?)");
                    $stmt->execute([$userData['user_id'], $token, $expires]);

                    setcookie('remember_token', $token, [
                        'expires' => strtotime('+30 days'),
                        'path' => '/',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);
                }

                // Redirect to checkout if that's where they came from
                if (isset($_SESSION['redirect_after_login']) && $_SESSION['redirect_after_login'] === 'checkout.php') {
                    unset($_SESSION['redirect_after_login']);
                    header('Location: checkout.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            $error = "An error occurred during login. Please try again.";
        }
    }
}

require_once 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            color: #2e4a2e;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .remember-me {
            flex-direction: row;
            align-items: center;
            gap: 10px;
        }

        .login-submit {
            background: #1565c0;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .login-submit:hover {
            background: #0d47a1;
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        .register-link a {
            color: #1565c0;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <h2>Login to Your Account</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group remember-me">
                <input type="checkbox" id="remember_me" name="remember_me">
                <label for="remember_me">Remember me</label>
            </div>

            <button type="submit" class="login-submit">Login</button>

            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
