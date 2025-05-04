<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .welcome-section {
            text-align: center;
            padding: 40px 20px;
            background: #f5f5f5;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .features-list {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .features-list li {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .destinations-section {
            padding: 40px 20px;
        }

        .destinations-list {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .destinations-list li {
            background: #1565c0;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
        }

        .cta-section {
            background: #2e7d32;
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-radius: 8px;
            margin: 30px 0;
        }

        .contact-section {
            text-align: center;
            padding: 40px 20px;
            background: #f5f5f5;
            border-radius: 8px;
        }

        .site-footer {
            text-align: center;
            padding: 20px;
            background: #333;
            color: white;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <section class="welcome-section">
            <h2>Why Choose EuroTours?</h2>
            <ul class="features-list">
                <li>Comfortable and modern buses</li>
                <li>Affordable prices with no hidden fees</li>
                <li>Direct routes to major European cities</li>
                <li>On-time departures and arrivals</li>
                <li>Easy online booking and seat selection</li>
            </ul>
        </section>

        <section class="destinations-section">
            <h2>Popular Destinations</h2>
            <p>Travel with us to exciting cities like:</p>
            <ul class="destinations-list">
                <li>London to Paris</li>
                <li>Berlin to Amsterdam</li>
                <li>Madrid to Barcelona</li>
            </ul>
        </section>

        <section class="cta-section">
            <h3>Start your journey with EuroTours today!</h3>
            <p>Book your ticket now and experience stress-free travel.</p>
        </section>

        <section class="contact-section">
            <h3>Contact Us</h3>
            <p>For inquiries, contact us at: <strong>support@eurotours.com</strong></p>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
