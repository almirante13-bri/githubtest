<?php
require_once '../connection.php';
session_start();

// Logged-in user
$user_id = $_SESSION['user_id'] ?? 1;

// Fetch cart items for this user
$cart_items = mysqli_query($conn, "
    SELECT uc.*, p.name, p.price, p.image 
    FROM user_cart uc
    JOIN products p ON uc.product_id = p.id
    WHERE uc.user_id='$user_id'
");

if (mysqli_num_rows($cart_items) == 0) {
    echo "<p style='text-align:center;'>Your cart is empty.</p>";
    exit;
}

$total = 0;

echo "<table style='width:100%; border-collapse: collapse;'>";
echo "<tr>
        <th style='padding:8px; border-bottom:1px solid #ddd;'>Image</th>
        <th style='padding:8px; border-bottom:1px solid #ddd;'>Product</th>
        <th style='padding:8px; border-bottom:1px solid #ddd;'>Size</th>
        <th style='padding:8px; border-bottom:1px solid #ddd;'>Qty</th>
        <th style='padding:8px; border-bottom:1px solid #ddd;'>Subtotal</th>
      </tr>";

while ($item = mysqli_fetch_assoc($cart_items)) {
    $subtotal = $item['price'] * $item['quantity'];
    $total += $subtotal;

    echo "
    <tr>
        <td style='padding:8px; text-align:center;'>
            <img src='../uploads/{$item['image']}' width='50'>
        </td>
        <td style='padding:8px; text-align:center;'>{$item['name']}</td>
        <td style='padding:8px; text-align:center;'>{$item['size']}</td>
        <td style='padding:8px; text-align:center;'>{$item['quantity']}</td>
        <td style='padding:8px; text-align:center;'>₱" . number_format($subtotal,2) . "</td>
    </tr>";
}

echo "
<tr>
    <td colspan='4' style='text-align:right; padding:10px; font-weight:bold;'>Total:</td>
    <td style='text-align:center; font-weight:bold;'>₱" . number_format($total,2) . "</td>
</tr>";

echo "</table>";
?>
