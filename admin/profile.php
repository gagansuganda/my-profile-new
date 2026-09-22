<?php include __DIR__ . '/partials/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Pengaturan</h1>
        <p class="page-subtitle">Kelola profil, keamanan, tampilan, serta program reseller dan affiliate.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 240px 1fr; gap: 30px;">
    <!-- Left Navigation Settings -->
    <div style="display: flex; flex-direction: column; gap: 8px;">
        <a href="#" class="nav-link active" style="padding: 12px 16px; font-weight: 600;">
            <div class="nav-link-left">
                <i class="ri-user-line" style="font-size: 16px;"></i>
                <span>Profil</span>
            </div>
        </a>
        <a href="#" class="nav-link" style="padding: 12px 16px;" onclick="alert('Bagian Integrasi GitHub dikoordinasikan langsung via OAuth.');">
            <div class="nav-link-left">
                <i class="ri-github-line" style="font-size: 16px;"></i>
                <span>GitHub</span>
            </div>
        </a>
        <a href="#" class="nav-link" style="padding: 12px 16px;" onclick="alert('Keamanan & Autentikasi 2-Faktor aktif menggunakan JWT Cookie.');">
            <div class="nav-link-left">
                <i class="ri-shield-keyhole-line" style="font-size: 16px;"></i>
                <span>Keamanan</span>
            </div>
        </a>
        <a href="#" class="nav-link" style="padding: 12px 16px;" onclick="alert('Tema bawaan saat ini dikunci pada Mode Gelap (AICoding Theme).');">
            <div class="nav-link-left">
                <i class="ri-palette-line" style="font-size: 16px;"></i>
                <span>Editor</span>
            </div>
        </a>
        <a href="#" class="nav-link" style="padding: 12px 16px;" onclick="alert('Kustomisasi gaya, ukuran font bertenaga Plus Jakarta Sans.');">
            <div class="nav-link-left">
                <i class="ri-eye-line" style="font-size: 16px;"></i>
                <span>Tampilan</span>
            </div>
        </a>
        <a href="#" class="nav-link" style="padding: 12px 16px;" onclick="alert('Fitur Program Afiliasi & Reseller (Segera Hadir)');">
            <div class="nav-link-left">
                <i class="ri-user-shared-line" style="font-size: 16px;"></i>
                <span>Reseller</span>
            </div>
        </a>
    </div>

    <!-- Right Settings Dashboard Content -->
    <div>
        <div class="card" style="padding: 24px;">
            <h3 class="card-title" style="margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">Profil</h3>

            <div id="settings-alert-container"></div>

            <!-- Profile Photo Upload / Remove Replicated from Screenshot -->
            <div class="profile-photo-container" style="margin-bottom: 30px;">
                <img id="profile-img" src="../assets/img/profile.png" alt="Foto Profil" class="profile-avatar-large" onerror="this.src='https://placehold.co/150/181a22/fff?text=GS'">
                <div class="profile-photo-info">
                    <h4 style="font-size: 14px; font-weight: 600;">Foto Profil</h4>
                    <p>JPG atau PNG. Otomatis dipotong jadi kotak.</p>
                    <div class="photo-buttons">
                        <button class="btn-primary" style="padding: 6px 14px; font-size: 12px; font-weight: 500; width: auto; box-shadow: none;" onclick="document.getElementById('profile-photo-file').click();">Ubah Foto</button>
                        <button class="btn-secondary" style="padding: 6px 14px; font-size: 12px; font-weight: 500; border-color: rgba(217, 63, 84, 0.3); color: var(--danger-color);" onclick="removeProfilePhoto()">Hapus</button>
                    </div>
                    <input type="file" id="profile-photo-file" style="display: none;" accept="image/*" onchange="uploadProfilePhoto(this)">
                </div>
            </div>

            <!-- Form fields replicated exactly as screenshot -->
            <form id="settings-form">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="full-name">Nama Lengkap</label>
                        <input type="text" id="full-name" class="form-control" placeholder="Gagan Suganda" required value="Gagan Suganda">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone-number">Nomor HP</label>
                        <input type="text" id="phone-number" class="form-control" placeholder="089664044727" value="089664044727">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <label class="form-label" style="margin-bottom: 0;">Email</label>
                        <span class="badge" style="background-color: rgba(255,152,0,0.15); color: #ff9800; font-size: 10px; padding: 2px 6px; border-radius: 4px; border: 1px solid rgba(255,152,0,0.25);">TERKUNCI</span>
                    </div>
                    <input type="email" id="email" class="form-control" placeholder="gaganbaonkk@gmail.com" readonly value="gaganbaonkk@gmail.com" style="background-color: rgba(0,0,0,0.2); color: var(--text-secondary); cursor: not-allowed; border-color: var(--border-color);">
                </div>

                <div style="border-top: 1px solid var(--border-color); padding-top: 24px; margin-top: 24px; margin-bottom: 30px;">
                    <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 6px;">Ubah Kata Sandi (Opsional)</h4>
                    <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 16px;">Isi form di bawah hanya jika Anda ingin mengubah kata sandi masuk admin saat ini.</p>

                    <div class="form-group">
                        <label class="form-label" for="new-password">Kata Sandi Baru</label>
                        <input type="password" id="new-password" class="form-control" placeholder="Masukkan kata sandi baru (Minimal 5 karakter)">
                    </div>
                </div>

                <!-- Submit Button aligned right exactly as screenshot -->
                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-primary" id="btn-save-settings" style="width: auto; padding: 12px 24px;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        loadProfileData();

        document.getElementById('settings-form').addEventListener('submit', handleFormSubmit);
    });

    async function loadProfileData() {
        const res = await apiFetch('auth/me');
        if (res.success && res.data) {
            document.getElementById('full-name').value = res.data.name;
            document.getElementById('phone-number').value = res.data.phone || '';
            document.getElementById('email').value = res.data.email;
        }
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const name = document.getElementById('full-name').value.trim();
        const phone = document.getElementById('phone-number').value.trim();
        const password = document.getElementById('new-password').value;
        const btnSubmit = document.getElementById('btn-save-settings');
        const alertContainer = document.getElementById('settings-alert-container');

        if (password && password.length < 5) {
            alertContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="ri-error-warning-line"></i> Kata sandi baru minimal harus diisi 5 karakter.
            </div>
        `;
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Menyimpan...';
        alertContainer.innerHTML = '';

        const payload = {
            name,
            phone
        };
        if (password) {
            payload.password = password;
        }

        const res = await apiFetch('auth/me', {
            method: 'PUT',
            body: JSON.stringify(payload)
        });

        btnSubmit.disabled = false;
        btnSubmit.textContent = 'Simpan Perubahan';

        if (res.success) {
            document.getElementById('new-password').value = '';
            alertContainer.innerHTML = `
            <div class="alert alert-success">
                <i class="ri-checkbox-circle-line"></i> Profil berhasil diperbarui.
            </div>
        `;
            // Refresh sidebar branding representation
            if (typeof loadSidebarProfile === 'function') {
                loadSidebarProfile();
            }
        } else {
            alertContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="ri-error-warning-line"></i> Gagal menyimpan: ${res.message}
            </div>
        `;
        }
    }

    function removeProfilePhoto() {
        if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
            // Just reset back to default
            document.getElementById('profile-img').src = 'https://placehold.co/150/181a22/fff?text=GS';
            alert('Foto profil berhasil dicopot.');
        }
    }

    function uploadProfilePhoto(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Simulating upload visually for local flow since it stays in assets/img/profile.png or fallback
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-img').src = e.target.result;
                // Also option to change top/header profile visually
            };
            reader.readAsDataURL(file);

            alert('Demo: Foto berhasil dipertinjau di bawah.');
        }
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>