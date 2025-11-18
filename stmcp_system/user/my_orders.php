<?php
require_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

/* Handle User Actions */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $order_id = $_POST['order_id'] ?? null;
    $action   = $_POST['action'] ?? null;

    /* Cancel Order */
    if ($order_id && $action === 'cancel_order_user') {
        mysqli_query($conn, "
            UPDATE orders 
            SET payment_status='cancelled', order_status='cancelled' 
            WHERE id='$order_id' 
              AND user_id='$user_id' 
              AND payment_status='pending'
        ");
        header("Location: my_orders.php");
        exit;
    }

    /* Request Refund */
    if ($order_id && $action === 'request_refund') {

        // ⛔ PREVENT REFUND IF READY FOR PICKUP
        $check_status = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT order_status FROM orders 
            WHERE id='$order_id' AND user_id='$user_id'
        "));

        if ($check_status && $check_status['order_status'] === 'ready_for_pickup') {
            $_SESSION['refund_error'] = "Refund not allowed. Your order is already marked as Ready for Pickup.";
            header("Location: my_orders.php");
            exit;
        }

        // Existing refund validation
        $order_check = mysqli_query($conn, "
            SELECT * FROM orders 
            WHERE id='$order_id' 
              AND user_id='$user_id' 
              AND payment_status IN ('paid','verified') 
              AND refund_status='none'
              AND order_status NOT IN ('completed','cancelled','refunded')
        ");

        if (mysqli_num_rows($order_check) > 0) {
            mysqli_query($conn, "
                UPDATE orders 
                SET refund_status='requested' 
                WHERE id='$order_id' 
                  AND user_id='$user_id'
            ");
        }

        header("Location: my_orders.php");
        exit;
    }
}

/* Pagination */
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

$total_orders_res = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM orders 
    WHERE user_id='$user_id'
");
$total_orders = mysqli_fetch_assoc($total_orders_res)['total'];
$total_pages = ceil($total_orders / $limit);

$result = mysqli_query($conn, "
    SELECT * FROM orders
    WHERE user_id='$user_id'
    ORDER BY created_at DESC
    LIMIT $start, $limit
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders - STMCP Merchandise</title>
<link href="../css/js/userstyle.css" rel="stylesheet">
</head>
<body>

<h2>📦 My Orders</h2>

<div class="nav-btns">
    <a href="merchandise.php">🛍 Merchandise</a>
    <a href="cart.php">🛒 Cart</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- 🔔 Display error message -->
<?php if (isset($_SESSION['refund_error'])): ?>
    <div style="background:#ffdddd;padding:10px;margin:10px 0;border-left:4px solid #d00;color:#b00;">
        <?= $_SESSION['refund_error']; ?>
    </div>
    <?php unset($_SESSION['refund_error']); ?>
<?php endif; ?>

<?php if (mysqli_num_rows($result) > 0): ?>
<table>
<tr>
    <th>Date</th>
    <th>Total Amount</th>
    <th>Payment Method</th>
    <th>Payment Status</th>
    <th>Order Status</th>
    <th>Refund Status</th>
    <th>Action</th>
</tr>

<?php while ($order = mysqli_fetch_assoc($result)):

/* PAYMENT STATUS MAP */
$ps_map = [
    'pending'   => ['Pending', 'pending'],
    'paid'      => ['Paid', 'paid'],
    'verified'  => ['Verified', 'verified'],
    'cancelled' => ['Cancelled', 'cancelled'],
    'refunded'  => ['Refunded', 'refunded']
];
[$ps_text, $ps_class] = $ps_map[$order['payment_status']] ?? ['Pending', 'pending'];

/* ORDER STATUS MAP */
$os_map = [
    'pending'         => ['Pending', 'pending_os'],
    'processing'      => ['Processing', 'processing'],
    'ready_for_pickup'=> ['Ready for Pickup', 'ready'],
    'completed'       => ['Completed', 'completed'],
    'cancelled'       => ['Cancelled', 'cancelled_os'],
    'refunded'        => ['Refunded', 'refunded_os']
];
[$os_text, $os_class] = $os_map[$order['order_status']] ?? ['Pending', 'pending_os'];

/* REFUND STATUS MAP */
$rs_map = [
    'none'      => ['-', ''],
    'requested' => ['Requested', 'requested'],
    'approved'  => ['Approved', 'approved'],
    'rejected'  => ['Rejected', 'rejected']
];
[$rs_text, $rs_class] = $rs_map[$order['refund_status'] ?? 'none'] ?? ['-', ''];

?>

<tr>
    <td><?= $order['created_at']; ?></td>
    <td>₱<?= number_format($order['total_amount'], 2); ?></td>
    <td><?= ucfirst($order['payment_method']); ?></td>
    <td><span class="status <?= $ps_class ?>"><?= $ps_text ?></span></td>
    <td><span class="status <?= $os_class ?>"><?= $os_text ?></span></td>
    <td><?= $rs_class ? "<span class='status $rs_class'>$rs_text</span>" : $rs_text ?></td>

    <td>
        <a href="order_details.php?id=<?= $order['id'] ?>" class="button view-btn">View</a>

        <?php if ($order['order_status'] == 'pending' && $order['payment_status'] == 'pending'): ?>
            <button class="button cancel-btn" data-id="<?= $order['id'] ?>">Cancel</button>
        <?php endif; ?>

        <?php if (
            in_array($order['payment_status'], ['paid', 'verified']) &&
            $order['refund_status'] == 'none' &&
            !in_array($order['order_status'], ['completed', 'cancelled', 'refunded', 'ready_for_pickup'])
        ): ?>
            <button class="button refund-btn" data-id="<?= $order['id'] ?>">Request Refund</button>
        <?php endif; ?>
    </td>
</tr>

<?php endwhile; ?>
</table>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">Prev</a>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
        <a href="?page=<?= $p ?>" class="<?= ($p == $page) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>">Next</a>
    <?php endif; ?>
</div>

<?php else: ?>
<p style="text-align:center;">No orders found. <a href="merchandise.php">Shop Now</a></p>
<?php endif; ?>


<!-- CANCEL MODAL -->
<div id="cancelModal" class="modal">
<div class="modal-content">
    <h3>Cancel Order</h3>
    <p>Are you sure you want to cancel this order?</p>
    <form method="POST">
        <input type="hidden" name="order_id" id="cancel_order_id">
        <button type="submit" name="action" value="cancel_order_user" class="btn-confirm">Yes, Cancel Order</button>
    </form>
    <button class="btn-close" onclick="document.getElementById('cancelModal').style.display='none'">Close</button>
</div>
</div>

<!-- REFUND MODAL -->
<div id="refundModal" class="modal">
<div class="modal-content">
    <h3>Request Refund</h3>
    <p>Are you sure you want to request a refund?</p>
    <form method="POST">
        <input type="hidden" name="order_id" id="refund_order_id">
        <button type="submit" name="action" value="request_refund" class="btn-refund">Yes, Request Refund</button>
    </form>
    <button class="btn-close" onclick="document.getElementById('refundModal').style.display='none'">Close</button>
</div>
</div>

<script>
document.querySelectorAll('.cancel-btn').forEach(btn => {
    btn.onclick = function() {
        document.getElementById('cancel_order_id').value = this.dataset.id;
        document.getElementById('cancelModal').style.display = 'flex';
    };
});

document.querySelectorAll('.refund-btn').forEach(btn => {
    btn.onclick = function() {
        document.getElementById('refund_order_id').value = this.dataset.id;
        document.getElementById('refundModal').style.display = 'flex';
    };
});
</script>

</body>
</html>
