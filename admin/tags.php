<?php include __DIR__ . '/partials/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Tag</h1>
        <p class="page-subtitle">Kelola label tag untuk klasifikasi blog dan proyek portofolio Anda.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <select id="filter-type" class="form-control" style="width: auto; background-color: #121319; color: #fff; padding: 6px 12px; height: 38px;" onchange="loadTags()">
            <option value="blog">Tag Blog & Content</option>
            <option value="project">Tag Project</option>
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
                    <th>Tag Nama</th>
                    <th>Slug</th>
                    <th>Tipe Tag</th>
                    <th>Dibuat Pada</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tag-table-body">
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-secondary);">
                        <i class="ri-loader-4-line ri-spin"></i> Memuat data tag...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Tag Modal Form (Add & Edit) -->
<div class="modal" id="tag-modal">
    <div class="modal-content" style="max-width: 440px;">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-title">Tambah Tag</h3>
            <button class="modal-close" onclick="closeModal('tag-modal')">&times;</button>
        </div>
        <form id="tag-form">
            <div class="modal-body">
                <input type="hidden" id="tag-id">

                <div class="form-group">
                    <label class="form-label" for="tag-name">Nama Tag</label>
                    <input type="text" id="tag-name" class="form-control" placeholder="PHP, CI4, Vue.js, Tailwind" required>
                </div>

                <div class="form-group" style="margin-top: 16px; margin-bottom: 0;">
                    <label class="form-label" for="tag-type">Peruntukan Tag</label>
                    <select id="tag-type" class="form-control" style="background-color: #121319;">
                        <option value="blog">Blog & Content</option>
                        <option value="project">Project</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('tag-modal')">Batal</button>
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
            <p id="delete-confirm-text" style="color: var(--text-primary); font-size: 14px;">Apakah Anda yakin ingin menghapus tag ini secara permanen?</p>
        </div>
        <div class="modal-footer" style="border-top: none; padding-top: 0;">
            <button type="button" class="btn-secondary" onclick="closeModal('delete-confirm-modal')">Batal</button>
            <button type="button" class="btn-primary" id="btn-confirm-delete-action" style="background-color: var(--danger-color); box-shadow: none;">Hapus</button>
        </div>
    </div>
</div>

<script>
    let tagsData = [];
    let targetDeleteId = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadTags();

        document.getElementById('tag-form').addEventListener('submit', handleFormSubmit);
        document.getElementById('btn-confirm-delete-action').addEventListener('click', executeDeletedTag);
    });

    async function loadTags() {
        const fType = document.getElementById('filter-type').value;
        const res = await apiFetch(`tags?type=${fType}`);
        const tbody = document.getElementById('tag-table-body');

        if (res.success && res.data) {
            tagsData = res.data;
            tbody.innerHTML = '';

            if (tagsData.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-secondary);">
                        Tidak ada tag tipe [${fType.toUpperCase()}] tersedia. Silakan buat baru.
                    </td>
                </tr>
            `;
                return;
            }

            tagsData.forEach(tag => {
                const badgesT = tag.type === 'project' ?
                    '<span class="badge" style="background-color: rgba(28, 166, 185, 0.12); color: var(--accent-color);">Project</span>' :
                    '<span class="badge" style="background-color: rgba(52, 197, 110, 0.12); color: var(--success-color);">Blog</span>';

                tbody.innerHTML += `
                <tr>
                    <td>
                        <span class="badge" style="background-color: var(--active-menu-bg); color: var(--accent-color); border: 1px solid rgba(28, 166, 185, 0.2); padding: 4px 10px; border-radius: 6px;">
                            # ${tag.name}
                        </span>
                    </td>
                    <td><code style="color: var(--text-secondary); font-size: 11px;">${tag.slug}</code></td>
                    <td>${badgesT}</td>
                    <td style="color: var(--text-secondary);">${tag.created_at || '-'}</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon edit" onclick="openEditModal('${tag.id}')">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button class="btn-icon delete" onclick="deleteTag('${tag.id}')">
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
                    Gagal memuat tag: ${res.message}
                </td>
            </tr>
        `;
        }
    }

    function openAddModal() {
        document.getElementById('modal-title').textContent = 'Tambah Tag';
        document.getElementById('tag-id').value = '';
        document.getElementById('tag-name').value = '';
        document.getElementById('tag-type').value = document.getElementById('filter-type').value;

        openModal('tag-modal');
    }

    function openEditModal(id) {
        const tag = tagsData.find(item => item.id === id);
        if (!tag) return;

        document.getElementById('modal-title').textContent = 'Ubah Tag';
        document.getElementById('tag-id').value = tag.id;
        document.getElementById('tag-name').value = tag.name;
        document.getElementById('tag-type').value = tag.type || 'blog';

        openModal('tag-modal');
    }

    async function handleFormSubmit(e) {
        e.preventDefault();

        const id = document.getElementById('tag-id').value;
        const name = document.getElementById('tag-name').value.trim();
        const type = document.getElementById('tag-type').value;
        const btnSubmit = document.getElementById('btn-submit');

        if (!name) return;

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Menyimpan...';

        let method = 'POST';
        let url = 'tags';

        if (id) {
            method = 'PUT';
            url = `tags/${id}`;
        }

        const res = await apiFetch(url, {
            method: method,
            body: JSON.stringify({
                name,
                type
            })
        });

        btnSubmit.disabled = false;
        btnSubmit.textContent = 'Simpan';

        if (res.success) {
            closeModal('tag-modal');
            loadTags();
        } else {
            alert('Gagal menyimpan tag: ' + res.message);
        }
    }

    function deleteTag(id) {
        targetDeleteId = id;
        document.getElementById('delete-confirm-text').textContent = 'Apakah Anda yakin ingin menghapus tag ini? Kategori dan hubungan multi-select terkait akan dicopot.';
        openModal('delete-confirm-modal');
    }

    async function executeDeletedTag() {
        if (!targetDeleteId) return;
        const btn = document.getElementById('btn-confirm-delete-action');
        btn.disabled = true;
        btn.textContent = 'Menghapus...';

        const res = await apiFetch(`tags/${targetDeleteId}`, {
            method: 'DELETE'
        });

        btn.disabled = false;
        btn.textContent = 'Hapus';
        closeModal('delete-confirm-modal');

        if (res.success) {
            loadTags();
        } else {
            alert('Gagal menghapus tag: ' + res.message);
        }
        targetDeleteId = null;
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>