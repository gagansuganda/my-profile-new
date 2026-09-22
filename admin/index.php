<?php include __DIR__ . '/partials/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang kembali di pusat kendali AI Coding, <span id="welcome-name">Admin</span>!</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <p>Category</p>
            <h3 id="count-categories">0</h3>
        </div>
        <div class="stat-icon">
            <i class="ri-folder-open-line"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <p>Tag</p>
            <h3 id="count-tags">0</h3>
        </div>
        <div class="stat-icon">
            <i class="ri-price-tag-3-line"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <p>Blog & Content</p>
            <h3 id="count-blogs">0</h3>
        </div>
        <div class="stat-icon">
            <i class="ri-article-line"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <p>Project</p>
            <h3 id="count-projects">0</h3>
        </div>
        <div class="stat-icon">
            <i class="ri-code-box-line"></i>
        </div>
    </div>
</div>

<div class="card">
    <h3 class="card-title">Pintasan Cepat</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <a href="categories.php" class="btn-primary" style="text-decoration: none; padding: 16px;">
            <i class="ri-folder-add-line"></i> Kelola Kategori
        </a>
        <a href="tags.php" class="btn-primary" style="text-decoration: none; padding: 16px;">
            <i class="ri-price-tag-3-fill"></i> Kelola Tag
        </a>
        <a href="blogs.php" class="btn-primary" style="text-decoration: none; padding: 16px;">
            <i class="ri-add-line"></i> Buat Blog Baru
        </a>
        <a href="projects.php" class="btn-primary" style="text-decoration: none; padding: 16px;">
            <i class="ri-code-box-fill"></i> Tambah Project Baru
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        // Load Stats
        const resStats = await apiFetch('dashboard');
        if (resStats.success && resStats.data) {
            document.getElementById('count-categories').textContent = resStats.data.categories;
            document.getElementById('count-tags').textContent = resStats.data.tags;
            document.getElementById('count-blogs').textContent = resStats.data.blogs;
            document.getElementById('count-projects').textContent = resStats.data.projects;
        }

        // Load User Greeting
        const resUser = await apiFetch('auth/me');
        if (resUser.success && resUser.data) {
            document.getElementById('welcome-name').textContent = resUser.data.name;
        }
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>