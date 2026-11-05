<?php
require 'config.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=?");
$stmt->execute([$id]); $order = $stmt->fetch();
if (!$order) exit('Pesanan tidak ditemukan.');
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Pesanan Berhasil</title></head><body>
<h1>Pesanan berhasil dibuat</h1>
<p>Nomor pesanan: <?= e($order['id']) ?></p>
<p>Nama: <?= e($order['customer_name']) ?></p>
<p>Total awal: <?= rupiah($order['total_before_discount']) ?></p>
<p>Diskon: <?= rupiah($order['discount_amount']) ?></p>
<p>Total pembayaran: <?= rupiah($order['total_payment']) ?></p>
<p>Status: <?= e($order['status']) ?></p>
<a href="index.php">Kembali ke produk</a>
</body></html>
