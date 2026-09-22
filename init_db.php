<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use App\Config;

$host = Config::get('DB_HOST', '127.0.0.1');
$port = Config::get('DB_PORT', '3306');
$dbName = Config::get('DB_NAME', 'my_profile_db');
$username = Config::get('DB_USER', 'root');
$password = Config::get('DB_PASS', '');

echo "Mencoba menghubungkan ke MySQL server di {$host}:{$port}...\n";

try {
    // Connect without dbname first to create it
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
    echo "✓ Terhubung ke MySQL server!\n";

    echo "Membuat database `{$dbName}` jika belum ada...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database `{$dbName}` berhasil disiapkan/dibuat!\n";

    // Reconnect with database name
    $pdo->exec("USE `{$dbName}`");

    $schemaFile = __DIR__ . '/sql/schema.sql';
    if (file_exists($schemaFile)) {
        echo "Membaca berkas skema database `sql/schema.sql`...\n";
        $sql = file_get_contents($schemaFile);

        // Remove comments or database creation helper statements if they conflict
        // and run the SQL schema queries
        $pdo->exec($sql);
        echo "✓ Skema database dan data seed berhasil diimpor!\n";
    } else {
        echo "✗ Berkas `sql/schema.sql` tidak ditemukan.\n";
    }
} catch (PDOException $e) {
    echo "✗ Gagal mendirikan basis data: " . $e->getMessage() . "\n";
    echo "Pastikan servis MySQL lokal Anda (XAMPP / Laragon / MAMP) sudah menyala.\n";
}
