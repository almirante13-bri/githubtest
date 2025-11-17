<?php
require_once '../connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];


$user_id = $_SESSION['user_id'] ?? 1;

if (!isset($_GET['id'])) {
    echo "No order selected.";
    exit;
}

$order_id = $_GET['id'];

// Fetch order info
$order_res = mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id'");
if (mysqli_num_rows($order_res) == 0) {
    echo "Order not found.";
    exit;
}
$order = mysqli_fetch_assoc($order_res);

// Fetch order items
$items_res = mysqli_query($conn, "SELECT oi.*, p.name, p.image FROM order_items oi
                                  JOIN products p ON oi.product_id = p.id
                                  WHERE oi.order_id='$order_id'");

// Calculate total
$total = 0;
$items = [];
while ($item = mysqli_fetch_assoc($items_res)) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
    $items[] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Details</title>
<link  href="../css\js/style.css" rel="stylesheet">
</head>
<body>

<h2>📦 Order Details</h2>

<!-- Order Info Card -->
<div class="order-card">
    <h3>Order Summary</h3>
    <p><strong>Date:</strong> <?= $order['created_at']; ?></p>
    <p><strong>Total Amount:</strong> ₱<?= number_format($total,2); ?></p>
    <p><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']); ?></p>
    <p><strong>Payment Status:</strong> 
        <?php
            $ps = $order['payment_status'];
            $class = ($ps=='pending')?'pending':(($ps=='paid')?'paid':'cancelled');
            echo "<span class='status $class'>".ucfirst($ps)."</span>";
        ?>
    </p>
    <p><strong>Order Status:</strong>
        <?php
            $os = $order['order_status'];
            $class = ($os=='pending')?'pending':(($os=='ready')?'ready':'cancelled');
            echo "<span class='status $class'>".ucfirst(str_replace('_',' ',$os))."</span>";
        ?>
    </p>
    <p><strong>Refund Status:</strong>
        <?php
            $rs = $order['refund_status'] ?? 'none';
            $class = ($rs=='requested')?'requested':(($rs=='approved')?'approved':(($rs=='rejected')?'rejected':'')); 
            echo ($rs=='none')?'-':"<span class='status $class'>".ucfirst($rs)."</span>";
        ?>
    </p>

    <!-- Display GCash Proof if uploaded -->
    <?php if($order['payment_method']=='gcash' && !empty($order['proof'])): ?>
        <p><strong>Payment Proof:</strong></p>
        <img src="../uploads/<?= htmlspecialchars($order['proof']); ?>" alt="Payment Proof" class="payment-proof">
    <?php endif; ?>
</div>

<!-- Product Cards -->
<?php foreach($items as $item): ?>
<div class="product-card">
    <img src="../uploads/<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
    <div class="product-info">
        <p><strong><?= htmlspecialchars($item['name']); ?></strong></p>
        <p>Quantity: <?= $item['quantity']; ?></p>
        <p>Price: ₱<?= number_format($item['price'],2); ?> | Subtotal: ₱<?= number_format($item['subtotal'],2); ?></p>
    </div>
</div>
<?php endforeach; ?>

<!-- Action Buttons -->
<div style="width:90%; margin:auto; text-align:right; margin-top:10px;">
    <?php if($order['order_status']=='pending' && $order['payment_status']=='pending'): ?>
        <button class="button cancel-btn" onclick="document.getElementById('cancelModal').style.display='flex';">Cancel Order</button>
    <?php endif; ?>
    <a href="my_orders.php" class="button back-btn">Back to Orders</a>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Cancel Order</h3>
        <p>Are you sure you want to cancel this order?</p>
        <form method="POST" action="my_orders.php">
            <input type="hidden" name="order_id" value="<?= $order_id; ?>">
            <button type="submit" name="action" value="cancel_order_user" class="button cancel-btn">Yes, Cancel Order</button>
        </form>
        <button class="button back-btn" onclick="document.getElementById('cancelModal').style.display='none';">Close</button>
    </div>
</div>

<script>
// Close modals when clicking outside
window.onclick = function(e){
    if(e.target.classList.contains('modal')){
        e.target.style.display='none';
    }
}
</script>

</body>
</html>
