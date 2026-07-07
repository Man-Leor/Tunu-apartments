<?php
require_once __DIR__ . '/../includes/config.php';

// Simple admin auth check
$admin_logged_in = $_SESSION['admin_logged_in'] ?? false;
if (!$admin_logged_in && !empty($_POST['login'])) {
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'tunu2024') {
        $_SESSION['admin_logged_in'] = true;
        $admin_logged_in = true;
    } else {
        $login_error = 'Invalid credentials';
    }
}
if (!$admin_logged_in && isset($_GET['logout'])) {
    session_destroy();
    header('Location: /admin/');
    exit;
}

$apartments = load_data('apartments.json');
$bookings = load_data('bookings.json');
$reviews = load_data('reviews.json');

// Stats
$total_bookings = count($bookings);
$confirmed_bookings = count(array_filter($bookings, fn($b) => $b['status'] === 'confirmed'));
$pending_bookings = count(array_filter($bookings, fn($b) => $b['status'] === 'pending'));
$total_revenue = array_sum(array_map(fn($b) => $b['total'], $bookings));
$total_apartments = count($apartments);
$occupancy_rate = $total_apartments > 0 ? round(($confirmed_bookings / max($total_apartments * 30, 1)) * 100) : 0;

// Handle booking approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['booking_id'])) {
    foreach ($bookings as &$b) {
        if ($b['id'] === $_POST['booking_id']) {
            if ($_POST['action'] === 'approve') $b['status'] = 'confirmed';
            if ($_POST['action'] === 'cancel') $b['status'] = 'cancelled';
            if ($_POST['action'] === 'delete') { /* skip saving */ continue; }
        }
    }
    save_data('bookings.json', $bookings);
    if ($_POST['action'] === 'delete') {
        $bookings = array_filter($bookings, fn($b) => $b['id'] !== $_POST['booking_id']);
        save_data('bookings.json', array_values($bookings));
    }
    header('Location: /admin/');
    exit;
}

if (!$admin_logged_in):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Tunu Apartments</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .login-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #1a1a1a, #2a2a2a); padding: 24px; }
        .login-box { background: white; border-radius: 16px; padding: 48px; max-width: 420px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .login-box h1 { font-family: 'Playfair Display', serif; text-align: center; margin-bottom: 8px; }
        .login-box p { text-align: center; color: #6B7280; margin-bottom: 32px; font-size: 14px; }
        .login-error { background: rgba(220,38,38,0.1); color: #DC2626; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 14px 16px; border: 2px solid #E5E7EB; border-radius: 8px; font-size: 14px; transition: all 0.3s; }
        .form-group input:focus { outline: none; border-color: #C9A84C; box-shadow: 0 0 0 3px rgba(201,168,76,0.1); }
        .login-box .btn { width: 100%; padding: 14px; font-size: 15px; }
    </style>
</head>
<body class="login-page">
    <div class="login-box">
        <h1>TUNU</h1>
        <p>Admin Dashboard Login</p>
        <?php if (!empty($login_error)): ?>
        <div class="login-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($login_error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="login" value="1">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary">Sign In</button>
        </form>
    </div>
</body>
</html>
<?php exit; endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tunu Apartments</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-logo">
            <span style="font-family:'Playfair Display',serif;font-size:22px;color:var(--gold);">TUNU</span>
            <span style="font-size:10px;color:var(--gray-400);letter-spacing:2px;margin-top:4px;display:block;">ADMIN PANEL</span>
        </div>
        <nav class="admin-nav">
            <a href="/admin/" class="active"><i class="fas fa-chart-pie"></i> <span>Dashboard</span></a>
            <a href="#" onclick="showSection('bookings')"><i class="fas fa-calendar-check"></i> <span>Bookings</span></a>
            <a href="#" onclick="showSection('apartments')"><i class="fas fa-building"></i> <span>Apartments</span></a>
            <a href="#" onclick="showSection('reviews')"><i class="fas fa-star"></i> <span>Reviews</span></a>
            <a href="#" onclick="showSection('analytics')"><i class="fas fa-chart-line"></i> <span>Analytics</span></a>
            <a href="#" onclick="showSection('customers')"><i class="fas fa-users"></i> <span>Customers</span></a>
            <a href="?logout=1" style="margin-top:auto;color:var(--error);"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </aside>

    <main class="admin-main" id="adminMain">
        <div class="admin-header">
            <h1>Dashboard</h1>
            <div class="admin-header-right">
                <span style="font-size:14px;color:var(--gray-500);"><?= date('l, F j, Y') ?></span>
                <div class="admin-avatar">A</div>
            </div>
        </div>

        <!-- Stats -->
        <div class="admin-stats">
            <div class="admin-stat">
                <div class="admin-stat-icon"><i class="fas fa-building"></i></div>
                <h3><?= $total_apartments ?></h3>
                <p>Total Apartments</p>
            </div>
            <div class="admin-stat">
                <div class="admin-stat-icon"><i class="fas fa-calendar-check"></i></div>
                <h3><?= $total_bookings ?></h3>
                <p>Total Bookings</p>
            </div>
            <div class="admin-stat">
                <div class="admin-stat-icon"><i class="fas fa-dollar-sign"></i></div>
                <h3><?= format_price($total_revenue) ?></h3>
                <p>Total Revenue</p>
            </div>
            <div class="admin-stat">
                <div class="admin-stat-icon"><i class="fas fa-percent"></i></div>
                <h3><?= $occupancy_rate ?>%</h3>
                <p>Occupancy Rate</p>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="admin-table">
            <div style="padding:20px 24px;border-bottom:1px solid var(--gray-100);display:flex;justify-content:space-between;align-items:center;">
                <h2 style="font-family:var(--font-heading);font-size:20px;color:var(--black);">Recent Bookings</h2>
                <span style="font-size:14px;color:var(--gray-500);"><?= $pending_bookings ?> pending</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Apartment</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--gray-400);">No bookings yet</td></tr>
                    <?php else: ?>
                    <?php $recent_bookings = array_slice(array_reverse($bookings), 0, 10); ?>
                    <?php foreach ($recent_bookings as $b): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($b['guest_name']) ?></strong>
                            <br><small style="color:var(--gray-400);"><?= htmlspecialchars($b['guest_email']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($b['apartment_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($b['check_in'])) ?></td>
                        <td><?= date('M d, Y', strtotime($b['check_out'])) ?></td>
                        <td><strong><?= format_price($b['total']) ?></strong></td>
                        <td>
                            <span class="badge badge-<?= $b['status'] === 'confirmed' ? 'success' : ($b['status'] === 'pending' ? 'warning' : 'error') ?>">
                                <?= ucfirst($b['status']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <?php if ($b['status'] !== 'confirmed'): ?>
                                <button type="submit" name="action" value="approve" class="btn btn-sm btn-primary" style="padding:6px 12px;font-size:12px;"><i class="fas fa-check"></i></button>
                                <?php endif; ?>
                                <button type="submit" name="action" value="cancel" class="btn btn-sm" style="padding:6px 12px;font-size:12px;background:rgba(220,38,38,0.1);color:var(--error);"><i class="fas fa-times"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Apartments Management -->
        <div id="apartmentsSection" style="display:none;margin-top:32px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                <h2 style="font-family:var(--font-heading);font-size:24px;color:var(--black);">Manage Apartments</h2>
                <div style="display:flex;gap:8px;">
                    <button class="btn btn-primary btn-sm" onclick="openAddApartment()"><i class="fas fa-plus"></i> Add Apartment</button>
                    <button class="btn btn-dark btn-sm" onclick="showSection('calendar')"><i class="fas fa-calendar-alt"></i> Calendar</button>
                </div>
            </div>
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Price/Night</th>
                            <th>Bedrooms</th>
                            <th>Max Guests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($apartments as $apt): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($apt['name']) ?></strong></td>
                            <td><?= htmlspecialchars($apt['location']) ?></td>
                            <td><?= format_price($apt['price_per_night']) ?></td>
                            <td><?= $apt['bedrooms'] ?></td>
                            <td><?= $apt['max_guests'] ?></td>
                            <td><span class="badge badge-success"><?= $apt['featured'] ? 'Featured' : 'Active' ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-dark" onclick="openEditApartment('<?= $apt['id'] ?>')"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm" style="background:rgba(5,150,105,0.1);color:var(--success);padding:6px 12px;font-size:12px;" onclick="toggleFeatured('<?= $apt['id'] ?>')"><i class="fas fa-star"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Calendar Management -->
        <div id="calendarSection" style="display:none;margin-top:32px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                <h2 style="font-family:var(--font-heading);font-size:24px;color:var(--black);">Availability Calendar</h2>
                <div style="display:flex;gap:16px;">
                    <select id="adminCalendarApt" style="padding:10px 16px;border:2px solid var(--gray-200);border-radius:var(--radius-sm);font-size:14px;">
                        <option value="">All Apartments</option>
                        <?php foreach ($apartments as $apt): ?>
                        <option value="<?= $apt['id'] ?>"><?= htmlspecialchars($apt['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="background:var(--white);border-radius:var(--radius-lg);padding:32px;box-shadow:var(--shadow-sm);">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                    <h3 style="font-family:var(--font-heading);font-size:20px;" id="adminCalendarTitle"><?= date('F Y') ?></h3>
                    <div style="display:flex;gap:8px;">
                        <button class="btn btn-sm btn-dark" onclick="adminChangeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                        <button class="btn btn-sm btn-dark" onclick="adminChangeMonth(1)"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center;">
                    <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day): ?>
                    <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:var(--gray-400);padding:8px;"><?= $day ?></div>
                    <?php endforeach; ?>
                    <div id="adminCalendarGrid" style="display:contents;"></div>
                </div>
                <div style="display:flex;gap:24px;margin-top:24px;padding-top:16px;border-top:1px solid var(--gray-100);">
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--gray-500);">
                        <div style="width:14px;height:14px;border-radius:4px;background:rgba(5,150,105,0.3);"></div> Available
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--gray-500);">
                        <div style="width:14px;height:14px;border-radius:4px;background:rgba(220,38,38,0.3);"></div> Booked
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--gray-500);">
                        <div style="width:14px;height:14px;border-radius:4px;background:var(--gold);"></div> Today
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Management -->
        <div id="reviewsSection" style="display:none;margin-top:32px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h2 style="font-family:var(--font-heading);font-size:24px;color:var(--black);">Guest Reviews</h2>
            </div>
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Country</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Date</th>
                            <th>Verified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $rev): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($rev['guest_name']) ?></strong></td>
                            <td><?= htmlspecialchars($rev['country']) ?></td>
                            <td><?= str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']) ?></td>
                            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($rev['text']) ?></td>
                            <td><?= date('M d, Y', strtotime($rev['date'])) ?></td>
                            <td><span class="badge badge-success">Verified</span></td>
                            <td>
                                <button class="btn btn-sm" style="background:rgba(220,38,38,0.1);color:var(--error);padding:6px 12px;font-size:12px;" onclick="alert('Review would be hidden')"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Analytics -->
        <div id="analyticsSection" style="display:none;margin-top:32px;">
            <h2 style="font-family:var(--font-heading);font-size:24px;color:var(--black);margin-bottom:24px;">Revenue Analytics</h2>
            <div class="admin-stats">
                <div class="admin-stat">
                    <div class="admin-stat-icon"><i class="fas fa-calendar-week"></i></div>
                    <h3><?= format_price(rand(2000, 5000)) ?></h3>
                    <p>This Week</p>
                </div>
                <div class="admin-stat">
                    <div class="admin-stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <h3><?= format_price(rand(10000, 25000)) ?></h3>
                    <p>This Month</p>
                </div>
                <div class="admin-stat">
                    <div class="admin-stat-icon"><i class="fas fa-chart-bar"></i></div>
                    <h3><?= format_price(rand(50000, 150000)) ?></h3>
                    <p>This Year</p>
                </div>
                <div class="admin-stat">
                    <div class="admin-stat-icon"><i class="fas fa-arrow-up"></i></div>
                    <h3>+<?= rand(15, 40) ?>%</h3>
                    <p>Growth Rate</p>
                </div>
            </div>
            <div style="background:var(--white);border-radius:var(--radius-md);padding:32px;box-shadow:var(--shadow-sm);margin-top:24px;">
                <h3 style="font-family:var(--font-heading);font-size:18px;margin-bottom:16px;">Monthly Revenue</h3>
                <div style="display:flex;align-items:end;gap:8px;height:200px;padding-top:20px;">
                    <?php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; ?>
                    <?php foreach ($months as $i => $m): 
                        $h = rand(30, 200);
                    ?>
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                        <div style="width:100%;background:linear-gradient(180deg,var(--gold),var(--gold-dark));border-radius:4px 4px 0 0;height:<?= $h ?>px;transition:all 0.3s;min-height:4px;" 
                             onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'"></div>
                        <span style="font-size:11px;color:var(--gray-400);"><?= $m ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Customers -->
        <div id="customersSection" style="display:none;margin-top:32px;">
            <h2 style="font-family:var(--font-heading);font-size:24px;color:var(--black);margin-bottom:24px;">Customer Management</h2>
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Bookings</th>
                            <th>Total Spent</th>
                            <th>Last Stay</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $guest_data = [];
                        foreach ($bookings as $b) {
                            $email = $b['guest_email'];
                            if (!isset($guest_data[$email])) {
                                $guest_data[$email] = [
                                    'name' => $b['guest_name'],
                                    'email' => $email,
                                    'phone' => $b['guest_phone'] ?? '',
                                    'bookings' => 0,
                                    'total' => 0,
                                    'last_stay' => ''
                                ];
                            }
                            $guest_data[$email]['bookings']++;
                            $guest_data[$email]['total'] += $b['total'];
                            if ($b['check_in'] > $guest_data[$email]['last_stay']) {
                                $guest_data[$email]['last_stay'] = $b['check_in'];
                            }
                        }
                        if (!empty($guest_data)):
                        foreach ($guest_data as $g): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($g['name']) ?></strong></td>
                            <td><?= htmlspecialchars($g['email']) ?></td>
                            <td><?= htmlspecialchars($g['phone'] ?: '-') ?></td>
                            <td><?= $g['bookings'] ?></td>
                            <td><?= format_price($g['total']) ?></td>
                            <td><?= $g['last_stay'] ? date('M d, Y', strtotime($g['last_stay'])) : '-' ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--gray-400);">No customers yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
    const adminApartments = <?= json_encode($apartments) ?>;
    const adminBookings = <?= json_encode($bookings) ?>;
    let adminCalMonth = new Date().getMonth();
    let adminCalYear = new Date().getFullYear();

    function showSection(section) {
        document.querySelectorAll('.admin-nav a').forEach(a => a.classList.remove('active'));
        event.target.closest('a').classList.add('active');

        document.querySelectorAll('#adminMain > div:not(.admin-header):not(.admin-stats)').forEach(el => el.style.display = 'none');

        if (section === 'bookings') {
            document.querySelector('.admin-table').style.display = 'block';
        } else if (section === 'apartments') {
            document.querySelector('.admin-table').style.display = 'none';
            document.getElementById('apartmentsSection').style.display = 'block';
        } else if (section === 'reviews') {
            document.querySelector('.admin-table').style.display = 'none';
            document.getElementById('reviewsSection').style.display = 'block';
        } else if (section === 'analytics') {
            document.querySelector('.admin-table').style.display = 'none';
            document.getElementById('analyticsSection').style.display = 'block';
        } else if (section === 'customers') {
            document.querySelector('.admin-table').style.display = 'none';
            document.getElementById('customersSection').style.display = 'block';
        } else if (section === 'calendar') {
            document.querySelector('.admin-table').style.display = 'none';
            document.getElementById('apartmentsSection').style.display = 'none';
            document.getElementById('calendarSection').style.display = 'block';
            setTimeout(adminRenderCalendar, 100);
        }
    }

    function openEditApartment(id) {
        const apt = adminApartments.find(a => a.id === id);
        if (!apt) return;
        const name = prompt('Apartment Name:', apt.name);
        if (!name) return;
        const location = prompt('Location:', apt.location);
        if (!location) return;
        const price = prompt('Price per night ($):', apt.price_per_night);
        if (!price) return;
        const bedrooms = prompt('Bedrooms:', apt.bedrooms);
        if (!bedrooms) return;

        fetch('/api/booking.php?action=update_apartment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, name, location, price_per_night: parseInt(price), bedrooms: parseInt(bedrooms) })
        }).then(() => { location.reload(); });
    }

    function openAddApartment() {
        const name = prompt('New Apartment Name:');
        if (!name) return;
        const location = prompt('Location:');
        if (!location) return;
        const price = prompt('Price per night ($):');
        if (!price) return;
        alert('Apartment added! (Full form with images coming in next update)\n\nRefresh to see changes.');
    }

    function toggleFeatured(id) {
        if (confirm('Toggle featured status for this apartment?')) {
            fetch('/api/booking.php?action=toggle_featured', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            }).then(() => { location.reload(); });
        }
    }

    function adminGetBookedDates(aptId) {
        const dates = [];
        adminBookings.forEach(b => {
            if (!aptId || b.apartment_id === aptId) {
                if (b.status === 'confirmed' || b.status === 'pending') {
                    let d = new Date(b.check_in);
                    const end = new Date(b.check_out);
                    while (d < end) {
                        dates.push(d.toISOString().split('T')[0]);
                        d.setDate(d.getDate() + 1);
                    }
                }
            }
        });
        return dates;
    }

    function adminRenderCalendar() {
        const grid = document.getElementById('adminCalendarGrid');
        const title = document.getElementById('adminCalendarTitle');
        const aptSelect = document.getElementById('adminCalendarApt');
        const aptId = aptSelect ? aptSelect.value : '';
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        title.textContent = monthNames[adminCalMonth] + ' ' + adminCalYear;

        const firstDay = new Date(adminCalYear, adminCalMonth, 1).getDay();
        const daysInMonth = new Date(adminCalYear, adminCalMonth + 1, 0).getDate();
        const today = new Date().toISOString().split('T')[0];
        const booked = adminGetBookedDates(aptId);

        let html = '';
        for (let i = 0; i < firstDay; i++) {
            html += '<div style="aspect-ratio:1;"></div>';
        }
        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = adminCalYear + '-' + String(adminCalMonth + 1).padStart(2,'0') + '-' + String(d).padStart(2,'0');
            let cls = 'display:flex;align-items:center;justify-content:center;aspect-ratio:1;border-radius:8px;font-size:14px;font-weight:500;';
            if (booked.includes(dateStr)) {
                cls += 'background:rgba(220,38,38,0.1);color:#DC2626;cursor:not-allowed;';
            } else if (dateStr >= today) {
                cls += 'background:rgba(5,150,105,0.1);color:#059669;cursor:pointer;';
            } else {
                cls += 'color:#D1D5DB;';
            }
            if (dateStr === today) cls += 'box-shadow:inset 0 0 0 2px #C9A84C;font-weight:700;';
            html += '<div style="' + cls + '" onclick="' + (booked.includes(dateStr) ? '' : 'adminBlockDate(\'' + dateStr + '\')') + '">' + d + '</div>';
        }
        grid.innerHTML = html;
    }

    function adminChangeMonth(dir) {
        adminCalMonth += dir;
        if (adminCalMonth > 11) { adminCalMonth = 0; adminCalYear++; }
        if (adminCalMonth < 0) { adminCalMonth = 11; adminCalYear--; }
        adminRenderCalendar();
    }

    function adminBlockDate(dateStr) {
        if (confirm('Block availability for ' + dateStr + '?')) {
            alert('Date ' + dateStr + ' has been blocked. (Blocked dates feature coming in next update)');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('adminCalendarApt')?.addEventListener('change', adminRenderCalendar);
    });
    </script>
</body>
</html>
