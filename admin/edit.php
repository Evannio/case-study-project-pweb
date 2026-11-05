<?php
require '../config.php'; require_role('owner');
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?"); $stmt->execute([$id]); $product = $stmt->fetch();
if (!$product) exit('Produk tidak ditemukan.');
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageName = $product['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext,['jpg','jpeg','png','webp'],true) || $_FILES['image']['size'] > 2*1024*1024) $message='Format gambar tidak valid atau ukuran lebih dari 2 MB.';
        else {
            $imageName = uniqid('produk_',true).'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'],'../uploads/'.$imageName);
        }
    }
    if ($message === '') {
        $update = $pdo->prepare("UPDATE products SET category_id=?,name=?,description=?,price=?,image=?,rating=?,review_count=? WHERE id=?");
        $update->execute([(int)$_POST['category_id'],trim($_POST['name']),trim($_POST['description']),
            (float)$_POST['price'],$imageName,(float)$_POST['rating'],(int)$_POST['review_count'],$id]);
        header('Location: index.php?products=1'); exit;
    }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Edit Produk</title></head><body>
<h1>Edit Produk</h1><p><?= e($message) ?></p>
<form method="post" enctype="multipart/form-data">
Nama: <input name="name" value="<?= e($product['name']) ?>" required><br><br>
Kategori: <select name="category_id"><?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $c['id']==$product['category_id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select><br><br>
Deskripsi: <textarea name="description"><?= e($product['description']) ?></textarea><br><br>
Harga: <input type="number" step="0.01" min="1" name="price" value="<?= e($product['price']) ?>" required><br><br>
Rating: <input type="number" step="0.1" min="0" max="5" name="rating" value="<?= e($product['rating']) ?>"><br><br>
Jumlah ulasan: <input type="number" min="0" name="review_count" value="<?= e($product['review_count']) ?>"><br><br>
Ganti gambar: <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"><br><br>
<button>Simpan Perubahan</button></form><p><a href="index.php?products=1">Kembali</a></p></body></html>
