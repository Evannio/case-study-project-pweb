<?php
require 'config.php';
$count=(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role_id=(SELECT id FROM roles WHERE role_name='owner')")->fetchColumn();
if ($count>0) exit('Akun owner sudah tersedia. Hapus file setup_owner.php setelah selesai.');
$message='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $password=$_POST['password']??'';
    if ($name && filter_var($email,FILTER_VALIDATE_EMAIL) && strlen($password)>=8) {
        $stmt=$pdo->prepare("INSERT INTO users(role_id,name,email,password) VALUES((SELECT id FROM roles WHERE role_name='owner'),?,?,?)");
        $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
        $message='Owner berhasil dibuat. Hapus file setup_owner.php sekarang, lalu login.';
    } else $message='Isi data dengan benar. Password minimal 8 karakter.';
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Setup Owner</title></head><body>
<h1>Buat Akun Owner Pertama</h1><p><?= e($message) ?></p>
<form method="post">Nama: <input name="name" required><br><br>Email: <input type="email" name="email" required><br><br>Password: <input type="password" name="password" minlength="8" required><br><br><button>Buat Owner</button></form>
</body></html>
