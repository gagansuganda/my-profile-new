<?php
require_once __DIR__ . '/../../app/bootstrap.php';

use App\Auth\AuthMiddleware;

$currentUser = AuthMiddleware::check();
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/admin/index.php" class="sidebar-brand">
            <i class="ri-braces-line"></i> MyProject
        </a>
    </div>

    <!-- Profile Info placed at the top replacing Balance Widget as requested -->
    <div class="balance-widget" style="background: linear-gradient(135deg, rgba(28, 166, 185, 0.08) 0%, rgba(15, 53, 61, 0.3) 100%);">
        <div class="user-profile-badge" style="margin-bottom: 0;">
            <div class="user-avatar-initials" id="sidebar-initials-top" style="border-color: var(--accent-color);">
                GS
            </div>
            <div class="user-info">
                <div class="user-name" id="sidebar-name-top" style="font-size: 13px; font-weight: 700; color: #fff;">Gagan Suganda</div>
                <div class="user-email" id="sidebar-email-top" style="font-size: 11px; color: var(--text-secondary);">gagans.id</div>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Utama</div>
        <a href="index.php" class="nav-link">
            <div class="nav-link-left">
                <i class="ri-dashboard-line"></i>
                <span>Dashboard</span>
            </div>
        </a>
        <a href="categories.php" class="nav-link">
            <div class="nav-link-left">
                <i class="ri-folder-open-line"></i>
                <span>Category</span>
            </div>
        </a>
        <a href="tags.php" class="nav-link">
            <div class="nav-link-left">
                <i class="ri-price-tag-3-line"></i>
                <span>Tag</span>
            </div>
        </a>

        <div class="nav-label">Riwayat & Statistik</div>
        <a href="blogs.php" class="nav-link">
            <div class="nav-link-left">
                <i class="ri-article-line"></i>
                <span>Blog & Content</span>
            </div>
        </a>
        <a href="projects.php" class="nav-link">
            <div class="nav-link-left">
                <i class="ri-code-box-line"></i>
                <span>Project</span>
            </div>
        </a>

        <div class="nav-label">Lainnya</div>
        <a href="profile.php" class="nav-link">
            <div class="nav-link-left">
                <i class="ri-settings-4-line"></i>
                <span>Pengaturan</span>
            </div>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="#" onclick="handleLogout(); return false;" class="logout-link" style="display: flex; align-items: center; justify-content: center; width: 100%; border: 1px solid rgba(217, 63, 84, 0.2); padding: 8px; border-radius: 6px; background: rgba(217, 63, 84, 0.05);">
            <i class="ri-logout-box-r-line"></i> Keluar Admin
        </a>
    </div>
</aside>