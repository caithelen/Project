<?php
class Validator {
    public static function sanitizeInput($input, $type = 'string') {
        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            default:
                return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
        }
    }

    public static function validateEmail($email) {
        $email = self::sanitizeInput($email, 'email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        return $email;
    }

    public static function validatePassword($password, $minLength = 8) {
        if (strlen($password) < $minLength) {
            throw new InvalidArgumentException("Password must be at least {$minLength} characters long");
        }
        if (!preg_match('/[A-Z]/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one uppercase letter');
        }
        if (!preg_match('/[a-z]/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one lowercase letter');
        }
        if (!preg_match('/[0-9]/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one number');
        }
        return $password;
    }

    public static function validateUsername($username, $minLength = 3) {
        $username = self::sanitizeInput($username);
        if (strlen($username) < $minLength) {
            throw new InvalidArgumentException("Username must be at least {$minLength} characters long");
        }
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            throw new InvalidArgumentException('Username can only contain letters, numbers, underscores and dashes');
        }
        return $username;
    }

    public static function validatePrice($price) {
        $price = self::sanitizeInput($price, 'float');
        if (!is_numeric($price) || $price <= 0) {
            throw new InvalidArgumentException('Price must be a positive number');
        }
        return $price;
    }

    public static function validateDate($date) {
        $date = self::sanitizeInput($date);
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            throw new InvalidArgumentException('Invalid date format');
        }
        return date('Y-m-d H:i:s', $timestamp);
    }

    public static function validatePhone($phone) {
        $phone = self::sanitizeInput($phone);
        if (!preg_match('/^\+?[1-9]\d{1,14}$/', $phone)) {
            throw new InvalidArgumentException('Invalid phone number format');
        }
        return $phone;
    }
}
