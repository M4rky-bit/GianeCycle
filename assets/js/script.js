document.addEventListener('DOMContentLoaded', () => {
    const isLoggedIn = document.body.dataset.auth === 'logged-in';
    const bikeSelect = document.getElementById('motorcycle');

    if (bikeSelect) {
        const params = new URLSearchParams(window.location.search);
        const selectedBike = params.get('bike');

        if (selectedBike) {
            const normalized = decodeURIComponent(selectedBike).replace(/\+/g, ' ');
            const optionExists = Array.from(bikeSelect.options).some((option) => option.value === normalized);
            if (optionExists) {
                bikeSelect.value = normalized;
            }
        }
    }

    document.querySelectorAll('[data-require-login="true"]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (!isLoggedIn) {
                event.preventDefault();
                window.location.href = 'login.php';
                return;
            }
        });
    });
});
