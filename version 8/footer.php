<?php
// Prevent direct access to this file
if (!defined('EURO_TOURS')) {
    define('EURO_TOURS', true);
}
?>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About EuroTours</h3>
                <p>Discover Europe's rich history and culture with our guided tours. We offer unforgettable experiences across the continent's most beautiful destinations.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php">Tours</a></li>
                    <li><a href="schedule.php">Schedule</a></li>
                    <li><a href="my_bookings.php">My Bookings</a></li>
                    <li><a href="cart.php">Shopping Cart</a></li>
                    <li><a href="support.php">Support</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <ul class="contact-info">
                    <li><i class="fas fa-envelope"></i> info@eurotours.com</li>
                    <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                    <li><i class="fas fa-map-marker-alt"></i> 123 Tour Street, Travel City</li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Newsletter</h3>
                <p>Subscribe to our newsletter for the latest updates and exclusive offers!</p>
                <form class="newsletter-form" action="subscribe.php" method="POST">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> EuroTours. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
.footer {
    background-color: #2e7d32;
    color: white;
    padding: 40px 0 20px;
    margin-top: 40px;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-bottom: 30px;
}

.footer-section h3 {
    color: white;
    margin-bottom: 15px;
    font-size: 1.2rem;
    position: relative;
    padding-bottom: 10px;
}

.footer-section h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 2px;
    background-color: #4CAF50;
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section ul li {
    margin-bottom: 12px;
}

.footer-section ul li a {
    color: white;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-block;
}

.footer-section ul li a:hover {
    color: #4CAF50;
    transform: translateX(5px);
}

.social-links {
    margin-top: 20px;
    display: flex;
    gap: 15px;
}

.social-link {
    color: white;
    font-size: 20px;
    transition: all 0.3s;
}

.social-link:hover {
    color: #4CAF50;
    transform: translateY(-3px);
}

.contact-info li {
    display: flex;
    align-items: center;
    gap: 10px;
}

.contact-info li i {
    color: #4CAF50;
}

.newsletter-form {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}

.newsletter-form input {
    flex: 1;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    outline: none;
}

.newsletter-form button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.newsletter-form button:hover {
    background-color: #45a049;
}

.footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-bottom p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.8;
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
    }
    
    .footer-section {
        margin-bottom: 30px;
        text-align: center;
    }

    .footer-section h3::after {
        left: 50%;
        transform: translateX(-50%);
    }

    .social-links {
        justify-content: center;
    }

    .contact-info li {
        justify-content: center;
    }

    .newsletter-form {
        flex-direction: column;
    }

    .newsletter-form input,
    .newsletter-form button {
        width: 100%;
    }
}
</style>
