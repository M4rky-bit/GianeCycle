<?php
require_once __DIR__ . '/includes/auth.php';
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow">Ride smarter</span>
            <h1>Find the right bike and parts for your next adventure.</h1>
            <p>
                GianeCycleHub is an online motorcycle rental and parts shopping system that helps customers reserve bikes, buy motorcycle components, and verify trusted account identities before every transaction.
            </p>
            <div class="hero-actions">
                <?php if (isset($_SESSION['user'])): ?>
                    <a class="btn btn-primary" href="<?php echo $_SESSION['user']['role'] === 'admin' ? 'admin-dashboard.php' : 'user-dashboard.php'; ?>">Open dashboard</a>
                <?php else: ?>
                    <a class="btn btn-primary" href="register.php">Create account</a>
                    <a class="btn btn-secondary" href="login.php">Login</a>
                <?php endif; ?>
            </div>
            <ul class="quick-points">
                <li>Online booking and reservation</li>
                <li>Exact GPS location access</li>
                <li>ID photo verification for safety</li>
            </ul>
        </div>

        <div class="hero-card">
            <div class="card-badge">Today’s availability</div>
            <h3>Featured bikes</h3>
            <div class="mini-bike">
                <div>
                    <strong>Honda Click 160</strong>
                    <span>Manual • 2/day</span>
                </div>
                <span class="price">₱1,250/day</span>
            </div>
            <div class="mini-bike">
                <div>
                    <strong>Yamaha Mio Sporty</strong>
                    <span>Automatic • 3/day</span>
                </div>
                <span class="price">₱1,050/day</span>
            </div>
            <div class="mini-bike">
                <div>
                    <strong>Kawasaki Raider</strong>
                    <span>Sport • 1/day</span>
                </div>
                <span class="price">₱2,400/day</span>
            </div>
        </div>
    </div>
</section>

<section class="feature-section container">
    <div class="section-head">
        <span class="eyebrow">Built for riders</span>
        <h2>Everything you need in one motorcycle platform</h2>
    </div>

    <div class="feature-grid">
        <article class="feature-card">
            <div class="icon">🛵</div>
            <h3>Book or reserve online</h3>
            <p>Reserve your motorcycle anytime with flexible booking options and instant confirmation.</p>
        </article>
        <article class="feature-card">
            <div class="icon">📍</div>
            <h3>GPS tracking & location</h3>
            <p>Find our exact location quickly and arrange pickup or drop-off without confusion.</p>
        </article>
        <article class="feature-card">
            <div class="icon">✅</div>
            <h3>Photo verification</h3>
            <p>Users must upload a valid photo to help prevent scams and build trust with the shop.</p>
        </article>
    </div>
</section>

<section class="stats-band">
    <div class="container stats-grid">
        <div>
            <strong>1.2k+</strong>
            <span>Bike rentals completed</span>
        </div>
        <div>
            <strong>320+</strong>
            <span>Parts sold monthly</span>
        </div>
        <div>
            <strong>98%</strong>
            <span>Customer satisfaction</span>
        </div>
        <div>
            <strong>24/7</strong>
            <span>Support access</span>
        </div>
    </div>
</section>

<section class="container info-section">
    <div class="section-head left">
        <span class="eyebrow">How it works</span>
        <h2>Fast, secure, and user-friendly</h2>
    </div>

    <div class="process-grid">
        <div class="process-card">
            <span>01</span>
            <h3>Create account</h3>
            <p>Register as a user and complete your profile with personal information.</p>
        </div>
        <div class="process-card">
            <span>02</span>
            <h3>Upload verification</h3>
            <p>Submit a photo to establish trust and help the admin verify your account.</p>
        </div>
        <div class="process-card">
            <span>03</span>
            <h3>Reserve or shop</h3>
            <p>Book a motorcycle or browse motorcycle parts through the online system.</p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
