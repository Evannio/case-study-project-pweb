<?php
require '../config.php';
require_role('owner');
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalSales = $pdo->query("SELECT COALESCE(SUM(total_payment),0) FROM orders WHERE status <> 'Dibatalkan'")->fetchColumn();
$products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$monthly = $pdo->query("SELECT DATE_FORMAT(created_at,'%Y-%m') AS month_name,
    SUM(total_payment) AS sales FROM orders WHERE status <> 'Dibatalkan'
    GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY month_name DESC LIMIT 6")->fetchAll();
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Dashboard Owner</title></head><body>
<h1>Dashboard Owner Kopipiro</h1>
<p>Halo, <?= e(current_user()['name']) ?> | <a href="../index.php">Website</a> | <a href="../logout.php">Logout</a></p>
<ul>
<li><a href="create.php">Tambah Produk</a></li>
<li><a href="index.php?products=1">Kelola Produk</a></li>
<li><a href="categories.php">Kelola Kategori</a></li>
<li><a href="cashiers.php">Kelola Kasir</a></li>
<li><a href="../excel_generate.php">Unduh Excel/CSV Laporan</a></li>

</ul>
<h2>Ringkasan Penjualan</h2>
<p>Jumlah pesanan: <?= $totalOrders ?></p><p>Total penjualan (tidak termasuk pesanan batal): <?= rupiah($totalSales) ?></p>
<p>Jumlah produk: <?= $products ?></p>
<h3>Penjualan per Bulan</h3>
<table border="1" cellpadding="6"><tr><th>Bulan</th><th>Penjualan</th><th>Grafik sederhana</th></tr>
<?php foreach ($monthly as $m): ?><tr><td><?= e($m['month_name']) ?></td><td><?= rupiah($m['sales']) ?></td>
<td><meter min="0" max="<?= max(1,(float)$monthly[0]['sales']) ?>" value="<?= (float)$m['sales'] ?>"></meter></td></tr><?php endforeach; ?>
</table>
<?php
$allOrders = $pdo->query("SELECT o.*, u.name AS cashier_name FROM orders o
    LEFT JOIN users u ON o.cashier_id=u.id ORDER BY o.created_at DESC")->fetchAll();
?>
<h2>Semua Pesanan</h2>
<table border="1" cellpadding="6"><tr><th>ID</th><th>Pemesan</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Kasir</th><th>Detail Produk</th></tr>
<?php foreach ($allOrders as $o): ?><tr>
<td><?= $o['id'] ?></td><td><?= e($o['customer_name']) ?></td><td><?= e($o['created_at']) ?></td>
<td><?= rupiah($o['total_payment']) ?></td><td><?= e($o['status']) ?></td>
<td><?= e($o['cashier_name'] ?? 'Belum ditugaskan') ?></td>
<td><a href="index.php?order_detail=<?= $o['id'] ?>">Lihat detail</a></td></tr><?php endforeach; ?></table>
<?php
if (isset($_GET['order_detail'])) {
    $detail = $pdo->prepare("SELECT * FROM order_details WHERE order_id=?");
    $detail->execute([(int)$_GET['order_detail']]);
    echo '<h3>Detail Pesanan #'.(int)$_GET['order_detail'].'</h3><ul>';
    foreach ($detail->fetchAll() as $d) {
        echo '<li>'.e($d['product_name']).' x '.(int)$d['quantity'].' = '.rupiah($d['subtotal']).'</li>';
    }
    echo '</ul>';
}
?>
<?php if (isset($_GET['products'])):
$list = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC")->fetchAll(); ?>
<h2>Daftar Produk</h2><table border="1" cellpadding="6"><tr><th>Nama</th><th>Kategori</th><th>Harga</th><th>Aksi</th></tr>
<?php foreach ($list as $p): ?><tr><td><?= e($p['name']) ?></td><td><?= e($p['category_name']) ?></td><td><?= rupiah($p['price']) ?></td>
<td><a href="edit.php?id=<?= $p['id'] ?>">Edit</a> | <a href="delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Hapus produk?')">Hapus</a></td></tr><?php endforeach; ?>
</table><?php endif; ?>
</body></html>
