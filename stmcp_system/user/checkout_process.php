<?php
require_once '../connection.php';
session_start();

// Get logged-in user
$user_id = $_SESSION['user_id'] ?? 1;

// Get selected items
$checkout_items = $_POST['checkout_items'] ?? [];
if (empty($checkout_items)) {
    header("Location: cart.php");
    exit;
}

// Initialize total and fetch item details
$total = 0;
$item_details = [];
$ids = implode(',', array_map('intval', $checkout_items));

$query = mysqli_query($conn, "
    SELECT uc.id AS cart_id, uc.product_id, uc.quantity, p.name, p.price
    FROM user_cart uc
    JOIN products p ON uc.product_id = p.id
    WHERE uc.id IN ($ids) AND uc.user_id='$user_id'
");

while ($row = mysqli_fetch_assoc($query)) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $item_details[] = $row;
}

// Payment method
$payment_method = $_POST['payment_method'] ?? '';
$proof = null;

// Validate payment method
if (!in_array($payment_method, ['cash', 'gcash'])) {
    header("Location: cart.php");
    exit;
}

// Handle GCash proof upload
if ($payment_method === 'gcash' && isset($_FILES['proof']) && $_FILES['proof']['error'] === 0) {
    $ext = pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . ".$ext";
    $upload_dir = "../uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    move_uploaded_file($_FILES['proof']['tmp_name'], $upload_dir . $filename);
    $proof = $filename;
}

// Insert into orders
$stmt = $conn->prepare("INSERT INTO orders 
    (user_id, payment_method, proof, total_amount, payment_status, order_status, created_at) 
    VALUES (?, ?, ?, ?, 'pending', 'pending', NOW())");
$stmt->bind_param("issi", $user_id, $payment_method, $proof, $total);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// Insert order_items
$stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($item_details as $item) {
    $pid = $item['product_id'];
    $qty = $item['quantity'];
    $price = $item['price'];
    $stmt_item->bind_param("iiid", $order_id, $pid, $qty, $price);
    $stmt_item->execute();
}
$stmt_item->close();

// Remove items from user_cart (using user_id instead of session_id)
if (!empty($checkout_items)) {
    $ids_str = implode(',', array_map('intval', $checkout_items));
    mysqli_query($conn, "DELETE FROM user_cart WHERE id IN ($ids_str) AND user_id='$user_id'");
}

// Clear checkout_cart session
unset($_SESSION['checkout_cart']);

// Redirect back to cart
header("Location: cart.php");
exit;
?>
