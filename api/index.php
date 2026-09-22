<?php

require_once __DIR__ . '/../app/bootstrap.php';

use App\Auth\AuthMiddleware;
use App\Auth\JwtService;
use App\Helpers\Slug;
use App\Helpers\Uuid;
use App\Helpers\ImageUploader;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Blog;
use App\Models\Project;

// Set up clean URL routing via REQUEST_URI
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/api/';

// Remove query parameters
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];

// Check prefix
if (strpos($path, $basePath) !== 0) {
    apiResponse(404, false, "Not Found. Invalid Route Prefix.");
}

$route = substr($path, strlen($basePath));
$routeParts = explode('/', filter_var(rtrim($route, '/'), FILTER_SANITIZE_URL));
$method = $_SERVER['REQUEST_METHOD'];

// Helper to check JSON input
function getJsonInput(): array
{
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    return is_array($data) ? $data : [];
}

// -------------------------------------------------------------
// ROUTES DISPATCHER
// -------------------------------------------------------------

switch ($routeParts[0]) {
    case 'auth':
        handleAuth($routeParts, $method);
        break;

    case 'dashboard':
        handleDashboard($method);
        break;

    case 'categories':
        handleCategories($routeParts, $method);
        break;

    case 'tags':
        handleTags($routeParts, $method);
        break;

    case 'blogs':
        handleBlogs($routeParts, $method);
        break;

    case 'projects':
        handleProjects($routeParts, $method);
        break;

    default:
        apiResponse(404, false, "API Route '/api/" . implode('/', $routeParts) . "' tidak ditemukan.");
}

// -------------------------------------------------------------
// CONTROLLERS IMPLEMENTATION
// -------------------------------------------------------------

function handleAuth(array $parts, string $method): void
{
    $subAction = $parts[1] ?? '';

    if ($subAction === 'login' && $method === 'POST') {
        $input = getJsonInput();
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            apiResponse(400, false, "Email dan password wajib diisi.");
        }

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            apiResponse(401, false, "Email atau password salah.");
        }

        // Generate JWT
        $payload = [
            'uid' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
        $token = JwtService::encode($payload);

        // Set JWT HTTP-Only Cookie
        $expiryTime = time() + 86400; // 24 hours
        setcookie('admin_token', $token, [
            'expires' => $expiryTime,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        apiResponse(200, true, "Login berhasil.", [
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone']
            ]
        ]);
    }

    if ($subAction === 'logout' && $method === 'POST') {
        // Clear Cookie
        setcookie('admin_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        apiResponse(200, true, "Logout berhasil.");
    }

    if ($subAction === 'me') {
        $userData = AuthMiddleware::enforceAPI();
        $user = User::find($userData['uid']);
        if (!$user) {
            apiResponse(404, false, "User tidak ditemukan.");
        }

        if ($method === 'GET') {
            apiResponse(200, true, "User terautentikasi.", [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone']
            ]);
        }

        if ($method === 'PUT') {
            $input = getJsonInput();
            $name = trim($input['name'] ?? '');
            $phone = trim($input['phone'] ?? '');
            $password = $input['password'] ?? null;

            if (empty($name)) {
                apiResponse(400, false, "Nama tidak boleh kosong.");
            }

            try {
                User::updateProfile($user['id'], $name, $phone, $password);
                apiResponse(200, true, "Profil berhasil diperbarui.");
            } catch (Exception $e) {
                apiResponse(500, false, "Gagal memperbarui profil: " . $e->getMessage());
            }
        }
    }

    apiResponse(404, false, "Endpoint auth tidak didukung.");
}

function handleDashboard(string $method): void
{
    AuthMiddleware::enforceAPI();

    if ($method !== 'GET') {
        apiResponse(405, false, "Method tidak didukung.");
    }

    try {
        $db = \App\Database::connect();
        $categoriesCount = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        $tagsCount = $db->query("SELECT COUNT(*) FROM tags")->fetchColumn();
        $blogsCount = $db->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
        $projectsCount = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();

        apiResponse(200, true, "Statistik berhasil dimuat.", [
            'categories' => $categoriesCount,
            'tags' => $tagsCount,
            'blogs' => $blogsCount,
            'projects' => $projectsCount
        ]);
    } catch (Exception $e) {
        apiResponse(500, false, "Gagal mendapatkan statistik: " . $e->getMessage());
    }
}

function handleCategories(array $parts, string $method): void
{
    AuthMiddleware::enforceAPI();
    $id = $parts[1] ?? null;
    $type = $_GET['type'] ?? null;

    if ($method === 'GET') {
        if ($id) {
            $cat = Category::find($id);
            if (!$cat) apiResponse(404, false, "Category tidak ditemukan.");
            apiResponse(200, true, "Category ditemukan.", $cat);
        } elseif ($type) {
            apiResponse(200, true, "Daftar Category berhasil dimuat.", Category::allByType($type));
        } else {
            apiResponse(200, true, "Daftar Category berhasil dimuat.", Category::all());
        }
    }

    if ($method === 'POST') {
        $input = getJsonInput();
        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $catType = trim($input['type'] ?? 'blog');

        if (empty($name)) {
            apiResponse(400, false, "Nama Category wajib diisi.");
        }

        $newId = Uuid::v4();
        $slug = Slug::unique('categories', $name);

        try {
            Category::create($newId, $name, $slug, $description, $catType);
            apiResponse(201, true, "Category baru berhasil dibuat.", ['id' => $newId, 'slug' => $slug]);
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal menyimpan Category: " . $e->getMessage());
        }
    }

    if ($method === 'PUT') {
        if (!$id) apiResponse(400, false, "ID Category wajib dilampirkan.");
        $input = getJsonInput();
        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $catType = trim($input['type'] ?? 'blog');

        if (empty($name)) apiResponse(400, false, "Nama Category wajib diisi.");

        $cat = Category::find($id);
        if (!$cat) apiResponse(404, false, "Category tidak ditemukan.");

        $slug = Slug::unique('categories', $name, $id);

        try {
            Category::update($id, $name, $slug, $description, $catType);
            apiResponse(200, true, "Category berhasil diperbarui.");
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal memperbarui Category: " . $e->getMessage());
        }
    }

    if ($method === 'DELETE') {
        if (!$id) apiResponse(400, false, "ID Category wajib dilampirkan.");
        $cat = Category::find($id);
        if (!$cat) apiResponse(404, false, "Category tidak ditemukan.");

        try {
            Category::delete($id);
            apiResponse(200, true, "Category berhasil dihapus.");
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal menghapus Category: " . $e->getMessage());
        }
    }
}

function handleTags(array $parts, string $method): void
{
    AuthMiddleware::enforceAPI();
    $id = $parts[1] ?? null;
    $type = $_GET['type'] ?? null;

    if ($method === 'GET') {
        if ($id) {
            $tag = Tag::find($id);
            if (!$tag) apiResponse(404, false, "Tag tidak ditemukan.");
            apiResponse(200, true, "Tag ditemukan.", $tag);
        } elseif ($type) {
            apiResponse(200, true, "Daftar Tag berhasil dimuat.", Tag::allByType($type));
        } else {
            apiResponse(200, true, "Daftar Tag berhasil dimuat.", Tag::all());
        }
    }

    if ($method === 'POST') {
        $input = getJsonInput();
        $name = trim($input['name'] ?? '');
        $tagType = trim($input['type'] ?? 'blog');

        if (empty($name)) apiResponse(400, false, "Nama Tag wajib diisi.");

        $newId = Uuid::v4();
        $slug = Slug::unique('tags', $name);

        try {
            Tag::create($newId, $name, $slug, $tagType);
            apiResponse(201, true, "Tag baru berhasil dibuat.", ['id' => $newId, 'slug' => $slug]);
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal menyimpan Tag: " . $e->getMessage());
        }
    }

    if ($method === 'PUT') {
        if (!$id) apiResponse(400, false, "ID Tag wajib dilampirkan.");
        $input = getJsonInput();
        $name = trim($input['name'] ?? '');
        $tagType = trim($input['type'] ?? 'blog');

        if (empty($name)) apiResponse(400, false, "Nama Tag wajib diisi.");

        $tag = Tag::find($id);
        if (!$tag) apiResponse(404, false, "Tag tidak ditemukan.");

        $slug = Slug::unique('tags', $name, $id);

        try {
            Tag::update($id, $name, $slug, $tagType);
            apiResponse(200, true, "Tag berhasil diperbarui.");
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal memperbarui Tag: " . $e->getMessage());
        }
    }

    if ($method === 'DELETE') {
        if (!$id) apiResponse(400, false, "ID Tag wajib dilampirkan.");
        $tag = Tag::find($id);
        if (!$tag) apiResponse(404, false, "Tag tidak ditemukan.");

        try {
            Tag::delete($id);
            apiResponse(200, true, "Tag berhasil dihapus.");
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal menghapus Tag: " . $e->getMessage());
        }
    }
}

function handleBlogs(array $parts, string $method): void
{
    AuthMiddleware::enforceAPI();
    $id = $parts[1] ?? null;

    if ($method === 'GET') {
        if ($id) {
            $blog = Blog::findWithRelations($id);
            if (!$blog) apiResponse(404, false, "Blog tidak ditemukan.");
            apiResponse(200, true, "Blog ditemukan.", $blog);
        } else {
            apiResponse(200, true, "Daftar Blog berhasil dimuat.", Blog::allWithRelations());
        }
    }

    if ($method === 'POST') {
        // Multi-part content or application/json?
        // Typically image uploads require multi-part form data in PHP
        $isMultipart = (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false);

        if ($isMultipart) {
            $title = trim($_POST['title'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $category_id = trim($_POST['category_id'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $tagIds = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];
        } else {
            $input = getJsonInput();
            $title = trim($input['title'] ?? '');
            $excerpt = trim($input['excerpt'] ?? '');
            $content = trim($input['content'] ?? '');
            $category_id = trim($input['category_id'] ?? '');
            $status = trim($input['status'] ?? 'draft');
            $tagIds = $input['tags'] ?? [];
        }

        if (empty($title)) apiResponse(400, false, "Judul Blog wajib diisi.");

        // Upload featured image if provided
        $featuredImage = null;
        if (isset($_FILES['featured_image'])) {
            try {
                $featuredImage = ImageUploader::upload($_FILES['featured_image']);
            } catch (Exception $e) {
                apiResponse(400, false, $e->getMessage());
            }
        }

        $newId = Uuid::v4();
        $slug = Slug::unique('blogs', $title);

        $data = [
            'id' => $newId,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'featured_image' => $featuredImage,
            'category_id' => $category_id,
            'status' => $status
        ];

        try {
            Blog::create($data, $tagIds);
            apiResponse(201, true, "Blog baru berhasil dibuat.", ['id' => $newId, 'slug' => $slug]);
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal mengunggah Blog: " . $e->getMessage());
        }
    }

    if ($method === 'POST' && $id) {
        // Method spoofing or POST redirection since raw PUT does not read multipart file upload in PHP naturally
        // So we route PUT requests with files here by sending as POST with direct route ID
        $blog = Blog::find($id);
        if (!$blog) apiResponse(404, false, "Blog tidak ditemukan.");

        $title = trim($_POST['title'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category_id = trim($_POST['category_id'] ?? '');
        $status = trim($_POST['status'] ?? 'draft');
        $tagIds = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];

        if (empty($title)) apiResponse(400, false, "Judul Blog wajib diisi.");

        $slug = Slug::unique('blogs', $title, $id);
        $data = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'category_id' => $category_id,
            'status' => $status
        ];

        if (isset($_FILES['featured_image'])) {
            try {
                $featuredImage = ImageUploader::upload($_FILES['featured_image']);
                if ($featuredImage) {
                    $data['featured_image'] = $featuredImage;
                }
            } catch (Exception $e) {
                apiResponse(400, false, $e->getMessage());
            }
        }

        try {
            Blog::update($id, $data, $tagIds);
            apiResponse(200, true, "Blog berhasil diperbarui.");
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal memperbarui Blog: " . $e->getMessage());
        }
    }

    if ($method === 'PUT') {
        if (!$id) apiResponse(400, false, "ID Blog wajib dilampirkan.");
        $blog = Blog::find($id);
        if (!$blog) apiResponse(404, false, "Blog tidak ditemukan.");

        $input = getJsonInput();
        $title = trim($input['title'] ?? '');
        $excerpt = trim($input['excerpt'] ?? '');
        $content = trim($input['content'] ?? '');
        $category_id = trim($input['category_id'] ?? '');
        $status = trim($input['status'] ?? 'draft');
        $tagIds = $input['tags'] ?? [];

        if (empty($title)) apiResponse(400, false, "Judul Blog wajib diisi.");

        $slug = Slug::unique('blogs', $title, $id);
        $data = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'category_id' => $category_id,
            'status' => $status
        ];

        try {
            Blog::update($id, $data, $tagIds);
            apiResponse(200, true, "Blog berhasil diperbarui.");
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal memperbarui Blog: " . $e->getMessage());
        }
    }

    if ($method === 'DELETE') {
        if (!$id) apiResponse(400, false, "ID Blog wajib dilampirkan.");
        $blog = Blog::find($id);
        if (!$blog) apiResponse(404, false, "Blog tidak ditemukan.");

        try {
            // Unlink actual image if desired, or skip to keep it safe
            Blog::delete($id);
            apiResponse(200, true, "Blog berhasil dihapus.");
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal menghapus Blog: " . $e->getMessage());
        }
    }
}

function handleProjects(array $parts, string $method): void
{
    AuthMiddleware::enforceAPI();
    $id = $parts[1] ?? null;

    if ($method === 'GET') {
        if ($id) {
            $project = Project::findWithRelations($id);
            if (!$project) apiResponse(404, false, "Project tidak ditemukan.");
            apiResponse(200, true, "Project ditemukan.", $project);
        } else {
            apiResponse(200, true, "Daftar Project berhasil dimuat.", Project::allWithRelations());
        }
    }

    if ($method === 'POST') {
        $isMultipart = (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false);

        if ($isMultipart) {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $project_url = trim($_POST['project_url'] ?? '');
            $category_id = trim($_POST['category_id'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            $tagIds = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];
        } else {
            $input = getJsonInput();
            $title = trim($input['title'] ?? '');
            $description = trim($input['description'] ?? '');
            $project_url = trim($input['project_url'] ?? '');
            $category_id = trim($input['category_id'] ?? '');
            $status = trim($input['status'] ?? 'draft');
            $tagIds = $input['tags'] ?? [];
        }

        if (empty($title)) apiResponse(400, false, "Judul Project wajib diisi.");

        $image = null;
        if (isset($_FILES['image'])) {
            try {
                $image = ImageUploader::upload($_FILES['image']);
            } catch (Exception $e) {
                apiResponse(400, false, $e->getMessage());
            }
        }

        $newId = Uuid::v4();
        $slug = Slug::unique('projects', $title);

        $data = [
            'id' => $newId,
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'image' => $image,
            'project_url' => $project_url,
            'category_id' => $category_id,
            'status' => $status
        ];

        try {
            Project::create($data, $tagIds);
            apiResponse(201, true, "Project baru berhasil dibuat.", ['id' => $newId, 'slug' => $slug]);
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal membuat Project baru: " . $e->getMessage());
        }
    }

    if ($method === 'POST' && $id) {
        $project = Project::find($id);
        if (!$project) apiResponse(404, false, "Project tidak ditemukan.");

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $project_url = trim($_POST['project_url'] ?? '');
        $category_id = trim($_POST['category_id'] ?? '');
        $status = trim($_POST['status'] ?? 'draft');
        $tagIds = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];

        if (empty($title)) apiResponse(400, false, "Judul Project wajib diisi.");

        $slug = Slug::unique('projects', $title, $id);
        $data = [
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'project_url' => $project_url,
            'category_id' => $category_id,
            'status' => $status
        ];

        if (isset($_FILES['image'])) {
            try {
                $image = ImageUploader::upload($_FILES['image']);
                if ($image) {
                    $data['image'] = $image;
                }
            } catch (Exception $e) {
                apiResponse(400, false, $e->getMessage());
            }
        }

        try {
            Project::update($id, $data, $tagIds);
            apiResponse(200, true, "Project berhasil diperbarui.");
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal memperbarui Project: " . $e->getMessage());
        }
    }

    if ($method === 'PUT') {
        if (!$id) apiResponse(400, false, "ID Project wajib dilampirkan.");
        $project = Project::find($id);
        if (!$project) apiResponse(404, false, "Project tidak ditemukan.");

        $input = getJsonInput();
        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $project_url = trim($input['project_url'] ?? '');
        $category_id = trim($input['category_id'] ?? '');
        $status = trim($input['status'] ?? 'draft');
        $tagIds = $input['tags'] ?? [];

        if (empty($title)) apiResponse(400, false, "Judul Project wajib diisi.");

        $slug = Slug::unique('projects', $title, $id);
        $data = [
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'project_url' => $project_url,
            'category_id' => $category_id,
            'status' => $status
        ];

        try {
            Project::update($id, $data, $tagIds);
            apiResponse(200, true, "Project berhasil diperbarui.");
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal memperbarui Project: " . $e->getMessage());
        }
    }

    if ($method === 'DELETE') {
        if (!$id) apiResponse(400, false, "ID Project wajib dilampirkan.");
        $project = Project::find($id);
        if (!$project) apiResponse(404, false, "Project tidak ditemukan.");

        try {
            Project::delete($id);
            apiResponse(200, true, "Project berhasil dihapus.");
        } catch (Exception $e) {
            apiResponse(500, false, "Gagal menghapus Project: " . $e->getMessage());
        }
    }
}
