<?php
require 'config.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT users.*, roles.role_name FROM users
        JOIN roles ON users.role_id=roles.id WHERE users.email=?");
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'], 'name' => $user['name'],
            'email' => $user['email'], 'role' => $user['role_name']
        ];
        if ($user['role_name'] === 'owner') header('Location: admin/index.php');
        elseif ($user['role_name'] === 'kasir') header('Location: view_orders.php');
        else header('Location: index.php');
        exit;
    }
    $message = 'Email atau password salah.';
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Login</title></head><body>
<h1>Login Kopipiro</h1><p><?= e($message) ?></p>
<form method="post">
Email: <input type="email" name="email" required><br><br>
Password: <input type="password" name="password" required><br><br>
<button type="submit">Login</button>
</form><p><a href="register.php">Register customer</a> | <a href="index.php">Kembali</a></p>
</body></html>
