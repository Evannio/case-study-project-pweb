<?php
require '../config.php'; require_role('owner');
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $rating = max(0, min(5, (float)($_POST['rating'] ?? 0)));
    $reviewCount = max(0, (int)($_POST['review_count'] ?? 0));
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg','jpeg','png','webp'], true) || $_FILES['image']['size'] > 2*1024*1024) {
            $message = 'Gambar harus JPG, PNG, atau WEBP dan maksimal 2 MB.';
        } else {
            $imageName = uniqid('produk_', true).'.'.$extension;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$imageName)) $message = 'Upload gambar gagal.';
        }
    }
    if ($message === '' && $name !== '' && $price > 0 && $categoryId > 0) {
        $stmt = $pdo->prepare("INSERT INTO products (category_id,name,description,price,image,rating,review_count) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$categoryId,$name,$description,$price,$imageName,$rating,$reviewCount]);
        header('Location: index.php?products=1'); exit;
    } elseif ($message === '') $message = 'Nama, kategori, dan harga wajib benar.';
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Tambah Produk</title></head><body>
<h1>Tambah Produk</h1><p><?= e($message) ?></p>
<form method="post" enctype="multipart/form-data">
Nama produk: <input name="name" required><br><br>
Kategori: <select name="category_id" required><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select><br><br>
Deskripsi: <textarea name="description"></textarea><br><br>
Harga: <input type="number" name="price" min="1" step="0.01" required><br><br>
Rating (0-5): <input type="number" name="rating" min="0" max="5" step="0.1" value="0"><br><br>
Jumlah ulasan: <input type="number" name="review_count" min="0" value="0"><br><br>
Gambar: <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"><br><br>
<button type="submit">Simpan Produk</button>
</form><p><a href="index.php">Kembali</a></p></body></html>
