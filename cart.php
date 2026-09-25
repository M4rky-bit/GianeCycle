<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$user = $_SESSION['user'];
$cart = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'remove_item') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $_SESSION['cart'] = array_values(array_filter($cart, fn($item) => (int) $item['id'] !== $productId));
        header('Location: cart.php');
        exit;
    }

    if ($action === 'checkout') {
        if (empty($cart)) {
            header('Location: cart.php');
            exit;
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1);
        }

        $orders = load_orders();
        $orders[] = [
            'id' => time(),
            'user_email' => $user['email'],
            'items' => $cart,
            'total' => $total,
            'created_at' => date('Y-m-d H:i:s')
        ];
        save_orders($orders);
        $_SESSION['cart'] = [];

        header('Location: user-dashboard.php?purchase=success');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
$total = 0;
foreach ($cart as $item) {
    $total += (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1);
}
?>

<section class="page-section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Cart</span>
            <h1>Your order summary</h1>
        </div>
        <a href="shop.php" class="btn btn-secondary">Continue shopping</a>
    </div>

    <?php if (empty($cart)): ?>
        <div class="panel">
            <p class="muted">Your cart is empty. Add motorcycle parts to continue.</p>
        </div>
    <?php else: ?>
        <div class="panel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(($item['icon'] ?? '🧰') . ' ' . ($item['name'] ?? 'Item')); ?></td>
                            <td><?php echo (int) ($item['quantity'] ?? 1); ?></td>
                            <td>₱<?php echo number_format((float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1), 2, '.', ','); ?></td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="action" value="remove_item">
                                    <input type="hidden" name="product_id" value="<?php echo (int) ($item['id'] ?? 0); ?>">
                                    <button type="submit" class="btn btn-danger small">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <h3>Total: ₱<?php echo number_format($total, 2, '.', ','); ?></h3>
                <form method="POST">
                    <input type="hidden" name="action" value="checkout">
                    <button type="submit" class="btn btn-primary">Checkout</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
