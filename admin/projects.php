<?php include __DIR__ . '/partials/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Project</h1>
        <p class="page-subtitle">Kelola daftar portofolio pengerjaan karya, web app, dan sistem Anda.</p>
    </div>
    <button class="btn-primary" onclick="openAddModal()" style="width: auto; padding: 0 16px; height: 38px;">
        <i class="ri-add-line"></i> Tambah
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 80px;">Pratinjau</th>
                    <th>Judul Projek / Nama</th>
                    <th>Kategori</th>
                    <th>Tag Rekayasa</th>
                    <th>Tautan Luar</th>
                    <th>Status</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="project-table-body">
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-secondary);">
                        <i class="ri-loader-4-line ri-spin"></i> Memuat data projek...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Project Modal Form (Add & Edit Edit) -->
<div class="modal" id="project-modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-title">Tambah Project Baru</h3>
            <button class="modal-close" onclick="closeModal('project-modal')">&times;</button>
        </div>
        <form id="project-form" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" id="project-id" name="id">

                <div class="form-group">
                    <label class="form-label" for="project-title">Nama Projek / Sistem</label>
                    <input type="text" id="project-title" name="title" class="form-control"
                        placeholder="Aplikasi HRD Keuangan / Company Profile" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="project-category">Kategori Projek</label>
                        <select id="project-category" name="category_id" class="form-control"
                            style="background-color: #121319;">
                            <option value="">-- Pilih Kategori --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="project-status">Status Projek</label>
                        <select id="project-status" name="status" class="form-control"
                            style="background-color: #121319;">
                            <option value="draft">Draft (Simpan Internal)</option>
                            <option value="published">Published (Publikasikan)</option>
                        </select>
                    </div>
                </div>

                <!-- User friendly checkbox group selection to replace keyboard rigid multiselect (#6) -->
                <div class="form-group">
                    <label class="form-label">Tag yang digunakan (Pilih satu atau lebih)</label>
                    <div id="project-tags-container"
                        style="display: flex; flex-wrap: wrap; gap: 10px; padding: 12px; background-color: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 8px; max-height: 110px; overflow-y: auto;">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="project-url">Tautan Eksternal Projek (URL Link)</label>
                    <input type="url" id="project-url" name="project_url" class="form-control"
                        placeholder="https://github.com/akun/url atau https://demo-projek.com">
                </div>

                <!-- Quill Rich Text Editor integration representing clean Word-like environment (#12) -->
                <div class="form-group">
                    <label class="form-label">Uraian / Deskripsi Projek</label>
                    <div id="quill-editor"
                        style="height: 240px; background-color: #121319; color: #fff; border: 1px solid var(--border-color); border-radius: 0 0 8px 8px;">
                    </div>
                    <input type="hidden" id="project-description" name="description">
                </div>

                <!-- Custom designed bootstrap styled photo files input component (#5) with multiple upload supports (#4) -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="project-image">Tangkapan Layar Unggulan (Projek Banner) / Galeri
                        Media (Bulk Upload Didukung)</label>
                    <div class="input-group" style="display: flex; flex-direction: column;">
                        <input type="file" id="project-image" name="images[]" class="form-control" accept="image/*"
                            multiple style="border-radius: 8px;">
                        <span class="text-muted"
                            style="font-size: 11px; color: var(--text-secondary); margin-top: 6px;">Mengunggah multiple
                            gambar sekaligus didukung. Format: JPG, PNG, WEBP (Max 2MB per file).</span>
                    </div>
                    <div id="image-preview-container" style="margin-top: 10px; display: none;">
                        <img id="image-preview" src=""
                            style="max-height: 120px; border-radius: 6px; border: 1px solid var(--border-color);">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('project-modal')">Batal</button>
                <button type="submit" class="btn-primary" id="btn-submit">Simpan Projek</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal as requested instead of raw confirm() popup (#7) -->
<div class="modal" id="delete-confirm-modal">
    <div class="modal-content" style="max-width: 400px; padding: 10px;">
        <div class="modal-header" style="border-bottom: none; padding-bottom: 0;">
            <h3 class="modal-title" style="color: var(--danger-color);"><i class="ri-error-warning-line"></i> Konfirmasi
                Hapus</h3>
            <button class="modal-close" onclick="closeModal('delete-confirm-modal')">&times;</button>
        </div>
        <div class="modal-body" style="padding-top: 10px;">
            <p id="delete-confirm-text" style="color: var(--text-primary); font-size: 14px;">Apakah Anda yakin ingin
                menghapus data projek ini secara permanen?</p>
        </div>
        <div class="modal-footer" style="border-top: none; padding-top: 0;">
            <button type="button" class="btn-secondary" onclick="closeModal('delete-confirm-modal')">Batal</button>
            <button type="button" class="btn-primary" id="btn-confirm-delete-action"
                style="background-color: var(--danger-color); box-shadow: none;">Hapus</button>
        </div>
    </div>
</div>

<script>
    let projectsData = [];
    let categoriesOptionData = [];
    let tagsOptionData = [];
    let targetDeleteId = null;
    let quill = null;

    document.addEventListener('DOMContentLoaded', async function() {
        initializeQuill();
        await loadFormOptions();
        loadProjects();

        document.getElementById('project-form').addEventListener('submit', handleFormSubmit);
        document.getElementById('btn-confirm-delete-action').addEventListener('click', executeDeletedProject);
    });

    function initializeQuill() {
        quill = new Quill('#quill-editor', {
            theme: 'snow',
            placeholder: 'Rinci penjelasan projek, peran Anda, teknologi, serta arsitektur yang Anda tawarkan...',
            modules: {
                toolbar: [
                    [{
                        'header': [1, 2, 3, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['link', 'blockquote', 'code-block'],
                    ['clean']
                ]
            }
        });
    }

    async function loadFormOptions() {
        // Categories Option - Filter by 'project' type (#3)
        const resCat = await apiFetch('categories?type=project');
        const selectCat = document.getElementById('project-category');
        if (resCat.success && resCat.data) {
            categoriesOptionData = resCat.data;
            categoriesOptionData.forEach(c => {
                const option = document.createElement('option');
                option.value = c.id;
                option.textContent = c.name;
                selectCat.appendChild(option);
            });
        }

        // Tags Option - Filter by 'project' type (#3)
        const resTag = await apiFetch('tags?type=project');
        const tagsContainer = document.getElementById('project-tags-container');
        if (resTag.success && resTag.data) {
            tagsOptionData = resTag.data;
            tagsContainer.innerHTML = '';
            tagsOptionData.forEach(t => {
                const lbl = document.createElement('label');
                lbl.style.display = 'inline-flex';
                lbl.style.alignItems = 'center';
                lbl.style.gap = '6px';
                lbl.style.fontSize = '12px';
                lbl.style.cursor = 'pointer';
                lbl.style.userSelect = 'none';
                lbl.innerHTML =
                    `<input type="checkbox" name="tags" value="${t.id}" style="accent-color: var(--accent-color);"> ${t.name}`;
                tagsContainer.appendChild(lbl);
            });
        }
    }

    async function loadProjects() {
        const res = await apiFetch('projects');
        const tbody = document.getElementById('project-table-body');

        if (res.success && res.data) {
            projectsData = res.data;
            tbody.innerHTML = '';

            if (projectsData.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-secondary);">
                        Belum ada karya projek portofolio yang dilampirkan.
                    </td>
                </tr>
            `;
                return;
            }

            projectsData.forEach(p => {
                let tagsHtml = '';
                if (p.tags && p.tags.length > 0) {
                    p.tags.forEach(t => {
                        tagsHtml +=
                            `<span class="badge" style="background-color:#1e2430; color:#fff; border: 1px solid var(--border-color); margin-right:4px; font-size:9px; margin-bottom:4px;">${t.name}</span>`;
                    });
                } else {
                    tagsHtml = '<span style="color:var(--text-secondary); font-size:11px;">-</span>';
                }

                const imgUrl = p.image ? `../${p.image}` :
                    'https://placehold.co/120x90/181a22/798093?text=NO+IMAGE';
                const statusBadge = p.status === 'published' ?
                    '<span class="badge badge-success">Published</span>' :
                    '<span class="badge badge-draft">Draft</span>';
                const relativeUrl = p.project_url ?
                    `<a href="${p.project_url}" target="_blank" style="color:var(--accent-color); font-size:16px;" title="Lihat Projek"><i class="ri-external-link-line"></i></a>` :
                    '<span style="color:var(--text-secondary);">-</span>';

                tbody.innerHTML += `
                <tr>
                    <td>
                        <img src="${imgUrl}" style="width: 54px; height: 38px; border-radius: 4px; object-fit: cover; border: 1px solid var(--border-color);">
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #fff; font-size: 14px;">${p.title}</div>
                        <div style="font-size:11px; color:var(--text-secondary); margin-top:2px;">URL Slug: <code style="color:var(--accent-color);">${p.slug}</code></div>
                    </td>
                    <td style="color: var(--text-secondary); font-weight: 500;">${p.category_name || '-'}</td>
                    <td>
                        <div style="display:flex; flex-wrap:wrap; max-width:200px;">
                            ${tagsHtml}
                        </div>
                    </td>
                    <td style="text-align:center;">${relativeUrl}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon edit" onclick="openEditModal('${p.id}')">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button class="btn-icon delete" onclick="deleteProject('${p.id}')">
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
                <td colspan="7" style="text-align: center; color: var(--danger-color);">
                    Gagal memuat projek: ${res.message}
                </td>
            </tr>
        `;
        }
    }

    function openAddModal() {
        document.getElementById('modal-title').textContent = 'Tambah Project Baru';
        document.getElementById('project-id').value = '';
        document.getElementById('project-title').value = '';
        document.getElementById('project-category').value = '';
        document.getElementById('project-status').value = 'draft';
        document.getElementById('project-url').value = '';
        if (quill) {
            quill.setContents([]);
        }
        document.getElementById('project-description').value = '';
        document.getElementById('project-image').value = '';
        document.getElementById('image-preview-container').style.display = 'none';

        // Uncheck all tags (#6)
        const checkboxes = document.querySelectorAll('#project-tags-container input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);

        openModal('project-modal');
    }

    function openEditModal(id) {
        const p = projectsData.find(item => item.id === id);
        if (!p) return;

        document.getElementById('modal-title').textContent = 'Ubah Deskripsi Projek';
        document.getElementById('project-id').value = p.id;
        document.getElementById('project-title').value = p.title;
        document.getElementById('project-category').value = p.category_id || '';
        document.getElementById('project-status').value = p.status;
        document.getElementById('project-url').value = p.project_url || '';

        // Load HTML into WYSIWYG Editor (#12)
        if (quill) {
            quill.clipboard.dangerouslyPasteHTML(p.description || '');
        }
        document.getElementById('project-description').value = p.description || '';
        document.getElementById('project-image').value = '';

        // Show current project screen banner if any
        const previewContainer = document.getElementById('image-preview-container');
        const previewImg = document.getElementById('image-preview');
        if (p.image) {
            previewImg.src = `../${p.image}`;
            previewContainer.style.display = 'block';
        } else {
            previewContainer.style.display = 'none';
        }

        // Check matching tag boxes (#6)
        const projectTagIds = p.tags ? p.tags.map(t => t.id) : [];
        const checkboxes = document.querySelectorAll('#project-tags-container input[type="checkbox"]');

        checkboxes.forEach(cb => {
            cb.checked = projectTagIds.includes(cb.value);
        });

        openModal('project-modal');
    }

    async function handleFormSubmit(e) {
        e.preventDefault();

        const id = document.getElementById('project-id').value;
        const btnSubmit = document.getElementById('btn-submit');

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Menyimpan...';

        // Fetch raw HTML from Quill WYSIWYG editor (#12)
        const descriptionHtml = quill.getSemanticHTML();

        // Creating form-data payload in order to safely post file upload
        const formData = new FormData();
        formData.append('title', document.getElementById('project-title').value.trim());
        formData.append('category_id', document.getElementById('project-category').value);
        formData.append('status', document.getElementById('project-status').value);
        formData.append('project_url', document.getElementById('project-url').value.trim());
        formData.append('description', descriptionHtml);

        // Pick file
        const fileInput = document.getElementById('project-image');
        if (fileInput.files.length > 0) {
            // Take first file as main banner
            formData.append('image', fileInput.files[0]);
        }

        // Pick multi-select tags values
        const checkedBoxes = document.querySelectorAll('#project-tags-container input[type="checkbox"]:checked');
        const selectedTags = Array.from(checkedBoxes).map(cb => cb.value);
        formData.append('tags', selectedTags.join(','));

        let url = 'projects';
        if (id) {
            // Since PHP does not read multipart file upload easily on PUT requests natively,
            // we'll send POST with appending the ID on the url to execute our router update bypass
            url = `projects/${id}`;
        }

        const res = await apiFetch(url, {
            method: 'POST',
            body: formData
        });

        btnSubmit.disabled = false;
        btnSubmit.textContent = 'Simpan Projek';

        if (res.success) {
            closeModal('project-modal');
            loadProjects();
        } else {
            alert('Gagal memposting projek portofolio: ' + res.message);
        }
    }

    function deleteProject(id) {
        targetDeleteId = id;
        document.getElementById('delete-confirm-text').textContent =
            'Apakah Anda yakin ingin menghapus data karya projek portofolio ini secara permanen?';
        openModal('delete-confirm-modal');
    }

    async function executeDeletedProject() {
        if (!targetDeleteId) return;
        const btn = document.getElementById('btn-confirm-delete-action');
        btn.disabled = true;
        btn.textContent = 'Menghapus...';

        const res = await apiFetch(`projects/${targetDeleteId}`, {
            method: 'DELETE'
        });

        btn.disabled = false;
        btn.textContent = 'Hapus';
        closeModal('delete-confirm-modal');

        if (res.success) {
            loadProjects();
        } else {
            alert('Gagal menghapus projek portofolio: ' + res.message);
        }
        targetDeleteId = null;
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>