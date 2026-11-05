<?php
require '../config.php'; require_role('owner');
$message='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $password=$_POST['password']??'';
    if ($name && filter_var($email,FILTER_VALIDATE_EMAIL) && strlen($password)>=6) {
        try {
            $s=$pdo->prepare("INSERT INTO users(role_id,name,email,password) VALUES((SELECT id FROM roles WHERE role_name='kasir'),?,?,?)");
            $s->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
            $message='Akun kasir berhasil dibuat.';
        } catch(PDOException $e) { $message='Email sudah digunakan.'; }
    } else $message='Periksa nama, email, dan password minimal 6 karakter.';
}
$cashiers=$pdo->query("SELECT id,name,email FROM users WHERE role_id=(SELECT id FROM roles WHERE role_name='kasir')")->fetchAll();
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Kelola Kasir</title></head><body>
<h1>Tambah Akun Kasir</h1><p><?= e($message) ?></p>
<form method="post">Nama: <input name="name" required><br><br>Email: <input type="email" name="email" required><br><br>Password: <input type="password" name="password" minlength="6" required><br><br><button>Buat Kasir</button></form>
<h2>Daftar Kasir</h2><ul><?php foreach($cashiers as $c): ?><li><?= e($c['name']) ?> (<?= e($c['email']) ?>), ID <?= $c['id'] ?></li><?php endforeach; ?></ul>
<h2>Tugaskan Pesanan</h2>
<?php
$orders=$pdo->query("SELECT id,customer_name,cashier_id FROM orders ORDER BY id DESC")->fetchAll();
?>
<table border="1" cellpadding="5"><tr><th>Order</th><th>Pemesan</th><th>Kasir</th><th>Aksi</th></tr>
<?php foreach($orders as $o): ?><tr><form method="post" action="assign_cashier.php"><td><?= $o['id'] ?><input type="hidden" name="order_id" value="<?= $o['id'] ?>"></td><td><?= e($o['customer_name']) ?></td><td>
<select name="cashier_id"><option value="">Belum ditugaskan</option><?php foreach($cashiers as $c): ?><option value="<?= $c['id'] ?>" <?= $c['id']==$o['cashier_id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
</td><td><button>Tugaskan</button></td></form></tr><?php endforeach; ?></table>
<p><a href="index.php">Kembali</a></p></body></html>
