<?php
session_start();

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load autoloader
require_once BASE_PATH . '/app/config/autoload.php';

// Use statements
use App\Controllers\HomeController;
use App\Controllers\CartController;
use App\Controllers\ShopController;
use App\Controllers\AuthController;
use App\Controllers\CheckoutController;
use App\Controllers\BookingController;
use App\Controllers\ScheduleController;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Router
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Debug info
error_log("Original Request URI: " . $request);

// Remove base path from request
$basePath = '/version 7.0';
$request = str_replace($basePath, '', $request);
error_log("After base path removal: " . $request);

// Remove query string and trim slashes
$request = strtok($request, '?');
$request = trim($request, '/');
error_log("Final processed request: " . $request);

// Define routes
$routes = [
    'GET' => [
        '' => ['HomeController', 'index'],
        'cart' => ['CartController', 'index'],
        'shop' => ['ShopController', 'index'],
        'login' => ['AuthController', 'loginForm'],
        'register' => ['AuthController', 'registerForm'],
        'checkout' => ['CheckoutController', 'index'],
        'checkout/confirmation' => ['CheckoutController', 'confirmation'],
        'my-bookings' => ['BookingController', 'index'],
        'schedule' => ['ScheduleController', 'index'],
        'schedule/live-updates' => ['ScheduleController', 'getLiveUpdates']
    ],
    'POST' => [
        'cart/add' => ['CartController', 'add'],
        'cart/remove' => ['CartController', 'remove'],
        'cart/clear' => ['CartController', 'clear'],
        'login' => ['AuthController', 'login'],
        'register' => ['AuthController', 'register'],
        'logout' => ['AuthController', 'logout'],
        'checkout/process' => ['CheckoutController', 'process'],
        'bookings/cancel' => ['BookingController', 'cancel']
    ]
];

// Route the request
error_log("Available routes for {$method}: " . print_r(array_keys($routes[$method] ?? []), true));
error_log("Looking for route: {$request}");

if (isset($routes[$method][$request])) {
    [$controller, $action] = $routes[$method][$request];
    
    if (class_exists($controller)) {
        $controllerInstance = new $controller();
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
            exit;
        }
    }
}

// 404 if no route found
header("HTTP/1.0 404 Not Found");
require BASE_PATH . '/app/views/errors/404.php';
