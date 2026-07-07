<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['name']) || empty($input['email']) || empty($input['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
    exit;
}

$to = SITE_EMAIL;
$subject = 'Contact Form: ' . htmlspecialchars($input['subject'] ?? 'General Inquiry');
$headers = 'From: ' . filter_var($input['email'], FILTER_SANITIZE_EMAIL) . "\r\n";
$headers .= 'Reply-To: ' . filter_var($input['email'], FILTER_SANITIZE_EMAIL) . "\r\n";
$message = "Name: " . htmlspecialchars($input['name']) . "\n";
$message .= "Email: " . htmlspecialchars($input['email']) . "\n";
$message .= "Subject: " . htmlspecialchars($input['subject'] ?? 'General Inquiry') . "\n\n";
$message .= "Message:\n" . htmlspecialchars($input['message']) . "\n";

$mail_sent = mail($to, $subject, $message, $headers);

echo json_encode([
    'success' => true,
    'message' => 'Message sent successfully!'
]);
