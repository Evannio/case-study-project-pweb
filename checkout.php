<?php
require 'config.php';
$sessionId = cart_session_id();
$stmt = $pdo->prepare("SELECT carts.product_id, carts.quantity, products.name, products.price
    FROM carts JOIN products ON carts.product_id=products.id WHERE carts.session_id=?");
$stmt->execute([$sessionId]); $items = $stmt->fetchAll();
if (!$items) { header('Location: cart.php'); exit; }

$user = current_user();
$customerName = $user['name'] ?? '';
$customerEmail = $user['email'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    if ($customerName === '') {
        $message = 'Nama pemesan wajib diisi.';
    } else {
        try {
            $pdo->beginTransaction();
            $total = 0;
            foreach ($items as $item) $total += $item['price'] * $item['quantity'];
            $discount = ($user && $user['role'] === 'customer') ? round($total * 0.10, 2) : 0;
            $payment = $total - $discount;
            $stmt = $pdo->prepare("INSERT INTO orders
                (user_id, customer_name, customer_email, total_before_discount, discount_amount, total_payment)
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user['id'] ?? null, $customerName, $customerEmail, $total, $discount, $payment]);
            $orderId = $pdo->lastInsertId();

            $detail = $pdo->prepare("INSERT INTO order_details
                (order_id, product_id, product_name, quantity, price_each, subtotal)
                VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                $detail->execute([$orderId, $item['product_id'], $item['name'], $item['quantity'],
                    $item['price'], $item['price'] * $item['quantity']]);
            }
            $delete = $pdo->prepare("DELETE FROM carts WHERE session_id=?");
            $delete->execute([$sessionId]);
            $pdo->commit();
            header('Location: confirmation_order.php?id=' . $orderId); exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $message = 'Checkout gagal. Silakan coba kembali.';
        }
    }
}
$total = 0; foreach ($items as $item) $total += $item['price'] * $item['quantity'];
$discount = ($user && $user['role'] === 'customer') ? round($total * 0.10, 2) : 0;
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Checkout</title></head><body>
<h1>Checkout</h1><p><?= e($message) ?></p>
<table border="1" cellpadding="6"><tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr>
<?php foreach ($items as $item): ?><tr>
<td><?= e($item['name']) ?></td><td><?= $item['quantity'] ?></td><td><?= rupiah($item['price']) ?></td>
<td><?= rupiah($item['price']*$item['quantity']) ?></td></tr><?php endforeach; ?></table>
<p>Total awal: <?= rupiah($total) ?></p><p>Diskon 10%: <?= rupiah($discount) ?></p>
<p>Total bayar: <?= rupiah($total-$discount) ?></p>
<form method="post">
Nama pemesan: <input name="customer_name" value="<?= e($customerName) ?>" required><br><br>
Email: <input type="email" name="customer_email" value="<?= e($customerEmail) ?>"><br><br>
<button type="submit">Buat Pesanan</button>
</form><p><a href="cart.php">Kembali ke keranjang</a></p>
</body></html>
