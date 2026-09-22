document.addEventListener('DOMContentLoaded', function () {
    // Highlight Active Sidebar Menu Link
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        // Simple accurate match
        if (currentPath.endsWith(href) || (href === 'index.php' && currentPath.endsWith('/admin/'))) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // Populate Sidebar profile information dynamically if authenticated
    loadSidebarProfile();
});

async function loadSidebarProfile() {
    const elInitialsPr = document.getElementById('sidebar-initials-top');
    const elNamePr = document.getElementById('sidebar-name-top');
    const elEmailPr = document.getElementById('sidebar-email-top');

    if (elInitialsPr && elNamePr && elEmailPr) {
        const res = await apiFetch('auth/me');
        if (res.success && res.data) {
            elNamePr.textContent = res.data.name;
            elEmailPr.textContent = res.data.email;
            elInitialsPr.textContent = getInitials(res.data.name);
        }
    }
}

async function handleLogout() {
    if (confirm('Apakah Anda yakin ingin keluar dari panel admin?')) {
        const res = await apiFetch('auth/logout', { method: 'POST' });
        if (res.success) {
            window.location.href = '/admin/login.php';
        } else {
            alert('Logout gagal: ' + res.message);
        }
    }
}
