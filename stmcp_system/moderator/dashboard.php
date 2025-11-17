<?php
require_once '../connection.php';
session_start();

// --- Handle Moderator Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $action   = $_POST['action'] ?? null;

    if ($order_id && $action) {
        switch ($action) {
            case 'mark_paid':
                mysqli_query($conn, "UPDATE orders SET payment_status='paid', order_status='pending' WHERE id='$order_id'");
                break;
            case 'verify_payment':
                mysqli_query($conn, "UPDATE orders SET payment_status='verified', order_status='pending' WHERE id='$order_id'");
                break;
            case 'ready':
                mysqli_query($conn, "UPDATE orders SET order_status='ready_for_pickup' WHERE id='$order_id' AND payment_status IN ('paid','verified')");
                break;
            case 'completed':
                mysqli_query($conn, "UPDATE orders SET order_status='completed' WHERE id='$order_id'");
                break;
            case 'approve_refund':
                mysqli_query($conn, "UPDATE orders SET refund_status='approved', payment_status='refunded', order_status='refunded' WHERE id='$order_id'");
                break;
            case 'reject_refund':
                mysqli_query($conn, "UPDATE orders SET refund_status='rejected' WHERE id='$order_id'");
                break;
        }
    }

    header("Location: dashboard.php");
    exit;
}

// --- Fetch all orders with USER FULL NAME ---
$orders = mysqli_query($conn, "
    SELECT 
        orders.*, 
        users.fullname 
    FROM orders
    LEFT JOIN users ON orders.user_id = users.id
    ORDER BY orders.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Moderator Dashboard - STMCP</title>
<style>
body { font-family: Arial, sans-serif; background: #f2f2f2; margin:0; padding:0; }
.container { max-width: 1200px; margin: 30px auto; background: #fff; padding: 25px 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
h2 { text-align: center; margin-bottom: 20px; }
.tabs { text-align:center; margin-bottom:20px; }
.tab-btn { padding:8px 15px; margin:2px; border:none; border-radius:5px; cursor:pointer; background:#ddd; transition: 0.3s; }
.tab-btn.active { background:#2196F3; color:#fff; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
th { background-color: #2196F3; color: #fff; }
button { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; color: #fff; margin: 2px; }
.mark-paid { background-color: #4CAF50; }
.verify-payment { background-color: #FF9800; }
.ready { background-color: #2196F3; }
.completed { background-color: #9E9E9E; }
.approve { background-color: green; }
.reject { background-color: red; }
.actions form { display: inline; margin: 0; }
</style>
</head>
<body>

<div class="container">
    <h2>Moderator Dashboard - Orders</h2>

    <div class="tabs">
        <button class="tab-btn active" onclick="filterOrders('all', this)">All</button>
        <button class="tab-btn" onclick="filterOrders('cash', this)">Cash</button>
        <button class="tab-btn" onclick="filterOrders('gcash', this)">GCash</button>
    </div>

    <table id="ordersTable">
        <tr>
            <th>Order ID</th>
            <th>Full Name</th>
            <th>Total</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
            <th>Order Status</th>
            <th>Refund Status</th>
            <th>Proof</th>
            <th>Actions</th>
        </tr>

        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
        <tr data-method="<?= $order['payment_method'] ?>">
            <td><?= $order['id'] ?></td>

            <!-- FULL NAME DITO NA -->
            <td><?= $order['fullname'] ? $order['fullname'] : 'Unknown User' ?></td>

            <td>₱<?= number_format($order['total_amount'], 2) ?></td>
            <td><?= ucfirst($order['payment_method']) ?></td>
            <td><?= ucfirst($order['payment_status']) ?></td>
            <td><?= ucfirst(str_replace('_',' ',$order['order_status'])) ?></td>

            <td>
                <?php
                switch ($order['refund_status']) {
                    case 'none': echo '-'; break;
                    case 'requested': echo '<span style="color:#ff9800;">Requested</span>'; break;
                    case 'approved': echo '<span style="color:green;">Approved</span>'; break;
                    case 'rejected': echo '<span style="color:red;">Rejected</span>'; break;
                }
                ?>
            </td>

            <td>
                <?= $order['proof'] ? "<a href='../uploads/{$order['proof']}' target='_blank'>View</a>" : '—'; ?>
            </td>

            <td class="actions">
                <?php if (!in_array($order['order_status'], ['completed','cancelled','refunded'])): ?>

                    <?php if ($order['payment_method'] === 'cash' && $order['payment_status'] === 'pending'): ?>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="hidden" name="action" value="mark_paid">
                            <button type="submit" class="mark-paid">Mark as Paid</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($order['payment_method'] === 'gcash' && $order['payment_status'] === 'pending'): ?>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="hidden" name="action" value="verify_payment">
                            <button type="submit" class="verify-payment">Verify Payment</button>
                        </form>
                    <?php endif; ?>

                    <?php if (in_array($order['payment_status'], ['paid','verified']) && $order['order_status'] === 'pending'): ?>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="hidden" name="action" value="ready">
                            <button type="submit" class="ready">Mark as Ready for Pickup</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($order['refund_status'] === 'requested'): ?>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="hidden" name="action" value="approve_refund">
                            <button type="submit" class="approve">Approve Refund</button>
                        </form>

                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="hidden" name="action" value="reject_refund">
                            <button type="submit" class="reject">Reject Refund</button>
                        </form>
                    <?php endif; ?>

                <?php endif; ?>

                <?php if ($order['order_status'] === 'ready_for_pickup'): ?>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <input type="hidden" name="action" value="completed">
                        <button type="submit" class="completed">Mark as Completed</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
function filterOrders(method, btn){
    const rows = document.querySelectorAll('#ordersTable tr[data-method]');
    rows.forEach(row => row.style.display = (method === 'all' || row.getAttribute('data-method') === method) ? '' : 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}
</script>

</body>
</html>
