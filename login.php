<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $users = load_users();

    foreach ($users as $user) {
        if (strtolower(($user['email'] ?? '')) === $email && password_verify($password, $user['password'] ?? '')) {
            $_SESSION['user'] = $user;

            if (($user['role'] ?? '') === 'admin') {
                header('Location: admin-dashboard.php');
            } else {
                header('Location: user-dashboard.php');
            }
            exit;
        }
    }

    $error = 'Invalid email or password.';
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
    <div class="container auth-shell">
        <div class="auth-card">
            <span class="eyebrow">Welcome back</span>
            <h1>Login to GianeCycleHub</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input id="email" name="email" type="email" placeholder="you@example.com" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn btn-primary full">Login</button>
            </form>

            <div class="auth-meta">
                <p>Don’t have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
