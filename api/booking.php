<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request data']);
        exit;
    }

    $required = ['apartment_id', 'check_in', 'check_out', 'guest_name', 'guest_email'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
            exit;
        }
    }

    $booking = [
        'id' => generate_id(),
        'apartment_id' => $input['apartment_id'],
        'apartment_name' => $input['apartment_name'] ?? 'Unknown',
        'check_in' => $input['check_in'],
        'check_out' => $input['check_out'],
        'guests' => intval($input['guests'] ?? 1),
        'guest_name' => $input['guest_name'],
        'guest_email' => $input['guest_email'],
        'guest_phone' => $input['guest_phone'] ?? '',
        'total' => floatval($input['total'] ?? 0),
        'nights' => intval($input['nights'] ?? 1),
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'payment_method' => $input['payment_method'] ?? 'credit_card',
        'created_at' => date('Y-m-d H:i:s'),
        'notes' => $input['notes'] ?? ''
    ];

    // Check availability again
    if (!is_available($booking['apartment_id'], $booking['check_in'], $booking['check_out'])) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Apartment is no longer available for the selected dates.']);
        exit;
    }

    $bookings = load_data('bookings.json');
    $bookings[] = $booking;
    save_data('bookings.json', $bookings);

    // Send confirmation email
    send_booking_confirmation($booking['guest_email'], $booking);

    echo json_encode([
        'success' => true,
        'booking_id' => $booking['id'],
        'message' => 'Booking confirmed successfully!'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'apartments') {
            echo json_encode(['success' => true, 'data' => load_data('apartments.json')]);
            exit;
        }
        if ($_GET['action'] === 'bookings') {
            $b = load_data('bookings.json');
            $status = $_GET['status'] ?? '';
            if ($status) $b = array_filter($b, fn($bk) => $bk['status'] === $status);
            echo json_encode(['success' => true, 'data' => array_values($b)]);
            exit;
        }
    }
    $bookings = load_data('bookings.json');
    $status = $_GET['status'] ?? '';
    if ($status) {
        $bookings = array_filter($bookings, fn($b) => $b['status'] === $status);
    }
    echo json_encode(['success' => true, 'data' => array_values($bookings)]);
    exit;
}

// Admin: update apartment / toggle featured
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($_GET['action'] === 'update_apartment' && $input) {
        $apartments = load_data('apartments.json');
        foreach ($apartments as &$a) {
            if ($a['id'] === $input['id']) {
                if (isset($input['name'])) $a['name'] = $input['name'];
                if (isset($input['location'])) $a['location'] = $input['location'];
                if (isset($input['price_per_night'])) $a['price_per_night'] = intval($input['price_per_night']);
                if (isset($input['bedrooms'])) $a['bedrooms'] = intval($input['bedrooms']);
                if (isset($input['description'])) $a['description'] = $input['description'];
                save_data('apartments.json', $apartments);
                echo json_encode(['success' => true, 'message' => 'Apartment updated']);
                exit;
            }
        }
        echo json_encode(['success' => false, 'error' => 'Apartment not found']);
        exit;
    }

    if ($_GET['action'] === 'toggle_featured' && $input) {
        $apartments = load_data('apartments.json');
        foreach ($apartments as &$a) {
            if ($a['id'] === $input['id']) {
                $a['featured'] = !$a['featured'];
                save_data('apartments.json', $apartments);
                echo json_encode(['success' => true, 'message' => 'Featured status toggled']);
                exit;
            }
        }
        echo json_encode(['success' => false, 'error' => 'Apartment not found']);
        exit;
    }
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);
