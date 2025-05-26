<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    try {
        // Check if email already exists
        $check_query = "SELECT id FROM newsletter_subscribers WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'This email is already subscribed to our newsletter']);
            exit;
        }

        // Generate unsubscribe token
        $unsubscribe_token = bin2hex(random_bytes(32));

        // Insert new subscriber
        $insert_query = "INSERT INTO newsletter_subscribers (email, subscribed_at, is_active, unsubscribe_token) VALUES (?, NOW(), 1, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ss", $email, $unsubscribe_token);

        if ($insert_stmt->execute()) {
            // Send welcome email
            if (sendWelcomeEmail($email, $unsubscribe_token)) {
                echo json_encode(['success' => true, 'message' => 'Successfully subscribed! Please check your email for confirmation.']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Successfully subscribed to our newsletter!']);
            }
        } else {
            throw new Exception("Database insertion failed");
        }

        $insert_stmt->close();
        $check_stmt->close();

    } catch (Exception $e) {
        error_log("Newsletter subscription error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Subscription failed. Please try again later.']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// Function to send welcome email
function sendWelcomeEmail($email, $unsubscribe_token)
{
    // Email configuration
    $to = $email;
    $subject = "Welcome to Echoes of Today Newsletter!";

    // Create unsubscribe link
    $unsubscribe_link = "http://" . $_SERVER['HTTP_HOST'] . "/public/unsubscribe.php?token=" . $unsubscribe_token;

    // HTML email content
    $html_message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Welcome to Echoes of Today</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #1f4e79; color: white; padding: 20px; text-align: center; }
            .content { padding: 30px 20px; background-color: #f9f9f9; }
            .footer { background-color: #333; color: white; padding: 20px; text-align: center; font-size: 12px; }
            .button { display: inline-block; padding: 12px 24px; background-color: #c00000; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .unsubscribe { font-size: 12px; color: #666; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>ECHOES OF TODAY</h1>
                <p>The Voice of Our Times</p>
            </div>
            
            <div class='content'>
                <h2>Welcome to Our Newsletter!</h2>
                <p>Dear Subscriber,</p>
                
                <p>Thank you for subscribing to the <strong>Echoes of Today</strong> newsletter! We're thrilled to have you join our community of informed readers.</p>
                
                <p>Here's what you can expect from us:</p>
                <ul>
                    <li>📰 Breaking news delivered straight to your inbox</li>
                    <li>🌍 In-depth coverage of local and international events</li>
                    <li>💼 Business insights and market updates</li>
                    <li>⚽ Sports highlights and analysis</li>
                    <li>🎭 Entertainment and lifestyle content</li>
                </ul>
                
                <p>We promise to keep you informed with accurate, timely, and compelling news stories that matter to you.</p>
                
                <p style='text-align: center;'>
                    <a href='http://" . $_SERVER['HTTP_HOST'] . "/public/index.php' class='button'>Visit Our Website</a>
                </p>
                
                <p>Stay connected with us on social media for real-time updates!</p>
                
                <p>Best regards,<br>
                <strong>The Echoes of Today Team</strong></p>
            </div>
            
            <div class='footer'>
                <p>&copy; " . date('Y') . " Echoes of Today. All rights reserved.</p>
                <p>📧 info@echoesoftoday.com | 📞 +1-555-123-4567</p>
                <div class='unsubscribe'>
                    <p>You received this email because you subscribed to our newsletter.</p>
                    <p><a href='" . $unsubscribe_link . "' style='color: #ccc;'>Unsubscribe from this newsletter</a></p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";

    // Plain text version
    $text_message = "
Welcome to Echoes of Today Newsletter!

Dear Subscriber,

Thank you for subscribing to the Echoes of Today newsletter! We're thrilled to have you join our community of informed readers.

Here's what you can expect from us:
- Breaking news delivered straight to your inbox
- In-depth coverage of local and international events  
- Business insights and market updates
- Sports highlights and analysis
- Entertainment and lifestyle content

We promise to keep you informed with accurate, timely, and compelling news stories that matter to you.

Visit our website: http://" . $_SERVER['HTTP_HOST'] . "/public/index.php

Stay connected with us on social media for real-time updates!

Best regards,
The Echoes of Today Team

---
© " . date('Y') . " Echoes of Today. All rights reserved.
Email: info@echoesoftoday.com | Phone: +1-555-123-4567

You received this email because you subscribed to our newsletter.
To unsubscribe, visit: " . $unsubscribe_link . "
    ";

    // Email headers
    $headers = array();
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/html; charset=UTF-8";
    $headers[] = "From: Echoes of Today <noreply@echoesoftoday.com>";
    $headers[] = "Reply-To: info@echoesoftoday.com";
    $headers[] = "Return-Path: noreply@echoesoftoday.com";
    $headers[] = "X-Mailer: PHP/" . phpversion();

    // Send email using PHP's mail function
    return mail($to, $subject, $html_message, implode("\r\n", $headers));
}
?>