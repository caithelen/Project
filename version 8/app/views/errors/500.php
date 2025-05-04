<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h1 class="display-1">500</h1>
            <h2>Internal Server Error</h2>
            <p class="lead">Something went wrong on our end. Please try again later.</p>
            <p class="text-muted"><?php echo isset($error) ? htmlspecialchars($error) : ''; ?></p>
            <a href="/" class="btn btn-primary">Go to Homepage</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
