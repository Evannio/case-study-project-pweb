<?php
require 'config.php'; require_role('owner');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="laporan_penjualan_kopipiro.csv"');
$output=fopen('php://output','w');
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output,['ID Pesanan','Tanggal','Nama Customer','Email','Total Awal','Diskon','Total Bayar','Status','Kasir']);
$sql="SELECT o.*, u.name AS cashier_name FROM orders o LEFT JOIN users u ON o.cashier_id=u.id ORDER BY o.created_at DESC";
foreach($pdo->query($sql) as $row) {
    fputcsv($output,[$row['id'],$row['created_at'],$row['customer_name'],$row['customer_email'],
        $row['total_before_discount'],$row['discount_amount'],$row['total_payment'],$row['status'],$row['cashier_name']]);
}
fclose($output); exit;
