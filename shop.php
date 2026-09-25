<?php
require_once __DIR__ . '/includes/auth.php';

$user = $_SESSION['user'] ?? null;
$products = load_products();
$message = '';
$editingProduct = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        if (($user['role'] ?? '') !== 'admin') {
            header('Location: login.php');
            exit;
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0);
        $icon = trim((string) ($_POST['icon'] ?? '🧰'));

        if ($name !== '' && $price > 0) {
            $products[] = [
                'id' => time() + count($products),
                'name' => $name,
                'price' => $price,
                'icon' => $icon !== '' ? $icon : '🧰'
            ];
            save_products($products);
            $message = 'Product added successfully.';
        } else {
            $message = 'Please enter a valid product name and price.';
        }
    }

    if ($action === 'edit') {
        if (($user['role'] ?? '') !== 'admin') {
            header('Location: login.php');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0);
        $icon = trim((string) ($_POST['icon'] ?? '🧰'));

        foreach ($products as $index => $product) {
            if ((int) $product['id'] === $id) {
                $products[$index]['name'] = $name;
                $products[$index]['price'] = $price;
                $products[$index]['icon'] = $icon !== '' ? $icon : '🧰';
                break;
            }
        }

        save_products($products);
        $message = 'Product updated successfully.';
    }

    if ($action === 'delete') {
        if (($user['role'] ?? '') !== 'admin') {
            header('Location: login.php');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $products = array_values(array_filter($products, fn($product) => (int) $product['id'] !== $id));
        save_products($products);
        $message = 'Product removed successfully.';
    }

    if ($action === 'add_to_cart') {
        if (!$user) {
            header('Location: login.php');
            exit;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $cart = $_SESSION['cart'] ?? [];

        foreach ($products as $product) {
            if ((int) $product['id'] === $productId) {
                $found = false;
                foreach ($cart as $index => $cartItem) {
                    if ((int) $cartItem['id'] === $productId) {
                        $cart[$index]['quantity'] = (int) ($cartItem['quantity'] ?? 1) + 1;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $cart[] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'icon' => $product['icon'] ?? '🧰',
                        'quantity' => 1
                    ];
                }

                $_SESSION['cart'] = $cart;
                $message = 'Item added to cart.';
                break;
            }
        }
    }
}

if (($user['role'] ?? '') === 'admin' && isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    foreach ($products as $product) {
        if ((int) $product['id'] === $editId) {
            $editingProduct = $product;
            break;
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="page-section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Motorcycle parts</span>
            <h1><?php echo ($user['role'] ?? '') === 'admin' ? 'Manage inventory' : 'Shop quality parts'; ?></h1>
        </div>
        <?php if ($user && ($user['role'] ?? '') !== 'admin'): ?>
            <a href="cart.php" class="btn btn-primary">View cart</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (($user['role'] ?? '') === 'admin'): ?>
        <div class="content-grid two-col">
            <div class="panel">
                <h3><?php echo $editingProduct ? 'Edit product' : 'Add product'; ?></h3>
                <form method="POST" class="inventory-form">
                    <input type="hidden" name="action" value="<?php echo $editingProduct ? 'edit' : 'add'; ?>">
                    <?php if ($editingProduct): ?>
                        <input type="hidden" name="id" value="<?php echo (int) $editingProduct['id']; ?>">
                    <?php endif; ?>

                    <div class="input-group">
                        <label for="name">Product name</label>
                        <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($editingProduct['name'] ?? ''); ?>" required>
                    </div>

                    <div class="input-group">
                        <label for="price">Price</label>
                        <input id="price" name="price" type="number" min="1" step="0.01" value="<?php echo htmlspecialchars((string) ($editingProduct['price'] ?? 0)); ?>" required>
                    </div>

                    <div class="input-group">
                        <label for="icon">Icon</label>
                        <input id="icon" name="icon" type="text" value="<?php echo htmlspecialchars($editingProduct['icon'] ?? '🧰'); ?>" placeholder="🧰">
                    </div>

                    <div class="inventory-actions">
                        <button type="submit" class="btn btn-primary"><?php echo $editingProduct ? 'Save changes' : 'Add item'; ?></button>
                        <?php if ($editingProduct): ?>
                            <a class="btn btn-secondary" href="shop.php">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="panel">
                <h3>Current inventory</h3>
                <div class="inventory-table-wrap">
                    <table class="data-table inventory-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(($product['icon'] ?? '🧰') . ' ' . $product['name']); ?></td>
                                    <td>₱<?php echo number_format((float) $product['price'], 2, '.', ','); ?></td>
                                    <td class="inventory-actions-cell">
                                        <a class="btn btn-secondary small" href="shop.php?edit=<?php echo (int) $product['id']; ?>">Edit</a>
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int) $product['id']; ?>">
                                            <button class="btn btn-danger small" type="submit">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="product-grid three-up">
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <div class="product-image placeholder"><?php echo htmlspecialchars($product['icon'] ?? '🧰'); ?></div>
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p>₱<?php echo number_format((float) $product['price'], 2, '.', ','); ?></p>
                    <?php if ($user): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="add_to_cart">
                            <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                            <button class="btn btn-primary full" type="submit">Add to cart</button>
                        </form>
                    <?php else: ?>
                        <a class="btn btn-primary full" href="login.php">Add to cart</a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
