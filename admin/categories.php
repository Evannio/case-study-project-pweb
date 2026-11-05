<?php
require '../config.php'; require_role('owner');
$message='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=trim($_POST['name']??''); $description=trim($_POST['description']??'');
    if ($name!=='') {
        try { $s=$pdo->prepare("INSERT INTO categories(name,description) VALUES(?,?)"); $s->execute([$name,$description]); header('Location: categories.php'); exit; }
        catch(PDOException $e) { $message='Kategori sudah ada atau gagal disimpan.'; }
    }
}
$categories=$pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Kategori</title></head><body>
<h1>Kelola Kategori</h1><p><?= e($message) ?></p>
<form method="post">Nama kategori: <input name="name" required> Deskripsi: <input name="description"><button>Tambah</button></form>
<ul><?php foreach($categories as $c): ?><li><?= e($c['name']) ?> - <?= e($c['description']) ?></li><?php endforeach; ?></ul>
<a href="index.php">Kembali</a></body></html>
