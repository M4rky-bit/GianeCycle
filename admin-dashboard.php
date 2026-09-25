<?php
require_once __DIR__ . '/includes/auth.php';
require_role('admin');

$message = '';
$motorcycles = load_motorcycles();
$editingMotorcycle = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_motorcycle') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $brand = trim((string) ($_POST['brand'] ?? ''));
        $dailyRate = (float) ($_POST['daily_rate'] ?? 0);
        $status = trim((string) ($_POST['status'] ?? 'available'));
        $icon = trim((string) ($_POST['icon'] ?? '🏍️'));

        if ($name !== '' && $brand !== '' && $dailyRate > 0) {
            $motorcycles[] = [
                'id' => time() + count($motorcycles),
                'name' => $name,
                'brand' => $brand,
                'daily_rate' => $dailyRate,
                'status' => in_array($status, ['available', 'maintenance'], true) ? $status : 'available',
                'icon' => $icon !== '' ? $icon : '🏍️'
            ];
            save_motorcycles($motorcycles);
            $message = 'Motorcycle added successfully.';
        } else {
            $message = 'Please provide a motorcycle name, brand, and daily rate.';
        }
    }

    if ($action === 'edit_motorcycle') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $brand = trim((string) ($_POST['brand'] ?? ''));
        $dailyRate = (float) ($_POST['daily_rate'] ?? 0);
        $status = trim((string) ($_POST['status'] ?? 'available'));
        $icon = trim((string) ($_POST['icon'] ?? '🏍️'));

        foreach ($motorcycles as $index => $motorcycle) {
            if ((int) $motorcycle['id'] === $id) {
                $motorcycles[$index]['name'] = $name;
                $motorcycles[$index]['brand'] = $brand;
                $motorcycles[$index]['daily_rate'] = $dailyRate;
                $motorcycles[$index]['status'] = in_array($status, ['available', 'maintenance'], true) ? $status : 'available';
                $motorcycles[$index]['icon'] = $icon !== '' ? $icon : '🏍️';
                break;
            }
        }

        save_motorcycles($motorcycles);
        $message = 'Motorcycle updated successfully.';
    }

    if ($action === 'delete_motorcycle') {
        $id = (int) ($_POST['id'] ?? 0);
        $motorcycles = array_values(array_filter($motorcycles, fn($motorcycle) => (int) $motorcycle['id'] !== $id));
        save_motorcycles($motorcycles);
        $message = 'Motorcycle removed from rental list.';
    }
}

if (isset($_GET['edit_motorcycle'])) {
    $editId = (int) $_GET['edit_motorcycle'];
    foreach ($motorcycles as $motorcycle) {
        if ((int) $motorcycle['id'] === $editId) {
            $editingMotorcycle = $motorcycle;
            break;
        }
    }
}

$users = load_users();
$verifiedUsers = array_filter($users, fn($u) => ($u['verified'] ?? false) === true);
$userCount = count($users);
$pending = count(array_filter($users, fn($u) => ($u['verified'] ?? false) === false && ($u['role'] ?? '') === 'user'));
$reservations = load_reservations();
$orders = load_orders();
include __DIR__ . '/includes/header.php';
?>

<section class="page-section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Admin dashboard</span>
            <h1>Shop control center</h1>
        </div>
        <a href="shop.php" class="btn btn-primary">Manage inventory</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="stats-grid four-up">
        <div class="stat-card">
            <span class="label">Total accounts</span>
            <strong><?php echo $userCount; ?></strong>
        </div>
        <div class="stat-card">
            <span class="label">Verified users</span>
            <strong><?php echo count($verifiedUsers); ?></strong>
        </div>
        <div class="stat-card">
            <span class="label">Pending verification</span>
            <strong><?php echo $pending; ?></strong>
        </div>
        <div class="stat-card">
            <span class="label">Active bookings</span>
            <strong><?php echo count($reservations); ?></strong>
        </div>
    </div>

    <div class="panel full-width">
        <h3><?php echo $editingMotorcycle ? 'Edit motorcycle' : 'Add motorcycle for rent'; ?></h3>
        <form method="POST" class="inventory-form">
            <input type="hidden" name="action" value="<?php echo $editingMotorcycle ? 'edit_motorcycle' : 'add_motorcycle'; ?>">
            <?php if ($editingMotorcycle): ?>
                <input type="hidden" name="id" value="<?php echo (int) $editingMotorcycle['id']; ?>">
            <?php endif; ?>

            <div class="input-grid">
                <div class="input-group">
                    <label for="name">Motorcycle name</label>
                    <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($editingMotorcycle['name'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label for="brand">Brand</label>
                    <input id="brand" name="brand" type="text" value="<?php echo htmlspecialchars($editingMotorcycle['brand'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label for="daily_rate">Daily rate</label>
                    <input id="daily_rate" name="daily_rate" type="number" min="1" step="0.01" value="<?php echo htmlspecialchars((string) ($editingMotorcycle['daily_rate'] ?? 0)); ?>" required>
                </div>
                <div class="input-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="available" <?php echo (($editingMotorcycle['status'] ?? 'available') === 'available') ? 'selected' : ''; ?>>Available</option>
                        <option value="maintenance" <?php echo (($editingMotorcycle['status'] ?? 'available') === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="icon">Icon</label>
                    <input id="icon" name="icon" type="text" value="<?php echo htmlspecialchars($editingMotorcycle['icon'] ?? '🏍️'); ?>" placeholder="🏍️">
                </div>
            </div>

            <div class="inventory-actions">
                <button type="submit" class="btn btn-primary"><?php echo $editingMotorcycle ? 'Save changes' : 'Add motorcycle'; ?></button>
                <?php if ($editingMotorcycle): ?>
                    <a class="btn btn-secondary" href="admin-dashboard.php">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="panel full-width">
        <h3>Rental fleet</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Bike</th>
                    <th>Brand</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($motorcycles as $motorcycle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(($motorcycle['icon'] ?? '🏍️') . ' ' . $motorcycle['name']); ?></td>
                        <td><?php echo htmlspecialchars($motorcycle['brand'] ?? 'Unknown'); ?></td>
                        <td>₱<?php echo number_format((float) ($motorcycle['daily_rate'] ?? 0), 2, '.', ','); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst((string) ($motorcycle['status'] ?? 'available'))); ?></td>
                        <td class="inventory-actions-cell">
                            <a class="btn btn-secondary small" href="admin-dashboard.php?edit_motorcycle=<?php echo (int) $motorcycle['id']; ?>">Edit</a>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="action" value="delete_motorcycle">
                                <input type="hidden" name="id" value="<?php echo (int) $motorcycle['id']; ?>">
                                <button class="btn btn-danger small" type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($motorcycles)): ?>
                    <tr>
                        <td colspan="5">No motorcycles available yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="panel full-width">
        <h3>Recent purchases</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['user_email'] ?? 'Unknown'); ?></td>
                        <td>₱<?php echo number_format((float) ($order['total'] ?? 0), 2, '.', ','); ?></td>
                        <td><?php echo count($order['items'] ?? []); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="3">No purchases yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
