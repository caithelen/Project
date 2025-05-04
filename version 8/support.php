<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/Database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .support-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .support-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .contact-list {
            list-style: none;
            padding: 0;
        }

        .contact-list li {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 10px;
        }

        .icon {
            font-size: 24px;
        }

        .faq-item {
            margin-bottom: 20px;
        }

        .faq-item h4 {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .ticket-form {
            display: grid;
            gap: 15px;
        }

        .ticket-form input,
        .ticket-form textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .ticket-form button {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .ticket-form button:hover {
            background: #1b5e20;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Customer Support</h1>
        
        <div class="support-grid">
            <div class="support-card">
                <h2>Contact Us</h2>
                <p>Get in touch with our support team:</p>
                <ul class="contact-list">
                    <li>
                        <span class="icon">📧</span>
                        <span class="detail">Email: support@eurotours.com</span>
                    </li>
                    <li>
                        <span class="icon">📞</span>
                        <span class="detail">Phone: +1 234 567 8900</span>
                    </li>
                    <li>
                        <span class="icon">💬</span>
                        <span class="detail">Live Chat: Available 24/7</span>
                    </li>
                </ul>
            </div>

        <div class="support-card">
            <div class="support-card">
                <h2>FAQ</h2>
                <div class="faq-list">
                    <div class="faq-item">
                        <h4>How do I book a trip?</h4>
                        <p>You can book a trip by browsing our available routes in the Shop section and clicking "Book Now" on your desired trip.</p>
                    </div>
                    <div class="faq-item">
                        <h4>What's your cancellation policy?</h4>
                        <p>Trips can be cancelled up to 24 hours before departure for a full refund.</p>
                    </div>
                    <div class="faq-item">
                        <h4>How early should I arrive?</h4>
                        <p>Please arrive at least 15 minutes before your scheduled departure time.</p>
                    </div>
                </div>
            </div>

            <div class="support-card">
                <h2>Submit a Ticket</h2>
                <form class="ticket-form" action="process_ticket.php" method="POST">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Ticket</button>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
