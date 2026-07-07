<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

define('SITE_NAME', 'Tunu Apartments Zanzibar');
define('SITE_URL', 'https://tunuapartments.com');
define('SITE_DESC', 'Modern serviced apartments in Zanzibar. Book your perfect stay with instant confirmation, secure payments, and 24/7 support.');
define('SITE_EMAIL', 'hello@tunuapartments.com');
define('SITE_PHONE', '+255 657 164 112');
define('SITE_WHATSAPP', '255657164112');
define('SITE_ADDRESS', 'Chukwani, Zanzibar, Tanzania');
define('SITE_INSTAGRAM', 'tunu_apartment');
define('SITE_CURRENCY', 'USD');
define('CHECK_IN_TIME', '14:00');
define('CHECK_OUT_TIME', '11:00');
define('CANCELLATION_DAYS', 7);

$data_dir = __DIR__ . '/../data';
if (!is_dir($data_dir)) { mkdir($data_dir, 0755, true); }

function load_data($file) {
    global $data_dir;
    $path = $data_dir . '/' . $file;
    if (!file_exists($path)) return [];
    $data = json_decode(file_get_contents($path), true);
    return $data ?: [];
}

function save_data($file, $data) {
    global $data_dir;
    $path = $data_dir . '/' . $file;
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

function generate_id() {
    return bin2hex(random_bytes(12));
}

function calculate_nights($check_in, $check_out) {
    $start = new DateTime($check_in);
    $end = new DateTime($check_out);
    return (int) $start->diff($end)->days;
}

function calculate_total($price_per_night, $check_in, $check_out) {
    $nights = calculate_nights($check_in, $check_out);
    return $price_per_night * $nights;
}

function format_price($amount) {
    return '$' . number_format($amount, 0);
}

function time_elapsed($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

function is_available($apartment_id, $check_in, $check_out) {
    $bookings = load_data('bookings.json');
    foreach ($bookings as $b) {
        if ($b['apartment_id'] !== $apartment_id) continue;
        if ($b['status'] !== 'confirmed' && $b['status'] !== 'pending') continue;
        $b_in = $b['check_in'];
        $b_out = $b['check_out'];
        if ($check_in < $b_out && $check_out > $b_in) return false;
    }
    return true;
}

function get_available_apartments($check_in, $check_out) {
    $apartments = load_data('apartments.json');
    $available = [];
    foreach ($apartments as $apt) {
        if (is_available($apt['id'], $check_in, $check_out)) {
            $available[] = $apt;
        }
    }
    return $available;
}

function send_booking_confirmation($email, $data) {
    $to = $email;
    $subject = 'Booking Confirmed - ' . SITE_NAME;
    $headers = 'From: ' . SITE_EMAIL . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $message = '<html><body style="font-family:Inter,sans-serif;padding:40px;background:#f9fafb;">';
    $message .= '<div style="max-width:600px;margin:0 auto;background:white;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">';
    $message .= '<div style="background:#1a1a1a;padding:32px;text-align:center;">';
    $message .= '<h1 style="font-family:Playfair Display,serif;color:#C9A84C;margin:0;">Booking Confirmed</h1>';
    $message .= '<p style="color:white;margin-top:8px;">' . SITE_NAME . '</p></div>';
    $message .= '<div style="padding:32px;">';
    $message .= '<h2 style="font-size:20px;margin-bottom:16px;">Hello ' . htmlspecialchars($data['guest_name']) . ',</h2>';
    $message .= '<p style="color:#6B7280;margin-bottom:24px;">Your booking has been confirmed. Here are the details:</p>';
    $message .= '<table style="width:100%;border-collapse:collapse;">';
    $message .= '<tr><td style="padding:12px 0;border-bottom:1px solid #f3f4f6;color:#6B7280;">Apartment</td><td style="padding:12px 0;border-bottom:1px solid #f3f4f6;font-weight:600;">' . htmlspecialchars($data['apartment_name']) . '</td></tr>';
    $message .= '<tr><td style="padding:12px 0;border-bottom:1px solid #f3f4f6;color:#6B7280;">Check-in</td><td style="padding:12px 0;border-bottom:1px solid #f3f4f6;font-weight:600;">' . htmlspecialchars($data['check_in']) . '</td></tr>';
    $message .= '<tr><td style="padding:12px 0;border-bottom:1px solid #f3f4f6;color:#6B7280;">Check-out</td><td style="padding:12px 0;border-bottom:1px solid #f3f4f6;font-weight:600;">' . htmlspecialchars($data['check_out']) . '</td></tr>';
    $message .= '<tr><td style="padding:12px 0;border-bottom:1px solid #f3f4f6;color:#6B7280;">Guests</td><td style="padding:12px 0;border-bottom:1px solid #f3f4f6;font-weight:600;">' . intval($data['guests']) . '</td></tr>';
    $message .= '<tr><td style="padding:12px 0;color:#6B7280;">Total</td><td style="padding:12px 0;font-weight:700;color:#C9A84C;font-size:20px;">' . format_price($data['total']) . '</td></tr>';
    $message .= '</table>';
    $message .= '<a href="' . SITE_URL . '/portal" style="display:block;text-align:center;margin-top:32px;padding:14px 32px;background:linear-gradient(135deg,#C9A84C,#A8883A);color:white;text-decoration:none;border-radius:8px;font-weight:600;">View My Bookings</a>';
    $message .= '</div></div></body></html>';
    return mail($to, $subject, $message, $headers);
}

$apartments = load_data('apartments.json');
$reviews = load_data('reviews.json');
$bookings = load_data('bookings.json');
$page_title = SITE_NAME;

function init_default_data() {
    if (empty(load_data('apartments.json'))) {
        $default_apartments = [
            [
                'id' => 'apt_001',
                'name' => 'Chukwani Garden Studio',
                'slug' => 'chukwani-garden-studio',
                'location' => 'Chukwani',
                'description' => 'A serene garden studio in the heart of Chukwani. Surrounded by tropical greenery, this studio features a king-size bed, fully equipped kitchen, and a private veranda. Perfect for couples seeking peace and privacy near Zanzibar City.',
                'price_per_night' => 85,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'max_guests' => 2,
                'amenities' => ['WiFi', 'Air Conditioning', 'Kitchen', 'Parking', 'Generator Backup', 'Hot Water', 'Garden View'],
                'images' => ['https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800', 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800'],
                'featured' => true,
                'coordinates' => [-6.2500, 39.2333]
            ],
            [
                'id' => 'apt_002',
                'name' => 'Mbweni Beach Suite',
                'slug' => 'mbweni-beach-suite',
                'location' => 'Mbweni',
                'description' => 'Spacious suite near Mbweni beach with ocean views. Walking distance to local restaurants and the Mbweni Ruins. Features an open-plan living area, modern furnishings, and a terrace with Indian Ocean views.',
                'price_per_night' => 120,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'max_guests' => 4,
                'amenities' => ['WiFi', 'Air Conditioning', 'Kitchen', 'Parking', 'Generator Backup', 'Hot Water', 'Ocean View'],
                'images' => ['https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?w=800', 'https://images.unsplash.com/photo-1560185007-cde436f6a4d0?w=800'],
                'featured' => true,
                'coordinates' => [-6.2333, 39.2167]
            ],
            [
                'id' => 'apt_003',
                'name' => 'Paje Ocean Villa',
                'slug' => 'paje-ocean-villa',
                'location' => 'Paje',
                'description' => 'Beautiful three-bedroom villa overlooking the turquoise waters of Paje. Ideal for families or groups. Includes a private pool, outdoor lounge, and direct beach access. The perfect base for kite surfing adventures on Zanzibar\'s southeast coast.',
                'price_per_night' => 250,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'max_guests' => 6,
                'amenities' => ['WiFi', 'Air Conditioning', 'Kitchen', 'Parking', 'Generator Backup', 'Private Pool', 'Beach Access'],
                'images' => ['https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800', 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800'],
                'featured' => true,
                'coordinates' => [-6.4333, 39.5500]
            ],
            [
                'id' => 'apt_004',
                'name' => 'Kiembe Samaki Residence',
                'slug' => 'kiembe-samaki-residence',
                'location' => 'Kiembe Samaki',
                'description' => 'Modern family residence in the peaceful Kiembe Samaki neighborhood. Features three bedrooms, a spacious living area, and a rooftop terrace. Close to local markets and a short drive to Zanzibar City center.',
                'price_per_night' => 130,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'max_guests' => 6,
                'amenities' => ['WiFi', 'Air Conditioning', 'Kitchen', 'Parking', 'Generator Backup', 'Rooftop Terrace', 'Hot Water'],
                'images' => ['https://images.unsplash.com/photo-1600566753086-00f18f6b3ed0?w=800', 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800'],
                'featured' => false,
                'coordinates' => [-6.2000, 39.2667]
            ],
            [
                'id' => 'apt_005',
                'name' => 'Michenzani City Apartment',
                'slug' => 'michenzani-city-apartment',
                'location' => 'Michenzani',
                'description' => 'Conveniently located two-bedroom apartment in Michenzani, close to shops, restaurants, and public transport. Modern interiors with all amenities. Ideal for business travelers or families exploring Zanzibar City.',
                'price_per_night' => 90,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'max_guests' => 4,
                'amenities' => ['WiFi', 'Air Conditioning', 'Kitchen', 'Parking', 'Generator Backup', 'Hot Water', 'City View'],
                'images' => ['https://images.unsplash.com/photo-1600573472550-8090b5e0745e?w=800', 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800'],
                'featured' => false,
                'coordinates' => [-6.1667, 39.2000]
            ],
            [
                'id' => 'apt_006',
                'name' => 'Paje Beachfront Penthouse',
                'slug' => 'paje-beachfront-penthouse',
                'location' => 'Paje',
                'description' => 'Exclusive penthouse apartment with panoramic Indian Ocean views in Paje. Features floor-to-ceiling windows, a spacious rooftop deck, and outdoor shower. The epitome of barefoot luxury on Zanzibar\'s southeast coast.',
                'price_per_night' => 320,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'max_guests' => 6,
                'amenities' => ['WiFi', 'Air Conditioning', 'Kitchen', 'Parking', 'Generator Backup', 'Rooftop Terrace', 'Outdoor Shower', 'Beach Access'],
                'images' => ['https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800', 'https://images.unsplash.com/photo-1600573472550-8090b5e0745e?w=800'],
                'featured' => true,
                'coordinates' => [-6.4333, 39.5500]
            ]
        ];
        save_data('apartments.json', $default_apartments);
    }
    if (empty(load_data('reviews.json'))) {
        $default_reviews = [
            ['id' => 'rev_001', 'guest_name' => 'Sarah Mitchell', 'guest_image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face', 'country' => 'United Kingdom', 'rating' => 5, 'text' => 'Absolutely stunning apartment! The location was perfect, right on Kendwa Beach. The staff went above and beyond to make our stay special. Will definitely be coming back.', 'date' => '2026-03-15', 'verified' => true],
            ['id' => 'rev_002', 'guest_name' => 'Marco Rossi', 'guest_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face', 'country' => 'Italy', 'rating' => 5, 'text' => 'The Nungwi Luxury Suite exceeded all expectations. Spotlessly clean, beautifully furnished, and the rooftop terrace was magical at sunset. Already planning our next visit.', 'date' => '2026-02-22', 'verified' => true],
            ['id' => 'rev_003', 'guest_name' => 'Emily Chen', 'guest_image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face', 'country' => 'Singapore', 'rating' => 5, 'text' => 'Tunu Apartments made our Zanzibar honeymoon unforgettable. The private beach access, the attention to detail, the warm hospitality... everything was perfect. Highly recommend!', 'date' => '2026-01-10', 'verified' => true],
            ['id' => 'rev_004', 'guest_name' => 'James Okonkwo', 'guest_image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face', 'country' => 'Nigeria', 'rating' => 4, 'text' => 'Great apartments with excellent amenities. The WiFi was reliable, the AC worked perfectly, and the location was convenient. Would recommend for business travelers too.', 'date' => '2025-12-05', 'verified' => true],
            ['id' => 'rev_005', 'guest_name' => 'Anna Schmidt', 'guest_image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&h=100&fit=crop&crop=face', 'country' => 'Germany', 'rating' => 5, 'text' => 'I travel extensively and Tunu Apartments ranks among the best accommodations I have ever experienced. The booking process was seamless and the apartment was immaculate.', 'date' => '2025-11-18', 'verified' => true],
            ['id' => 'rev_006', 'guest_name' => 'David Kim', 'guest_image' => 'https://images.unsplash.com/photo-1500048993953-d23a436266cf?w=100&h=100&fit=crop&crop=face', 'country' => 'South Korea', 'rating' => 5, 'text' => 'Incredible value for money. The apartment was larger than expected, beautifully decorated, and had everything we needed. The beachfront location was the cherry on top.', 'date' => '2025-10-30', 'verified' => true]
        ];
        save_data('reviews.json', $default_reviews);
    }
}
init_default_data();
