<?php
require '../config.php'; require_role('owner');
$orderId=(int)($_POST['order_id']??0); $cashierId=$_POST['cashier_id']!==''?(int)$_POST['cashier_id']:null;
if ($cashierId !== null) {
    $check=$pdo->prepare("SELECT id FROM users WHERE id=? AND role_id=(SELECT id FROM roles WHERE role_name='kasir')");
    $check->execute([$cashierId]); if (!$check->fetch()) exit('Kasir tidak valid.');
}
$update=$pdo->prepare("UPDATE orders SET cashier_id=? WHERE id=?"); $update->execute([$cashierId,$orderId]);
header('Location: cashiers.php'); exit;
