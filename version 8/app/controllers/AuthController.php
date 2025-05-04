<?php
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Admin.php';

class AuthController {
    public function loginForm() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function registerForm() {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function login() {
        try {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (!$email || !$password) {
                throw new Exception('Email and password are required');
            }

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                throw new Exception('Invalid credentials');
            }

            // Create appropriate user object based on role
            if ($user['role'] === 'admin') {
                $userObj = new Admin($user['username'], $user['email']);
            } else {
                $userObj = new Customer($user['username'], $user['email']);
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            header('Location: /');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /login');
            exit;
        }
    }

    public function register() {
        try {
            require_once __DIR__ . '/../models/Validator.php';
            
            $username = Validator::validateUsername($_POST['username'] ?? '');
            $email = Validator::validateEmail($_POST['email'] ?? '');
            $password = Validator::validatePassword($_POST['password'] ?? '');
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (!$username || !$email || !$password) {
                throw new Exception('All fields are required');
            }

            if ($password !== $confirmPassword) {
                throw new Exception('Passwords do not match');
            }

            $customer = new Customer($username, $email);
            $customer->setPassword($password);
            $customer->save();

            $_SESSION['success'] = 'Registration successful! Please log in.';
            header('Location: /login');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /register');
            exit;
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
