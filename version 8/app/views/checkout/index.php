<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Checkout</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                            echo htmlspecialchars($_SESSION['error']);
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <h4>Order Summary</h4>
                    <div class="table-responsive">
                        <table class="table cart-table">
                            <thead>
                                <tr>
                                    <th>Trip</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['destination']); ?></td>
                                        <td>€<?php echo number_format($item['cost'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td><strong>€<?php echo number_format($this->cart->getTotal(), 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <form action="/checkout/process" method="POST" id="checkout-form">
                        <h4 class="mt-4">Payment Information</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="card-name" class="form-label">Name on Card</label>
                                <input type="text" class="form-control" id="card-name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="card-number" class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="card-number" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="expiry-month" class="form-label">Expiry Month</label>
                                <input type="text" class="form-control" id="expiry-month" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="expiry-year" class="form-label">Expiry Year</label>
                                <input type="text" class="form-control" id="expiry-year" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="cvv" required>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the terms and conditions
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100">Complete Purchase</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Order Details</h4>
                </div>
                <div class="card-body">
                    <p><strong>Items:</strong> <?php echo count($items); ?></p>
                    <p><strong>Total:</strong> €<?php echo number_format($this->cart->getTotal(), 2); ?></p>
                    <hr>
                    <p class="mb-0"><small>By completing your purchase you agree to our terms and conditions.</small></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
