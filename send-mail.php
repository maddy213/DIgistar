```php
<?php

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);
    exit;
}

/*
 * Honeypot spam protection.
 * Real users should leave this field empty.
 */
if (!empty($_POST['website'])) {
    echo json_encode([
        'success' => true
    ]);
    exit;
}

/*
 * Get and clean form values.
 */
$name = trim($_POST['name'] ?? '');
$company = trim($_POST['company'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

/*
 * Basic validation.
 */
if ($name === '' || $company === '' || $email === '') {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Please fill in all required fields.'
    ]);

    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ]);

    exit;
}

/*
 * Destination email.
 */
$to = 'contact@digistar.in';

/*
 * Email subject.
 */
$subject = 'New DigiStar Website Enquiry';

/*
 * Email body.
 */
$body = "New enquiry received from the DigiStar website.\n\n";

$body .= "Name: " . $name . "\n";
$body .= "Company: " . $company . "\n";
$body .= "Email: " . $email . "\n";
$body .= "Phone / WhatsApp: " . ($phone ?: 'Not provided') . "\n\n";

$body .= "What are you trying to grow?\n";
$body .= ($message ?: 'Not provided') . "\n";

/*
 * Email headers.
 *
 * The From address should normally be an email address
 * belonging to your own domain.
 */
$headers = [];
$headers[] = 'From: DigiStar Website <contact@digistar.in>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';

$headersString = implode("\r\n", $headers);

/*
 * Send email.
 */
$sent = mail(
    $to,
    $subject,
    $body,
    $headersString
);

if ($sent) {
    echo json_encode([
        'success' => true
    ]);
} else {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'The server could not send the email.'
    ]);
}
?>
```
