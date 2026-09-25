<?php
require_once __DIR__ . '/includes/auth.php';
require_role('user');
include __DIR__ . '/includes/header.php';
$user = $_SESSION['user'];
$reservations = array_values(array_filter(load_reservations(), fn($reservation) => ($reservation['user_email'] ?? '') === $user['email']));
$cartItems = $_SESSION['cart'] ?? [];
$cartCount = array_sum(array_map(fn($item) => (int) ($item['quantity'] ?? 1), $cartItems));
$latestReservation = $reservations ? $reservations[count($reservations) - 1] : null;
?>

<section class="page-section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">User dashboard</span>
            <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?></h1>
        </div>
        <a href="rentals.php" class="btn btn-primary">Book a motorcycle</a>
    </div>

    <div class="stats-grid three-up">
        <div class="stat-card">
            <span class="label">Active rental</span>
            <strong><?php echo $latestReservation ? htmlspecialchars($latestReservation['motorcycle']) : 'No active booking'; ?></strong>
        </div>
        <div class="stat-card">
            <span class="label">Verification status</span>
            <strong><?php echo ($user['verified'] ?? false) ? 'Verified' : 'Pending'; ?></strong>
        </div>
        <div class="stat-card">
            <span class="label">Items in cart</span>
            <strong><?php echo $cartCount; ?> parts</strong>
        </div>
    </div>

    <div class="content-grid two-col">
        <div class="panel">
            <h3>Upcoming reservation</h3>
            <?php if ($latestReservation): ?>
                <div class="reservation-box">
                    <div>
                        <span class="muted">Vehicle</span>
                        <strong><?php echo htmlspecialchars($latestReservation['motorcycle']); ?></strong>
                    </div>
                    <div>
                        <span class="muted">Dates</span>
                        <strong><?php echo htmlspecialchars($latestReservation['start_date']); ?> - <?php echo htmlspecialchars($latestReservation['end_date']); ?></strong>
                    </div>
                    <div>
                        <span class="muted">Status</span>
                        <strong class="status success"><?php echo htmlspecialchars($latestReservation['status']); ?></strong>
                    </div>
                </div>
            <?php else: ?>
                <p class="muted">No motorcycle reservation yet.</p>
            <?php endif; ?>
        </div>

        <div class="panel">
            <h3>Recent actions</h3>
            <ul class="list">
                <?php if ($latestReservation): ?>
                    <li>Booked <?php echo htmlspecialchars($latestReservation['motorcycle']); ?> for <?php echo htmlspecialchars($latestReservation['start_date']); ?>.</li>
                <?php else: ?>
                    <li>No motorcycle booking has been made yet.</li>
                <?php endif; ?>
                <li>Cart contains <?php echo $cartCount; ?> product item(s).</li>
                <li>Verification is <?php echo ($user['verified'] ?? false) ? 'approved' : 'pending'; ?>.</li>
            </ul>
        </div>
    </div>

    <div class="panel full-width">
        <h3>Recommended parts</h3>
        <div class="product-grid">
            <?php $featuredProducts = array_slice(load_products(), 0, 3); ?>
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <div class="product-image placeholder"><?php echo htmlspecialchars($product['icon'] ?? '🧰'); ?></div>
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p>₱<?php echo number_format((float) $product['price'], 2, '.', ','); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
