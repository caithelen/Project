<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-1">404</h1>
            <h2>Page Not Found</h2>
            <p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_SESSION['error']); ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>/" class="btn btn-primary mt-3">Go to Homepage</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
