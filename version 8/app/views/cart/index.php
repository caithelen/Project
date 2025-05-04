<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h1>Shopping Cart</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">Your cart is empty.</div>
        <a href="/shop" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table cart-table">
                <thead>
                    <tr>
                        <th>Destination</th>
                        <th>Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['destination']); ?></td>
                            <td>€<?php echo number_format($item['cost'], 2); ?></td>
                            <td>
                                <form action="/cart/remove" method="POST" class="d-inline">
                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm remove-btn">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td colspan="2"><strong>€<?php echo number_format($total, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="/shop" class="btn btn-primary">Continue Shopping</a>
            <div>
                <form action="/cart/clear" method="POST" class="d-inline">
                    <button type="submit" class="btn btn-warning">Clear Cart</button>
                </form>
                <a href="/checkout" class="btn btn-success ms-2">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
