<?php
session_start();

$host = 'localhost';
$dbname = 'kopipiro';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal. Periksa pengaturan config.php.");
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ../login.php');
        exit;
    }
}

function require_role($role) {
    require_login();
    if (($_SESSION['user']['role'] ?? '') !== $role) {
        http_response_code(403);
        exit('Akses ditolak.');
    }
}

function rupiah($number) {
    return 'Rp ' . number_format((float)$number, 0, ',', '.');
}

function cart_session_id() {
    if (empty($_SESSION['cart_session_id'])) {
        $_SESSION['cart_session_id'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['cart_session_id'];
}
