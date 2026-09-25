<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
refresh_session_user();
include __DIR__ . '/includes/header.php';
$user = $_SESSION['user'];
?>

<section class="page-section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Profile</span>
            <h1>My account information</h1>
        </div>
    </div>

    <div class="content-grid two-col">
        <div class="panel">
            <h3>Account details</h3>
            <ul class="list">
                <li><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
                <li><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></li>
                <li><strong>Verification status:</strong> <?php echo ($user['verified'] ?? false) ? 'Verified user' : 'Pending verification'; ?></li>
            </ul>
        </div>

        <div class="panel">
            <h3>Verification</h3>
            <?php if (!empty($user['verification_photo'])): ?>
                <p><strong>Status:</strong> <?php echo ($user['verified'] ?? false) ? 'Verified' : 'Pending'; ?></p>
                <img src="<?php echo htmlspecialchars($user['verification_photo']); ?>" alt="Verification photo" class="verification-preview">
            <?php else: ?>
                <p><strong>Status:</strong> Pending verification</p>
                <p class="muted">No verification image uploaded yet.</p>
            <?php endif; ?>
            <p class="muted">Upload your ID or proof to verify your account.</p>
            <a href="verification.php" class="btn btn-primary">Manage verification</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
