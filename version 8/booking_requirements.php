<?php
session_start();
require_once 'TourTrip.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$host = 'localhost';
$db = 'euro';
$user = 'root';
$pass = 'Ehw2019!';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

$tripId = $_GET['trip_id'] ?? null;
$error = '';
$success = '';

if (!$tripId) {
    header('Location: trips.php');
    exit;
}

// Get trip details
$stmt = $pdo->prepare("SELECT * FROM trips WHERE id = ?");
$stmt->execute([$tripId]);
$trip = $stmt->fetch();

if (!$trip) {
    header('Location: trips.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $errors = [];
    
    // Validate dietary requirements
    $dietary = array_filter($_POST['dietary_requirements'] ?? [], function($item) {
        return !empty(trim($item));
    });
    
    // Validate accessibility needs
    $accessibility = array_filter($_POST['accessibility_needs'] ?? [], function($item) {
        return !empty(trim($item));
    });
    
    // Validate room preferences
    $preferences = array_filter($_POST['room_preferences'] ?? [], function($item) {
        return !empty(trim($item));
    });
    
    if (empty($errors)) {
        $requirements = [
            'dietary' => $dietary,
            'accessibility' => $accessibility,
            'preferences' => $preferences,
            'other' => $_POST['other_requirements'] ?? ''
        ];
        
        // Store requirements in session
        $_SESSION['booking_requirements'] = [
            'trip_id' => $tripId,
            'requirements' => $requirements
        ];
        
        // Log for debugging
        error_log('Stored booking requirements for trip ' . $tripId . ': ' . json_encode($requirements));
        
        // Redirect to checkout
        header('Location: checkout.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Special Requirements - EuroTours</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .requirements-form {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h4 {
            color: #2e4a2e;
            margin-bottom: 1rem;
        }
        
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .trip-summary {
            background: #f5f5f5;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .trip-details {
            color: #666;
            margin-top: 0.5rem;
        }
        
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .requirements-form {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            color: #2e4a2e;
            margin-bottom: 1rem;
        }
        
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        
        .checkbox-item label {
            cursor: pointer;
        }
        
        textarea {
            width: 100%;
            min-height: 100px;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }
        
        .trip-summary {
            background: #f5f5f5;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
        }
        
        .trip-summary h3 {
            margin-top: 0;
        }
        
        .requirements-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
        }
        
        .requirements-actions button {
            padding: 0.75rem 1.5rem;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <h2>Special Requirements</h2>
    
    <div class="trip-summary">
        <h3><?= htmlspecialchars($trip['destination']) ?></h3>
        <p class="trip-details">
            Duration: <?= htmlspecialchars($trip['duration']) ?> days<br>
            Price: €<?= number_format($trip['cost'], 2) ?>
        </p>
    </div>
    
    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST" class="requirements-form">
        <div class="form-section">
            <h4>Dietary Requirements</h4>
            <div class="checkbox-group">
                <label><input type="checkbox" name="dietary_requirements[]" value="vegetarian"> Vegetarian</label>
                <label><input type="checkbox" name="dietary_requirements[]" value="vegan"> Vegan</label>
                <label><input type="checkbox" name="dietary_requirements[]" value="gluten-free"> Gluten-free</label>
                <label><input type="checkbox" name="dietary_requirements[]" value="dairy-free"> Dairy-free</label>
                <label><input type="checkbox" name="dietary_requirements[]" value="halal"> Halal</label>
                <label><input type="checkbox" name="dietary_requirements[]" value="kosher"> Kosher</label>
            </div>
        </div>
        
        <div class="form-section">
            <h4>Accessibility Needs</h4>
            <div class="checkbox-group">
                <label><input type="checkbox" name="accessibility_needs[]" value="wheelchair"> Wheelchair Access</label>
                <label><input type="checkbox" name="accessibility_needs[]" value="ground-floor"> Ground Floor Room</label>
                <label><input type="checkbox" name="accessibility_needs[]" value="hearing"> Hearing Assistance</label>
                <label><input type="checkbox" name="accessibility_needs[]" value="visual"> Visual Assistance</label>
            </div>
        </div>
        
        <div class="form-section">
            <h4>Room Preferences</h4>
            <div class="checkbox-group">
                <label><input type="checkbox" name="room_preferences[]" value="non-smoking"> Non-smoking</label>
                <label><input type="checkbox" name="room_preferences[]" value="quiet"> Quiet Room</label>
                <label><input type="checkbox" name="room_preferences[]" value="extra-bed"> Extra Bed</label>
                <label><input type="checkbox" name="room_preferences[]" value="crib"> Baby Crib</label>
            </div>
        </div>
        
        <div class="form-section">
            <h4>Additional Requirements</h4>
            <textarea name="other_requirements" rows="4" placeholder="Please specify any other requirements or preferences..."></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="primary-btn">Save Requirements</button>
            <a href="cart.php" class="secondary-btn">Back to Cart</a>
        </div>
    </form>
        <p>Please let us know about any special requirements you may have for your trip.</p>
    </div>
    
    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="POST" class="requirements-form">
        <div class="form-section">
            <h3>Dietary Requirements</h3>
            <div class="checkbox-group">
                <div class="checkbox-item">
                    <input type="checkbox" id="vegetarian" name="dietary_requirements[]" value="vegetarian">
                    <label for="vegetarian">Vegetarian</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="vegan" name="dietary_requirements[]" value="vegan">
                    <label for="vegan">Vegan</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="gluten-free" name="dietary_requirements[]" value="gluten-free">
                    <label for="gluten-free">Gluten-free</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="dairy-free" name="dietary_requirements[]" value="dairy-free">
                    <label for="dairy-free">Dairy-free</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="nut-free" name="dietary_requirements[]" value="nut-free">
                    <label for="nut-free">Nut-free</label>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Accessibility Needs</h3>
            <div class="checkbox-group">
                <div class="checkbox-item">
                    <input type="checkbox" id="wheelchair" name="accessibility_needs[]" value="wheelchair">
                    <label for="wheelchair">Wheelchair Access</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="elevator" name="accessibility_needs[]" value="elevator">
                    <label for="elevator">Elevator Required</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="ground-floor" name="accessibility_needs[]" value="ground-floor">
                    <label for="ground-floor">Ground Floor Room</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="assistance" name="accessibility_needs[]" value="assistance">
                    <label for="assistance">Special Assistance</label>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Room Preferences</h3>
            <div class="checkbox-group">
                <div class="checkbox-item">
                    <input type="checkbox" id="non-smoking" name="room_preferences[]" value="non-smoking">
                    <label for="non-smoking">Non-smoking</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="quiet" name="room_preferences[]" value="quiet">
                    <label for="quiet">Quiet Room</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="early-check-in" name="room_preferences[]" value="early-check-in">
                    <label for="early-check-in">Early Check-in</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="late-check-out" name="room_preferences[]" value="late-check-out">
                    <label for="late-check-out">Late Check-out</label>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Other Requirements</h3>
            <textarea name="other_requirements" placeholder="Please specify any other requirements or special requests..."></textarea>
        </div>
        
        <div class="requirements-actions">
            <a href="trips.php" class="secondary-btn">Back to Trips</a>
            <button type="submit" class="primary-btn">Continue to Checkout</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.requirements-form');
    const checkboxGroups = document.querySelectorAll('.checkbox-group');
    
    form.addEventListener('submit', function(e) {
        let hasRequirements = false;
        
        // Check if any checkbox is checked
        checkboxGroups.forEach(group => {
            const checkedBoxes = group.querySelectorAll('input[type="checkbox"]:checked');
            if (checkedBoxes.length > 0) hasRequirements = true;
        });
        
        // Check if other requirements field is filled
        const otherReq = document.querySelector('textarea[name="other_requirements"]');
        if (otherReq && otherReq.value.trim()) hasRequirements = true;
        
        if (!hasRequirements) {
            e.preventDefault();
            alert('Please select at least one requirement or fill in the additional requirements field.');
        }
    });
});
</script>

<footer>
    <p>&copy; <?= date("Y"); ?> EuroTours. All rights reserved.</p>
</footer>

</body>
</html>
