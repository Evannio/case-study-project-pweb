<?php
require '../config.php'; require_role('owner');
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
try { $stmt->execute([$id]); }
catch (PDOException $e) { exit('Produk tidak dapat dihapus karena sudah terkait dengan transaksi.'); }
header('Location: index.php?products=1'); exit;
