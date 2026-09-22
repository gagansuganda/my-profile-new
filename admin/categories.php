<?php include __DIR__ . '/partials/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Category</h1>
        <p class="page-subtitle">Kelola kategori konten portofolio dan blog Anda.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <select id="filter-type" class="form-control" style="width: auto; background-color: #121319; color: #fff; padding: 6px 12px; height: 38px;" onchange="loadCategories()">
            <option value="blog">Kategori Blog & Content</option>
            <option value="project">Kategori Project</option>
        </select>
        <button class="btn-primary" onclick="openAddModal()" style="width: auto; padding: 0 16px; height: 38px;">
            <i class="ri-add-line"></i> Tambah
        </button>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Tipe Kategori</th>
                    <th>Deskripsi</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="category-table-body">
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-secondary);">
                        <i class="ri-loader-4-line ri-spin"></i> Memuat data kategori...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Category Modal Form (Add & Edit) -->
<div class="modal" id="category-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-title">Tambah Category</h3>
            <button class="modal-close" onclick="closeModal('category-modal')">&times;</button>
        </div>
        <form id="category-form">
            <div class="modal-body">
                <input type="hidden" id="category-id">

                <div class="form-group">
                    <label class="form-label" for="category-name">Nama Category</label>
                    <input type="text" id="category-name" class="form-control" placeholder="Hubungan Masyarakat / Web Dev" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category-type">Tipe Konten</label>
                    <select id="category-type" class="form-control" style="background-color: #121319;">
                        <option value="blog">Blog & Content</option>
                        <option value="project">Project</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category-description">Deskripsi</label>
                    <textarea id="category-description" class="form-control" rows="4" placeholder="Tulis deskripsi opsional kategori di sini..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('category-modal')">Batal</button>
                <button type="submit" class="btn-primary" id="btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal as requested instead of raw confirm() popup (#7) -->
<div class="modal" id="delete-confirm-modal">
    <div class="modal-content" style="max-width: 400px; padding: 10px;">
        <div class="modal-header" style="border-bottom: none; padding-bottom: 0;">
            <h3 class="modal-title" style="color: var(--danger-color);"><i class="ri-error-warning-line"></i> Konfirmasi Hapus</h3>
            <button class="modal-close" onclick="closeModal('delete-confirm-modal')">&times;</button>
        </div>
        <div class="modal-body" style="padding-top: 10px;">
            <p id="delete-confirm-text" style="color: var(--text-primary); font-size: 14px;">Apakah Anda yakin ingin menghapus data ini secara permanen?</p>
        </div>
        <div class="modal-footer" style="border-top: none; padding-top: 0;">
            <button type="button" class="btn-secondary" onclick="closeModal('delete-confirm-modal')">Batal</button>
            <button type="button" class="btn-primary" id="btn-confirm-delete-action" style="background-color: var(--danger-color); box-shadow: none;">Hapus</button>
        </div>
    </div>
</div>

<script>
    let categoriesData = [];
    let targetDeleteId = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadCategories();

        document.getElementById('category-form').addEventListener('submit', handleFormSubmit);
        document.getElementById('btn-confirm-delete-action').addEventListener('click', executeDeletedCategory);
    });

    async function loadCategories() {
        const fType = document.getElementById('filter-type').value;
        const res = await apiFetch(`categories?type=${fType}`);
        const tbody = document.getElementById('category-table-body');

        if (res.success && res.data) {
            categoriesData = res.data;
            tbody.innerHTML = '';

            if (categoriesData.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-secondary);">
                        Tidak ada kategori tipe [${fType.toUpperCase()}] tersedia. Silakan buat baru.
                    </td>
                </tr>
            `;
                return;
            }

            categoriesData.forEach(cat => {
                const badgesT = cat.type === 'project' ?
                    '<span class="badge" style="background-color: rgba(28, 166, 185, 0.12); color: var(--accent-color);">Project</span>' :
                    '<span class="badge" style="background-color: rgba(52, 197, 110, 0.12); color: var(--success-color);">Blog</span>';

                tbody.innerHTML += `
                <tr>
                    <td style="font-weight: 600; color: #fff;">${cat.name}</td>
                    <td><code style="color: var(--accent-color); font-size: 11px;">${cat.slug}</code></td>
                    <td>${badgesT}</td>
                    <td style="color: var(--text-secondary); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        ${cat.description || '-'}
                    </td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon edit" onclick="openEditModal('${cat.id}')">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button class="btn-icon delete" onclick="deleteCategory('${cat.id}')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            });
        } else {
            tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; color: var(--danger-color);">
                    Gagal memuat kategori: ${res.message}
                </td>
            </tr>
        `;
        }
    }

    function openAddModal() {
        document.getElementById('modal-title').textContent = 'Tambah Category';
        document.getElementById('category-id').value = '';
        document.getElementById('category-name').value = '';
        document.getElementById('category-type').value = document.getElementById('filter-type').value;
        document.getElementById('category-description').value = '';

        openModal('category-modal');
    }

    function openEditModal(id) {
        const cat = categoriesData.find(item => item.id === id);
        if (!cat) return;

        document.getElementById('modal-title').textContent = 'Ubah Category';
        document.getElementById('category-id').value = cat.id;
        document.getElementById('category-name').value = cat.name;
        document.getElementById('category-type').value = cat.type || 'blog';
        document.getElementById('category-description').value = cat.description || '';

        openModal('category-modal');
    }

    async function handleFormSubmit(e) {
        e.preventDefault();

        const id = document.getElementById('category-id').value;
        const name = document.getElementById('category-name').value.trim();
        const type = document.getElementById('category-type').value;
        const description = document.getElementById('category-description').value.trim();
        const btnSubmit = document.getElementById('btn-submit');

        if (!name) return;

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Menyimpan...';

        let method = 'POST';
        let url = 'categories';

        if (id) {
            method = 'PUT';
            url = `categories/${id}`;
        }

        const res = await apiFetch(url, {
            method: method,
            body: JSON.stringify({
                name,
                type,
                description
            })
        });

        btnSubmit.disabled = false;
        btnSubmit.textContent = 'Simpan';

        if (res.success) {
            closeModal('category-modal');
            loadCategories();
        } else {
            alert('Gagal menyimpan kategori: ' + res.message);
        }
    }

    function deleteCategory(id) {
        targetDeleteId = id;
        document.getElementById('delete-confirm-text').textContent = 'Apakah Anda yakin ingin menghapus kategori ini? Semua konten terkait mungkin akan terpengaruh.';
        openModal('delete-confirm-modal');
    }

    async function executeDeletedCategory() {
        if (!targetDeleteId) return;
        const btn = document.getElementById('btn-confirm-delete-action');
        btn.disabled = true;
        btn.textContent = 'Menghapus...';

        const res = await apiFetch(`categories/${targetDeleteId}`, {
            method: 'DELETE'
        });

        btn.disabled = false;
        btn.textContent = 'Hapus';
        closeModal('delete-confirm-modal');

        if (res.success) {
            loadCategories();
        } else {
            alert('Gagal menghapus kategori: ' + res.message);
        }
        targetDeleteId = null;
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>