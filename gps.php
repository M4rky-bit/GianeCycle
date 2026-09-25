<?php
require_once __DIR__ . '/includes/auth.php';
include __DIR__ . '/includes/header.php';
?>

<section class="page-section container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Find us</span>
            <h1>Our exact location</h1>
        </div>
    </div>

    <div class="content-grid two-col">
        <div class="panel">
            <h3>Visit our shop</h3>
            <p><strong>GianeCycleHub</strong></p>
            <p>Coordinates: 6.347604, 124.937056</p>
            <p>Open Monday to Sunday: 8:00 AM - 8:00 PM</p>
            <p>Phone: +63 912 345 6789</p>
            <a class="btn btn-primary" href="https://maps.google.com/?q=6.347604,124.937056" target="_blank" rel="noreferrer">Open in Google Maps</a>
        </div>

        <div class="panel map-wrap">
            <iframe
                src="https://www.google.com/maps?q=6.347604,124.937056&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="GianeCycleHub Map">
            </iframe>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
