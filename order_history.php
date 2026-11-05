<?php
require 'config.php';
require_role('customer');
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([current_user()['id']]); $orders = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Riwayat Pesanan</title></head><body>
<h1>Riwayat Pesanan</h1><p><a href="index.php">Beranda</a></p>
<table border="1" cellpadding="6"><tr><th>ID</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Detail</th></tr>
<?php foreach ($orders as $o): ?><tr>
<td><?= $o['id'] ?></td><td><?= e($o['created_at']) ?></td><td><?= rupiah($o['total_payment']) ?></td>
<td><?= e($o['status']) ?></td><td><a href="confirmation_order.php?id=<?= $o['id'] ?>">Lihat</a></td>
</tr><?php endforeach; ?></table></body></html>
