<?php
require 'config.php';
$sql = "SELECT products.*, categories.name AS category_name
        FROM products JOIN categories ON products.category_id = categories.id
        ORDER BY products.id DESC";
$products = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Kopipiro - Produk</title></head>
<body>
<h1>Kopipiro Coffee Shop</h1>
<p>
<a href="index.php">Beranda</a> |
<a href="cart.php">Keranjang</a> |
<?php if (is_logged_in()): ?>
Halo, <?= e(current_user()['name']) ?> |
<a href="order_history.php">Riwayat Pesanan</a> |
<a href="logout.php">Logout</a>
<?php else: ?>
<a href="login.php">Login</a> | <a href="register.php">Register</a>
<?php endif; ?>
</p>
<hr>
<h2>Daftar Produk</h2>
<?php if (!$products): ?><p>Belum ada produk.</p><?php endif; ?>
<?php foreach ($products as $product): ?>
    <div>
        <?php if ($product['image']): ?>
            <img src="uploads/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" width="120">
        <?php endif; ?>
        <h3><?= e($product['name']) ?></h3>
        <p>Kategori: <?= e($product['category_name']) ?></p>
        <p><?= nl2br(e($product['description'])) ?></p>
        <p>Rating: <?= e($product['rating']) ?>/5 (<?= e($product['review_count']) ?> ulasan)</p>
        <p>Harga: <?= rupiah($product['price']) ?></p>
        <form action="cart.php" method="post">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            Jumlah: <input type="number" name="quantity" value="1" min="1" required>
            <button type="submit" name="add_to_cart">Tambah ke Keranjang</button>
        </form>
    </div>
    <hr>
<?php endforeach; ?>
</body></html>
