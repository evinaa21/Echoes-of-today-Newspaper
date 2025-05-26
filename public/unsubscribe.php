<?php
require_once '../includes/db_connection.php';

$message = '';
$success = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Find and deactivate the subscriber
    $query = "UPDATE newsletter_subscribers SET is_active = 0, unsubscribed_at = NOW() WHERE unsubscribe_token = ? AND is_active = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $message = "You have been successfully unsubscribed from our newsletter. We're sorry to see you go!";
        $success = true;
    } else {
        $message = "Invalid unsubscribe link or you're already unsubscribed.";
        $success = false;
    }
} else {
    $message = "Invalid unsubscribe request.";
    $success = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Unsubscribed' : 'Error'; ?> - Echoes of Today</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .unsubscribe-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            text-align: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .success-icon {
            color: #28a745;
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .error-icon {
            color: #dc3545;
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--secondary-color);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="unsubscribe-container">
            <?php if ($success): ?>
                <div class="success-icon">✓</div>
                <h2>Successfully Unsubscribed</h2>
            <?php else: ?>
                <div class="error-icon">✗</div>
                <h2>Unsubscribe Error</h2>
            <?php endif; ?>

            <p><?php echo htmlspecialchars($message); ?></p>

            <?php if ($success): ?>
                <p>If you change your mind, you can always subscribe again on our homepage.</p>
            <?php endif; ?>

            <a href="index.php" class="btn">Return to Homepage</a>
        </div>
    </div>
</body>

</html>