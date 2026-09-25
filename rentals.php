<?php
require_once __DIR__ . '/includes/auth.php';

$user = $_SESSION['user'] ?? null;
if ($user && ($user['role'] ?? '') === 'admin') {
    header('Location: admin-dashboard.php');
    exit;
}

$message = '';
$motorcycles = array_values(array_filter(load_motorcycles(), fn($motorcycle) => ($motorcycle['status'] ?? 'available') !== 'maintenance'));
$selectedMotorcycle = $_GET['bike'] ?? ($motorcycles[0]['name'] ?? 'Honda Click 160');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$user) {
        header('Location: login.php');
        exit;
    }

    $motorcycle = trim((string) ($_POST['motorcycle'] ?? ''));
    $startDate = trim((string) ($_POST['start_date'] ?? ''));
    $endDate = trim((string) ($_POST['end_date'] ?? ''));
    $renterName = trim((string) ($_POST['renter_name'] ?? ''));
    $renterPhone = trim((string) ($_POST['renter_phone'] ?? ''));
    $renterAddress = trim((string) ($_POST['renter_address'] ?? ''));

    if ($motorcycle !== '' && $startDate !== '' && $endDate !== '' && $renterName !== '' && $renterPhone !== '' && $renterAddress !== '') {
        $reservations = load_reservations();
        $reservations[] = [
            'id' => time(),
            'user_email' => $user['email'],
            'motorcycle' => $motorcycle,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'renter_name' => $renterName,
            'renter_phone' => $renterPhone,
            'renter_address' => $renterAddress,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        save_reservations($reservations);
        $message = 'Rental submitted successfully.';
    } else {
        $message = 'Please complete all booking and renter details.';
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="page-section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Motorcycle rental</span>
            <h1>Rent your ride online</h1>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="product-grid three-up">
        <?php foreach ($motorcycles as $motorcycle): ?>
            <article class="product-card large">
                <div class="product-image placeholder"><?php echo htmlspecialchars($motorcycle['icon'] ?? '🏍️'); ?></div>
                <h4><?php echo htmlspecialchars($motorcycle['name']); ?></h4>
                <p>₱<?php echo number_format((float) ($motorcycle['daily_rate'] ?? 0), 2, '.', ','); ?>/day</p>
                <?php if ($user): ?>
                    <a href="rentals.php?bike=<?php echo urlencode($motorcycle['name']); ?>#booking-form" class="btn btn-primary full">Rent now</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary full">Rent now</a>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
        <?php if (empty($motorcycles)): ?>
            <div class="panel full-width">
                <p>No motorcycles are currently available for rent.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="panel booking-form-wrap" id="booking-form">
        <h3>Rental request</h3>
        <form method="POST" class="booking-form">
            <div class="input-grid">
                <div class="input-group">
                    <label for="motorcycle">Choose motorcycle</label>
                    <select id="motorcycle" name="motorcycle">
                        <?php foreach ($motorcycles as $motorcycle): ?>
                            <option value="<?php echo htmlspecialchars($motorcycle['name']); ?>" <?php echo $selectedMotorcycle === $motorcycle['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($motorcycle['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="renter_name">Full name</label>
                    <input id="renter_name" name="renter_name" type="text" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label for="renter_phone">Phone number</label>
                    <input id="renter_phone" name="renter_phone" type="tel" placeholder="09XXXXXXXXX" required>
                </div>
                <div class="input-group">
                    <label for="renter_address">Address</label>
                    <input id="renter_address" name="renter_address" type="text" placeholder="Enter your address" required>
                </div>
                <div class="input-group">
                    <label for="start_date">Start date</label>
                    <input id="start_date" name="start_date" type="date" required>
                </div>
                <div class="input-group">
                    <label for="end_date">End date</label>
                    <input id="end_date" name="end_date" type="date" required>
                </div>
            </div>
            <?php if ($user): ?>
                <button type="submit" class="btn btn-primary">Rent motorcycle</button>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Rent motorcycle</a>
            <?php endif; ?>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
