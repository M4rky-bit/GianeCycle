<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GianeCycleHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-auth="<?php echo isset($_SESSION['user']) ? 'logged-in' : 'guest'; ?>">
    <header class="site-header">
        <nav class="navbar container">
            <a class="brand" href="index.php">
                <span class="brand-mark">G</span>
                GianeCycleHub
            </a>

            <?php if ($user): ?>
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                    <div class="nav-links">
                        <a class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
                        <a class="<?php echo $currentPage === 'admin-dashboard.php' ? 'active' : ''; ?>" href="admin-dashboard.php">Motorcycles</a>
                        <a class="<?php echo $currentPage === 'shop.php' ? 'active' : ''; ?>" href="shop.php">Inventory</a>
                        <a class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>" href="profile.php">Profile</a>
                    </div>
                    <div class="nav-actions">
                        <a class="btn btn-secondary" href="logout.php">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="nav-links">
                        <a class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
                        <a class="<?php echo $currentPage === 'user-dashboard.php' ? 'active' : ''; ?>" href="user-dashboard.php">Dashboard</a>
                        <a class="<?php echo $currentPage === 'rentals.php' ? 'active' : ''; ?>" href="rentals.php">Rentals</a>
                        <a class="<?php echo $currentPage === 'shop.php' ? 'active' : ''; ?>" href="shop.php">Parts Shop</a>
                        <a class="<?php echo $currentPage === 'cart.php' ? 'active' : ''; ?>" href="cart.php">Cart</a>
                        <a class="<?php echo $currentPage === 'gps.php' ? 'active' : ''; ?>" href="gps.php">Location</a>
                        <a class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>" href="profile.php">Profile</a>
                    </div>
                    <div class="nav-actions">
                        <a class="btn btn-secondary" href="logout.php">Logout</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="nav-links">
                    <a class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
                    <a class="<?php echo $currentPage === 'rentals.php' ? 'active' : ''; ?>" href="rentals.php">Rentals</a>
                    <a class="<?php echo $currentPage === 'shop.php' ? 'active' : ''; ?>" href="shop.php">Shop</a>
                    <a class="<?php echo $currentPage === 'gps.php' ? 'active' : ''; ?>" href="gps.php">GPS</a>
                </div>
                <div class="nav-actions">
                    <a class="btn btn-secondary" href="login.php">Login</a>
                    <a class="btn btn-primary" href="register.php">Register</a>
                </div>
            <?php endif; ?>
        </nav>
    </header>

    <main>
