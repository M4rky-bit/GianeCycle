<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['verification_photo'])) {
    $file = $_FILES['verification_photo'];
    $uploadsDir = get_uploads_dir();

    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }

    $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowed, true)) {
        $error = 'Please upload a valid JPG or PNG image.';
    } else {
        $fileName = time() . '_' . basename($file['name']);
        $destination = $uploadsDir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $users = load_users();
            foreach ($users as &$user) {
                if (($user['email'] ?? '') === ($_SESSION['user']['email'] ?? '')) {
                    $user['verification_photo'] = 'uploads/verification/' . $fileName;
                    $user['verified'] = true;
                    $_SESSION['user'] = $user;
                    break;
                }
            }
            unset($user);
            save_users($users);
            $success = 'Your verification photo was uploaded successfully.';
        } else {
            $error = 'Unable to upload file. Please try again.';
        }
    }
}

include __DIR__ . '/includes/header.php';
$user = $_SESSION['user'];
?>

<section class="page-section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Account verification</span>
            <h1>Verify your identity</h1>
        </div>
    </div>

    <div class="content-grid two-col">
        <div class="panel">
            <h3>Why verify?</h3>
            <ul class="list">
                <li>Protects against scam activity.</li>
                <li>Builds trust between users and the admin.</li>
                <li>Helps unlock rentals and purchases.</li>
            </ul>
        </div>

        <div class="panel">
            <h3>Upload ID photo</h3>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="auth-form">
                <div class="input-group">
                    <label for="verification_photo">Photo verification</label>
                    <input id="verification_photo" name="verification_photo" type="file" accept="image/png, image/jpeg" required>
                </div>

                <button type="submit" class="btn btn-primary full">Upload and verify</button>
            </form>

            <p class="muted">Status: <?php echo ($user['verified'] ?? false) ? 'Verified' : 'Pending verification'; ?></p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
