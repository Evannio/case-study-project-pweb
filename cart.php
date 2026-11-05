<?php
require 'config.php';
$sessionId = cart_session_id();

if (isset($_POST['add_to_cart'])) {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    $check = $pdo->prepare("SELECT id FROM products WHERE id=?");
    $check->execute([$productId]);
    if ($check->fetch()) {
        $userId = current_user()['id'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO carts (user_id, session_id, product_id, quantity)
            VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE quantity=quantity+VALUES(quantity), user_id=VALUES(user_id)");
        $stmt->execute([$userId, $sessionId, $productId, $quantity]);
    }
    header('Location: cart.php'); exit;
}
if (isset($_POST['update_cart'])) {
    foreach (($_POST['quantities'] ?? []) as $id => $qty) {
        $qty = (int)$qty;
        if ($qty <= 0) {
            $stmt = $pdo->prepare("DELETE FROM carts WHERE id=? AND session_id=?");
            $stmt->execute([(int)$id, $sessionId]);
        } else {
            $stmt = $pdo->prepare("UPDATE carts SET quantity=? WHERE id=? AND session_id=?");
            $stmt->execute([$qty, (int)$id, $sessionId]);
        }
    }
    header('Location: cart.php'); exit;
}
$stmt = $pdo->prepare("SELECT carts.id AS cart_id, carts.quantity, products.*
    FROM carts JOIN products ON carts.product_id=products.id WHERE carts.session_id=?");
$stmt->execute([$sessionId]); $items = $stmt->fetchAll();
$total = 0; foreach ($items as $item) $total += $item['price'] * $item['quantity'];
$discount = is_logged_in() && current_user()['role'] === 'customer' ? $total * 0.10 : 0;
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Keranjang</title></head><body>
<h1>Keranjang Belanja</h1><p><a href="index.php">Lanjut belanja</a></p>
<?php if (!$items): ?><p>Keranjang masih kosong.</p><?php else: ?>
<form method="post">
<table border="1" cellpadding="6"><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr>
<?php foreach ($items as $item): ?><tr>
<td><?= e($item['name']) ?></td><td><?= rupiah($item['price']) ?></td>
<td><input type="number" min="0" name="quantities[<?= $item['cart_id'] ?>]" value="<?= $item['quantity'] ?>"></td>
<td><?= rupiah($item['price']*$item['quantity']) ?></td></tr><?php endforeach; ?>
</table><p><button name="update_cart">Perbarui Keranjang</button></p>
</form>
<p>Total: <?= rupiah($total) ?></p><p>Diskon: <?= rupiah($discount) ?></p>
<p>Total pembayaran: <?= rupiah($total-$discount) ?></p>
<a href="checkout.php">Lanjut Checkout</a>
<?php endif; ?></body></html>
