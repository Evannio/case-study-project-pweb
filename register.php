<?php
require 'config.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $message = 'Isi nama, email yang valid, dan password minimal 6 karakter.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (role_id, name, email, password)
                VALUES ((SELECT id FROM roles WHERE role_name='customer'), ?, ?, ?)");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $message = 'Registrasi berhasil. Silakan login.';
        } catch (PDOException $e) {
            $message = 'Email sudah digunakan atau registrasi gagal.';
        }
    }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Register</title></head><body>
<h1>Registrasi Customer</h1><p><?= e($message) ?></p>
<form method="post">
Nama: <input name="name" required><br><br>
Email: <input type="email" name="email" required><br><br>
Password: <input type="password" name="password" minlength="6" required><br><br>
<button type="submit">Register</button>
</form><p><a href="login.php">Login</a> | <a href="index.php">Kembali</a></p>
</body></html>
