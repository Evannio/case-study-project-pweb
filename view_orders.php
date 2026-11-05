<?php
require 'config.php';
require_role('kasir');
$cashierId = current_user()['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $allowed = ['Diproses', 'Selesai', 'Dibatalkan'];
    if (in_array($_POST['status'], $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE orders SET status=? WHERE id=? AND cashier_id=?");
        $stmt->execute([$_POST['status'], (int)$_POST['order_id'], $cashierId]);
    }
    header('Location: view_orders.php'); exit;
}
$stmt = $pdo->prepare("SELECT * FROM orders WHERE cashier_id=? ORDER BY created_at DESC");
$stmt->execute([$cashierId]); $orders = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Pesanan Kasir</title></head><body>
<h1>Pesanan yang Ditugaskan kepada Saya</h1>
<p>Kasir: <?= e(current_user()['name']) ?> | <a href="logout.php">Logout</a></p>
<table border="1" cellpadding="6"><tr><th>ID</th><th>Pemesan</th><th>Total</th><th>Status</th><th>Ubah Status</th><th>Detail</th></tr>
<?php foreach ($orders as $o): ?><tr>
<td><?= $o['id'] ?></td><td><?= e($o['customer_name']) ?></td><td><?= rupiah($o['total_payment']) ?></td>
<td><?= e($o['status']) ?></td><td>
<form method="post"><input type="hidden" name="order_id" value="<?= $o['id'] ?>">
<select name="status"><option>Diproses</option><option>Selesai</option><option>Dibatalkan</option></select>
<button type="submit">Simpan</button></form></td>
<td><a href="view_orders.php?detail=<?= $o['id'] ?>">Lihat</a></td></tr><?php endforeach; ?></table>
<?php
if (isset($_GET['detail'])) {
    $detailStmt = $pdo->prepare("SELECT od.* FROM order_details od JOIN orders o ON od.order_id=o.id
        WHERE o.id=? AND o.cashier_id=?");
    $detailStmt->execute([(int)$_GET['detail'], $cashierId]);
    echo '<h2>Detail Produk</h2><ul>';
    foreach ($detailStmt->fetchAll() as $d) echo '<li>'.e($d['product_name']).' x '.(int)$d['quantity'].' - '.rupiah($d['subtotal']).'</li>';
    echo '</ul>';
}
?>
</body></html>
