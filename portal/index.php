<?php
require_once __DIR__ . '/../includes/config.php';

// Customer session handling
$customer_logged_in = $_SESSION['customer_logged_in'] ?? false;
$customer_email = $_SESSION['customer_email'] ?? '';

// Handle login/register
if (!empty($_POST['login_submit'])) {
    // Simple login - check against bookings data
    $bookings = load_data('bookings.json');
    $email = $_POST['email'];
    $found = false;
    foreach ($bookings as $b) {
        if ($b['guest_email'] === $email) {
            $found = true;
            break;
        }
    }
    if ($found || $email === 'demo@guest.com') {
        $_SESSION['customer_logged_in'] = true;
        $_SESSION['customer_email'] = $email;
        $_SESSION['customer_name'] = $_POST['name'] ?? 'Guest';
        $customer_logged_in = true;
        $customer_email = $email;
    } else {
        $login_error = 'No account found with that email. Please use the email you booked with.';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /portal/');
    exit;
}

// Get customer bookings
$customer_bookings = [];
if ($customer_logged_in) {
    $all_bookings = load_data('bookings.json');
    foreach ($all_bookings as $b) {
        if ($b['guest_email'] === $customer_email) {
            $customer_bookings[] = $b;
        }
    }
}

if (!$customer_logged_in):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Portal - Tunu Apartments</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .portal-login { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #faf7f2, #f3f4f6); padding: 24px; }
        .portal-login-box { background: white; border-radius: 16px; padding: 48px; max-width: 460px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.1); }
        .portal-login-box h1 { font-family: 'Playfair Display', serif; text-align: center; font-size: 28px; margin-bottom: 4px; }
        .portal-login-box .subtitle { text-align: center; color: #6B7280; margin-bottom: 32px; font-size: 14px; }
        .portal-login-box .logo-text { text-align: center; margin-bottom: 24px; }
        .portal-login-box .logo-text span { font-family: 'Playfair Display', serif; font-size: 24px; color: #C9A84C; }
        .error-msg { background: rgba(220,38,38,0.1); color: #DC2626; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        .divider { text-align: center; margin: 24px 0; position: relative; }
        .divider::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #E5E7EB; }
        .divider span { background: white; padding: 0 16px; color: #9CA3AF; font-size: 13px; position: relative; }
    </style>
</head>
<body class="portal-login">
    <div class="portal-login-box">
        <div class="logo-text"><span>TUNU</span> <span style="font-size:12px;letter-spacing:2px;color:var(--gray-400);font-family:Inter;">GUEST PORTAL</span></div>
        <h1>Welcome Back</h1>
        <p class="subtitle">Sign in to view your bookings and manage your reservations.</p>
        <?php if (!empty($login_error)): ?>
        <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($login_error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="login_submit" value="1">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Sign In</button>
        </form>
        <div class="divider"><span>New here?</span></div>
        <a href="/" class="btn btn-dark btn-block"><i class="fas fa-calendar-check"></i> Book Your Stay</a>
        <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--gray-400);">
            <a href="/" style="color:var(--gold);">&larr; Back to Home</a>
        </p>
    </div>
</body>
</html>
<?php exit; endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portal - Tunu Apartments</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="portal-body">
    <header class="portal-header">
        <div class="container">
            <div class="portal-header-inner">
                <a href="/portal/" style="font-family:var(--font-heading);font-size:20px;color:var(--gold);">TUNU <span style="font-size:10px;letter-spacing:2px;color:var(--gray-400);font-family:Inter;">PORTAL</span></a>
                <div class="portal-user">
                    <span style="font-size:14px;color:var(--gray-600);"><?= htmlspecialchars($_SESSION['customer_name'] ?? 'Guest') ?></span>
                    <div class="portal-user-avatar"><?= strtoupper(($_SESSION['customer_name'] ?? 'G')[0]) ?></div>
                    <a href="?logout=1" style="font-size:13px;color:var(--gray-400);"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>
        </div>
    </header>

    <div class="portal-content">
        <!-- Profile Card -->
        <div class="portal-card">
            <div class="portal-card-body" style="display:flex;align-items:center;gap:24px;">
                <div style="width:64px;height:64px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;color:white;font-size:28px;font-weight:600;flex-shrink:0;">
                    <?= strtoupper(($_SESSION['customer_name'] ?? 'G')[0]) ?>
                </div>
                <div>
                    <h3 style="font-family:var(--font-heading);font-size:22px;color:var(--black);"><?= htmlspecialchars($_SESSION['customer_name'] ?? 'Guest') ?></h3>
                    <p style="color:var(--gray-500);font-size:14px;"><?= htmlspecialchars($customer_email) ?></p>
                </div>
                <div style="margin-left:auto;display:flex;gap:16px;text-align:center;">
                    <div>
                        <strong style="font-size:24px;color:var(--gold);"><?= count($customer_bookings) ?></strong>
                        <p style="font-size:13px;color:var(--gray-500);">Bookings</p>
                    </div>
                    <div style="width:1px;background:var(--gray-200);"></div>
                    <div>
                        <strong style="font-size:24px;color:var(--gold);"><?= format_price(array_sum(array_map(fn($b) => $b['total'], $customer_bookings))) ?></strong>
                        <p style="font-size:13px;color:var(--gray-500);">Total Spent</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Bookings -->
        <div class="portal-card">
            <div class="portal-card-header">
                <h2>My Bookings</h2>
                <a href="/" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Book New</a>
            </div>
            <div class="portal-card-body">
                <?php if (empty($customer_bookings)): ?>
                <div style="text-align:center;padding:40px 20px;">
                    <i class="fas fa-calendar-times" style="font-size:48px;color:var(--gray-300);margin-bottom:16px;display:block;"></i>
                    <h3 style="font-family:var(--font-heading);font-size:20px;color:var(--black);margin-bottom:8px;">No Bookings Yet</h3>
                    <p style="color:var(--gray-500);margin-bottom:24px;">You haven't made any reservations yet. Start planning your Zanzibar getaway!</p>
                    <a href="/" class="btn btn-primary"><i class="fas fa-calendar-check"></i> Browse Apartments</a>
                </div>
                <?php else: ?>
                <?php foreach (array_reverse($customer_bookings) as $b): ?>
                <div class="portal-booking">
                    <img src="<?php
                        $apt = current(array_filter($apartments, fn($a) => $a['id'] === $b['apartment_id']));
                        echo $apt ? $apt['images'][0] : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=200';
                    ?>" alt="<?= htmlspecialchars($b['apartment_name']) ?>">
                    <div class="portal-booking-info">
                        <h4><?= htmlspecialchars($b['apartment_name']) ?></h4>
                        <p><i class="fas fa-calendar" style="color:var(--gold);"></i> <?= date('M d, Y', strtotime($b['check_in'])) ?> - <?= date('M d, Y', strtotime($b['check_out'])) ?></p>
                        <p><i class="fas fa-moon" style="color:var(--gold);"></i> <?= $b['nights'] ?> nights &middot; <strong style="color:var(--gold);"><?= format_price($b['total']) ?></strong></p>
                        <p><span class="badge badge-<?= $b['status'] === 'confirmed' ? 'success' : ($b['status'] === 'pending' ? 'warning' : 'error') ?>"><?= ucfirst($b['status']) ?></span>
                        <span class="badge badge-success" style="margin-left:8px;"><?= ucfirst($b['payment_status']) ?></span></p>
                    </div>
                    <div class="portal-booking-actions">
                        <button class="btn btn-sm btn-outline" style="border-color:var(--gray-300);color:var(--gray-600);" onclick="alert('Invoice downloaded')"><i class="fas fa-download"></i> Invoice</button>
                        <?php if ($b['status'] === 'confirmed'): ?>
                        <button class="btn btn-sm" style="background:rgba(220,38,38,0.1);color:var(--error);" onclick="if(confirm('Cancel this booking?'))alert('Cancellation requested')"><i class="fas fa-times"></i> Cancel</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Saved/Favorite Apartments -->
        <div class="portal-card">
            <div class="portal-card-header">
                <h2>Saved Apartments</h2>
            </div>
            <div class="portal-card-body">
                <?php
                $featured = array_filter($apartments, fn($a) => $a['featured']);
                ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
                    <?php foreach (array_slice($featured, 0, 3) as $apt): ?>
                    <div style="border:1px solid var(--gray-200);border-radius:var(--radius-md);overflow:hidden;">
                        <img src="<?= $apt['images'][0] ?>&w=300" alt="<?= htmlspecialchars($apt['name']) ?>" style="width:100%;height:140px;object-fit:cover;">
                        <div style="padding:12px;">
                            <h5 style="font-size:14px;font-weight:600;color:var(--black);"><?= htmlspecialchars($apt['name']) ?></h5>
                            <p style="font-size:13px;color:var(--gray-500);"><?= format_price($apt['price_per_night']) ?>/night</p>
                            <a href="/" class="btn btn-primary btn-sm btn-block" style="margin-top:8px;font-size:12px;">View Details</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
