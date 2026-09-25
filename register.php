<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if ($password !== $passwordConfirm) {
        $error = 'Passwords do not match.';
    } elseif (empty($name) || empty($email) || empty($password)) {
        $error = 'Please complete all required fields.';
    } else {
        $users = load_users();
        foreach ($users as $user) {
            if (strtolower(($user['email'] ?? '')) === $email) {
                $error = 'A user with this email already exists.';
                break;
            }
        }

        if (empty($error)) {
            $users[] = [
                'id' => time(),
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'user',
                'verified' => false,
                'verification_photo' => null,
                'created_at' => date('Y-m-d H:i:s')
            ];

            save_users($users);
            header('Location: login.php');
            exit;
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
    <div class="container auth-shell">
        <div class="auth-card">
            <span class="eyebrow">Create account</span>
            <h1>Join GianeCycleHub</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="input-group">
                    <label for="name">Full Name</label>
                    <input id="name" name="name" type="text" placeholder="Enter your name" required>
                </div>

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input id="email" name="email" type="email" placeholder="you@example.com" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Create a password" required>
                </div>

                <div class="input-group">
                    <label for="password_confirm">Confirm Password</label>
                    <input id="password_confirm" name="password_confirm" type="password" placeholder="Repeat your password" required>
                </div>

                <button type="submit" class="btn btn-primary full">Register</button>
            </form>

            <div class="auth-meta">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
