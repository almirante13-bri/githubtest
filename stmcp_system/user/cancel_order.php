<?php
require_once '../connection.php';
session_start();



$user_id = $_SESSION['user_id'] ?? 1;

if (!isset($_GET['id'])) {
    header("Location: my_orders.php");
    exit;
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'] ?? 1;

// check if ang order ay existing and belongs to this user
$query = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$query->bind_param("ii", $order_id, $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "Order not found or not yours. <a href='my_orders.php'>Go back</a>";
    exit;
}

$order = $result->fetch_assoc();

// Inaallow lang ang cancellation kung pending pa
if ($order['order_status'] !== 'pending') {
    echo "This order cannot be cancelled. <a href='my_orders.php'>Go back</a>";
    exit;
}

// inaupdate ang order status sa database sa 'cancelled'
$update = $conn->prepare("UPDATE orders SET order_status = 'cancelled' WHERE id = ?");
$update->bind_param("i", $order_id);
$update->execute();

echo "Order #$order_id has been cancelled successfully. <a href='my_orders.php'>Return to My Orders</a>";
?>
