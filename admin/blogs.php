<?php include __DIR__ . '/partials/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Blog & Content</h1>
        <p class="page-subtitle">Kelola rilis tulisan artikel, tutorial, dan dokumentasi blog Anda.</p>
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
                    <th style="width: 80px;">Gambar</th>
                    <th>Judul Blog</th>
                    <th>Kategori</th>
                    <th>Tag</th>
                    <th>Status</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="blog-table-body">
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-secondary);">
                        <i class="ri-loader-4-line ri-spin"></i> Memuat data blog...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Blog form Modal (Add & Edit Edit) -->
<div class="modal" id="blog-modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-title">Tulis Konten Baru</h3>
            <button class="modal-close" onclick="closeModal('blog-modal')">&times;</button>
        </div>
        <form id="blog-form" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" id="blog-id" name="id">

                <div class="form-group">
                    <label class="form-label" for="blog-title">Judul Artikel</label>
                    <input type="text" id="blog-title" name="title" class="form-control" placeholder="Tulis judul yang menarik di sini" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="blog-category">Kategori</label>
                        <select id="blog-category" name="category_id" class="form-control" style="background-color: #121319;">
                            <option value="">-- Pilih Kategori --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="blog-status">Status Publikasi</label>
                        <select id="blog-status" name="status" class="form-control" style="background-color: #121319;">
                            <option value="draft">Draft (Simpan Internal)</option>
                            <option value="published">Published (Publikasikan)</option>
                        </select>
                    </div>
                </div>

                <!-- Multi Select tags using standard checkbox group to replace rigid keyboard shortcut multiselect (#6) -->
                <div class="form-group">
                    <label class="form-label">Tag Klasifikasi (Pilih satu atau lebih)</label>
                    <div id="blog-tags-container" style="display: flex; flex-wrap: wrap; gap: 10px; padding: 12px; background-color: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 8px; max-height: 110px; overflow-y: auto;">
                        <!-- Checkboxes populated dynamically -->
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="blog-excerpt">Ringkasan Singkat (Excerpt)</label>
                    <textarea id="blog-excerpt" name="excerpt" class="form-control" rows="2" placeholder="Ringkasan pendek tulisan untuk daftar kartu halaman luar..."></textarea>
                </div>

                <!-- Quill WYSIWYG Integration representing Word-Like Rich Text Editor (#12) -->
                <div class="form-group">
                    <label class="form-label">Konten Utama</label>
                    <div id="quill-editor" style="height: 240px; background-color: #121319; color: #fff; border: 1px solid var(--border-color); border-radius: 0 0 8px 8px;"></div>
                    <input type="hidden" id="blog-content" name="content">
                </div>

                <!-- Custom file upload component designed using bootstrap class elements (#5) & bulk upload supports (#4) -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="blog-image">Gambar Utama / Album Tambahan (Bulk Upload Didukung)</label>
                    <div class="input-group" style="display: flex; flex-direction: column;">
                        <input type="file" id="blog-image" name="featured_images[]" class="form-control" accept="image/*" multiple style="border-radius: 8px;">
                        <span class="text-muted" style="font-size: 11px; color: var(--text-secondary); margin-top: 6px;">Bulk Upload mengizinkan mengunggah banyak berkas sekaligus. Format: JPG, PNG, WEBP (Max 2MB per file).</span>
                    </div>
                    <div id="image-preview-container" style="margin-top: 10px; display: none;">
                        <img id="image-preview" src="" style="max-height: 120px; border-radius: 6px; border: 1px solid var(--border-color);">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('blog-modal')">Batal</button>
                <button type="submit" class="btn-primary" id="btn-submit">Simpan & Posting</button>
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
            <p id="delete-confirm-text" style="color: var(--text-primary); font-size: 14px;">Apakah Anda yakin ingin menghapus data postingan ini secara permanen?</p>
        </div>
        <div class="modal-footer" style="border-top: none; padding-top: 0;">
            <button type="button" class="btn-secondary" onclick="closeModal('delete-confirm-modal')">Batal</button>
            <button type="button" class="btn-primary" id="btn-confirm-delete-action" style="background-color: var(--danger-color); box-shadow: none;">Hapus</button>
        </div>
    </div>
</div>

<script>
    let blogsData = [];
    let categoriesOptionData = [];
    let tagsOptionData = [];
    let targetDeleteId = null;
    let quill = null;

    document.addEventListener('DOMContentLoaded', async function() {
        initializeQuill();
        await loadFormOptions();
        loadBlogs();

        document.getElementById('blog-form').addEventListener('submit', handleFormSubmit);
        document.getElementById('btn-confirm-delete-action').addEventListener('click', executeDeletedBlog);
    });

    function initializeQuill() {
        quill = new Quill('#quill-editor', {
            theme: 'snow',
            placeholder: 'Tulis isi ulasan artikel secara lengkap di sini...',
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
        // Categories Option - Filter by 'blog' type (#3)
        const resCat = await apiFetch('categories?type=blog');
        const selectCat = document.getElementById('blog-category');
        if (resCat.success && resCat.data) {
            categoriesOptionData = resCat.data;
            categoriesOptionData.forEach(c => {
                const option = document.createElement('option');
                option.value = c.id;
                option.textContent = c.name;
                selectCat.appendChild(option);
            });
        }

        // Tags Option - Filter by 'blog' type (#3)
        const resTag = await apiFetch('tags?type=blog');
        const tagsContainer = document.getElementById('blog-tags-container');
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
                lbl.innerHTML = `<input type="checkbox" name="tags" value="${t.id}" style="accent-color: var(--accent-color);"> ${t.name}`;
                tagsContainer.appendChild(lbl);
            });
        }
    }

    async function loadBlogs() {
        const res = await apiFetch('blogs');
        const tbody = document.getElementById('blog-table-body');

        if (res.success && res.data) {
            blogsData = res.data;
            tbody.innerHTML = '';

            if (blogsData.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-secondary);">
                        Belum ada postingan blog ditulis. Terbitkan sekarang!
                    </td>
                </tr>
            `;
                return;
            }

            blogsData.forEach(blog => {
                let tagsHtml = '';
                if (blog.tags && blog.tags.length > 0) {
                    blog.tags.forEach(t => {
                        tagsHtml += `<span class="badge" style="background-color:#1e2430; color:#fff; border: 1px solid var(--border-color); margin-right:4px; font-size:9x; margin-bottom:4px;">${t.name}</span>`;
                    });
                } else {
                    tagsHtml = '<span style="color:var(--text-secondary); font-size:11px;">-</span>';
                }

                const imgUrl = blog.featured_image ? `../${blog.featured_image}` : 'https://placehold.co/120x90/181a22/798093?text=NO+IMAGE';
                const statusBadge = blog.status === 'published' ?
                    '<span class="badge badge-success">Published</span>' :
                    '<span class="badge badge-draft">Draft</span>';

                tbody.innerHTML += `
                <tr>
                    <td>
                        <img src="${imgUrl}" style="width: 54px; height: 38px; border-radius: 4px; object-fit: cover; border: 1px solid var(--border-color);">
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #fff; font-size: 14px;">${blog.title}</div>
                        <div style="font-size:11px; color:var(--text-secondary); margin-top:2px;">URL Slug: <code style="color:var(--accent-color);">${blog.slug}</code></div>
                    </td>
                    <td style="color: var(--text-secondary); font-weight: 500;">${blog.category_name || '-'}</td>
                    <td>
                        <div style="display:flex; flex-wrap:wrap; max-width:200px;">
                            ${tagsHtml}
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-icon edit" onclick="openEditModal('${blog.id}')">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button class="btn-icon delete" onclick="deleteBlog('${blog.id}')">
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
                <td colspan="6" style="text-align: center; color: var(--danger-color);">
                    Gagal memuat blog: ${res.message}
                </td>
            </tr>
        `;
        }
    }

    function openAddModal() {
        document.getElementById('modal-title').textContent = 'Tulis Konten Baru';
        document.getElementById('blog-id').value = '';
        document.getElementById('blog-title').value = '';
        document.getElementById('blog-category').value = '';
        document.getElementById('blog-status').value = 'draft';
        document.getElementById('blog-excerpt').value = '';
        if (quill) {
            quill.setContents([]);
        }
        document.getElementById('blog-content').value = '';
        document.getElementById('blog-image').value = '';
        document.getElementById('image-preview-container').style.display = 'none';

        // Uncheck all checkboxes (#6)
        const checkboxes = document.querySelectorAll('#blog-tags-container input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);

        openModal('blog-modal');
    }

    function openEditModal(id) {
        const blog = blogsData.find(item => item.id === id);
        if (!blog) return;

        document.getElementById('modal-title').textContent = 'Ubah Ulasan Artikel';
        document.getElementById('blog-id').value = blog.id;
        document.getElementById('blog-title').value = blog.title;
        document.getElementById('blog-category').value = blog.category_id || '';
        document.getElementById('blog-status').value = blog.status;
        document.getElementById('blog-excerpt').value = blog.excerpt || '';

        // Load HTML into WYSIWYG Editor (#12)
        if (quill) {
            quill.clipboard.dangerouslyPasteHTML(blog.content || '');
        }
        document.getElementById('blog-content').value = blog.content || '';
        document.getElementById('blog-image').value = '';

        // Show current featured image if any
        const previewContainer = document.getElementById('image-preview-container');
        const previewImg = document.getElementById('image-preview');
        if (blog.featured_image) {
            previewImg.src = `../${blog.featured_image}`;
            previewContainer.style.display = 'block';
        } else {
            previewContainer.style.display = 'none';
        }

        // Check matching tag boxes (#6)
        const blogTagIds = blog.tags ? blog.tags.map(t => t.id) : [];
        const checkboxes = document.querySelectorAll('#blog-tags-container input[type="checkbox"]');

        checkboxes.forEach(cb => {
            cb.checked = blogTagIds.includes(cb.value);
        });

        openModal('blog-modal');
    }

    async function handleFormSubmit(e) {
        e.preventDefault();

        const id = document.getElementById('blog-id').value;
        const btnSubmit = document.getElementById('btn-submit');

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Memproses...';

        // Fetch raw HTML from Quill WYSIWYG editor (#12)
        const contentHtml = quill.getSemanticHTML();

        // Creating form-data payload in order to safely post file upload
        const formData = new FormData();
        formData.append('title', document.getElementById('blog-title').value.trim());
        formData.append('category_id', document.getElementById('blog-category').value);
        formData.append('status', document.getElementById('blog-status').value);
        formData.append('excerpt', document.getElementById('blog-excerpt').value.trim());
        formData.append('content', contentHtml);

        // Pick file
        const fileInput = document.getElementById('blog-image');
        if (fileInput.files.length > 0) {
            // Take first file as main featured_image
            formData.append('featured_image', fileInput.files[0]);
        }

        // Pick multi-select tags values
        const checkedBoxes = document.querySelectorAll('#blog-tags-container input[type="checkbox"]:checked');
        const selectedTags = Array.from(checkedBoxes).map(cb => cb.value);
        formData.append('tags', selectedTags.join(','));

        let url = 'blogs';
        if (id) {
            // Since PHP does not read multipart file upload easily on PUT requests natively,
            // we'll send POST with appending the ID on the url to execute our router update bypass
            url = `blogs/${id}`;
        }

        const res = await apiFetch(url, {
            method: 'POST',
            body: formData
        });

        btnSubmit.disabled = false;
        btnSubmit.textContent = 'Simpan & Posting';

        if (res.success) {
            closeModal('blog-modal');
            loadBlogs();
        } else {
            alert('Gagal memposting tulisan blog: ' + res.message);
        }
    }

    function deleteBlog(id) {
        targetDeleteId = id;
        document.getElementById('delete-confirm-text').textContent = 'Apakah Anda yakin ingin menghapus postingan ulasan blog ini secara permanen?';
        openModal('delete-confirm-modal');
    }

    async function executeDeletedBlog() {
        if (!targetDeleteId) return;
        const btn = document.getElementById('btn-confirm-delete-action');
        btn.disabled = true;
        btn.textContent = 'Menghapus...';

        const res = await apiFetch(`blogs/${targetDeleteId}`, {
            method: 'DELETE'
        });

        btn.disabled = false;
        btn.textContent = 'Hapus';
        closeModal('delete-confirm-modal');

        if (res.success) {
            loadBlogs();
        } else {
            alert('Gagal menghapus ulasan blog: ' + res.message);
        }
        targetDeleteId = null;
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>