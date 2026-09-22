// Common helper functions for Admin UI and API communication
const API_URL = '/api';

async function apiFetch(endpoint, options = {}) {
    // Default headers
    const defaultHeaders = {};

    if (!(options.body instanceof FormData)) {
        defaultHeaders['Content-Type'] = 'application/json';
    }

    if (options.headers) {
        options.headers = { ...defaultHeaders, ...options.headers };
    } else {
        options.headers = defaultHeaders;
    }

    // Set credentials to include HttpOnly cookies
    options.credentials = 'include';

    try {
        const response = await fetch(`${API_URL}/${endpoint}`, options);
        const result = await response.json();

        if (response.status === 401) {
            // Unauthorized - redirect to login page if we are in admin UI
            if (!window.location.pathname.endsWith('login.php')) {
                window.location.href = '/admin/login.php';
            }
        }

        return result;
    } catch (e) {
        console.error('Fetch error:', e);
        return { success: false, message: 'Masalah jaringan atau rute tidak ditemukan.' };
    }
}

// Global modal helpers
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Generate dynamic initials for profile
function getInitials(name) {
    if (!name) return 'A';
    const parts = name.split(' ');
    let initials = parts[0].charAt(0);
    if (parts.length > 1) {
        initials += parts[parts.length - 1].charAt(0);
    }
    return initials.toUpperCase();
}
