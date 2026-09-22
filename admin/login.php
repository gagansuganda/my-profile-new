<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Auth\AuthMiddleware;

if (AuthMiddleware::check()) {
    header('Location: /admin/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke akun kamu - gagans.id</title>
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body class="login-body">

    <div class="login-wrapper">
        <div class="login-header">
            <a href="#" class="login-logo">
                <i class="ri-braces-line"></i> gagans.id
            </a>
        </div>

        <div class="login-card">
            <h2 class="login-title">Masuk ke akun kamu</h2>

            <div id="alert-container"></div>

            <form id="login-form">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" class="form-control" placeholder="nama@email.com" required value="gaganbaonkk@gmail.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="password" class="form-control" placeholder="Masukkan password" required="required">
                </div>

                <button type="submit" class="btn-primary" id="btn-submit">
                    Masuk
                </button>
            </form>
        </div>

        <!-- Replicated copyright footer from screenshot -->
        <div style="text-align: center; margin-top: 40px; font-size: 11px; color: var(--text-secondary);">
            © 2026 gagans.id. All rights reserved.
        </div>
    </div>

    <script src="assets/js/api.js"></script>
    <script>
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const btnSubmit = document.getElementById('btn-submit');
            const alertContainer = document.getElementById('alert-container');

            alertContainer.innerHTML = '';
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Memproses...';

            const res = await apiFetch('auth/login', {
                method: 'POST',
                body: JSON.stringify({
                    email,
                    password
                })
            });

            if (res.success) {
                window.location.href = 'index.php';
            } else {
                btnSubmit.disabled = false;
                btnSubmit.textContent = 'Masuk';
                alertContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="ri-error-warning-line"></i> ${res.message}
            </div>
        `;
            }
        });
    </script>
</body>

</html>