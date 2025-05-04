<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EuroTours - Your Travel Partner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="bg-success text-white">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="<?php echo BASE_URL; ?>/">EuroTours</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/shop">Shop</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/cart">Cart</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/schedule">Live Schedule</a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/my-bookings">My Bookings</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <div class="d-flex">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="<?php echo BASE_URL; ?>/my-bookings" class="btn btn-outline-light me-2">My Bookings</a>
                            <form action="<?php echo BASE_URL; ?>/logout" method="POST" class="d-inline">
                                <button type="submit" class="btn btn-outline-light">Logout</button>
                            </form>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/login" class="btn btn-outline-light me-2">Login</a>
                            <a href="<?php echo BASE_URL; ?>/register" class="btn btn-outline-light">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main>
