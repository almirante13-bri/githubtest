<?php
require_once '../connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

$user_id = $_SESSION['user_id'] ?? 1;

// ------------------------------------
// REMOVE ITEM FROM CART
// ------------------------------------
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM user_cart WHERE id='$id' AND user_id='$user_id'");
    header("Location: cart.php");
    exit;
}


// ------------------------------------
// FETCH CART ITEMS
// ------------------------------------
$cart_items = mysqli_query($conn, "
    SELECT uc.*, p.name, p.price, p.image 
    FROM user_cart uc 
    JOIN products p ON uc.product_id = p.id 
    WHERE uc.user_id='$user_id'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Cart - STMCP Merchandise</title>
<link href="../css/js/userstyle.css" rel="stylesheet">
</head>
<body>

<h2>🛒 My Cart</h2>

<div class="nav-buttons">
    <a href="merchandise.php"><button class="nav-merch">Go to Merchandise</button></a>
    <a href="my_orders.php"><button class="nav-orders">My Orders</button></a>
</div>

<?php if(mysqli_num_rows($cart_items) > 0): ?>
<form id="cartForm">
<table>
    <tr>
        <th>Select</th>
        <th>Image</th>
        <th>Product</th>
        <th>Size</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
        <th>Action</th>
    </tr>

    <?php 
    $total = 0;
    while ($item = mysqli_fetch_assoc($cart_items)): 
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
    ?>
    <tr>
        <td><input type="checkbox" name="selected_items[]" value="<?= $item['id']; ?>"></td>
        <td><img src="../uploads/<?= $item['image']; ?>"></td>
        <td><?= htmlspecialchars($item['name']); ?></td>
        <td><?= htmlspecialchars($item['size']); ?></td>
        <td>₱<?= number_format($item['price'],2); ?></td>
        <td><?= $item['quantity']; ?></td>
        <td>₱<?= number_format($subtotal,2); ?></td>
        <td class="actions"><a href="cart.php?remove=<?= $item['id']; ?>">Remove</a></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td colspan="6" class="total">Total:</td>
        <td colspan="2"><b>₱<?= number_format($total,2); ?></b></td>
    </tr>
</table>

<div class="checkout">
    <button type="button" id="toggleSelect">Select All</button>
    <button type="button" id="checkoutBtn">Checkout Selected Items</button>
</div>
</form>

<!-- Checkout Modal with enhanced design -->
<div id="checkoutModal" class="modal">
    <div class="modal-content checkout-container">
        <h3>Checkout</h3>
        <form method="POST" action="checkout_process.php" enctype="multipart/form-data" id="modalCheckoutForm">
            <div id="checkoutItemsList" style="max-height:250px; overflow-y:auto; margin-bottom:15px; border:1px solid #eee; padding:10px; border-radius:5px;"></div>
            <p class="total">Total: ₱<span id="modalTotal">0.00</span></p>

            <h4>Payment Method</h4>
            <div class="payment-method">
                <label class="payment-option" id="cashOption">
                    <input type="radio" name="payment_method" value="cash" required>
                    <img src="../assets/cash_icon.png" alt="Cash"> Cash
                </label>
                <label class="payment-option" id="gcashOption">
                    <input type="radio" name="payment_method" value="gcash" required>
                    <img src="../assets/gcash_icon.png" alt="GCash"> GCash
                </label>
            </div>

            <div id="proofContainer" style="display:none; margin-bottom:15px;">
                <label for="proofUpload" style="display:block; margin-bottom:5px; font-weight:bold;">Upload Proof of Payment:</label>
                <input type="file" name="proof" id="proofUpload" accept="image/*" style="padding:8px; width:100%; border-radius:5px; border:1px solid #ccc;">
                <p style="font-size:0.85em;color:#555;margin-top:5px;">Only required for GCash payments.</p>
            </div>

            <button type="submit" style="background:#4CAF50; color:white; padding:12px 20px; border:none; border-radius:5px; cursor:pointer; font-size:16px; transition:0.3s;">Place Order</button>
            <button type="button" class="close-modal" id="closeModal" style="background:#f44336; margin-left:10px;">Cancel</button>
        </form>
    </div>
</div>

<script>
const toggleBtn = document.getElementById('toggleSelect');
const checkoutBtn = document.getElementById('checkoutBtn');
const checkoutModal = document.getElementById('checkoutModal');
const closeModal = document.getElementById('closeModal');
const checkoutItemsList = document.getElementById('checkoutItemsList');
const modalTotal = document.getElementById('modalTotal');
const proofUpload = document.getElementById('proofUpload');

let allSelected = false;
toggleBtn.addEventListener('click', () => {
    const checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
    allSelected = !allSelected;
    checkboxes.forEach(cb => cb.checked = allSelected);
    toggleBtn.textContent = allSelected ? "Deselect All" : "Select All";
});

// Checkout modal logic
checkoutBtn.addEventListener('click', () => {
    const selected = Array.from(document.querySelectorAll('input[name="selected_items[]"]:checked'));
    if(selected.length === 0){ alert("Select at least one item."); return; }

    let total = 0;
    let html = "<ul style='margin:0; padding:0; list-style:none;'>";
    selected.forEach(cb => {
        const row = cb.closest('tr');
        const name = row.children[2].textContent;
        const qty = row.children[5].textContent;
        const subtotal = row.children[6].textContent;
        html += "<li style='padding:5px 0; border-bottom:1px solid #eee;'>" + name + " (" + qty + ") - " + subtotal + "</li>";
        total += parseFloat(subtotal.replace("₱","").replace(",",""));
        html += "<input type='hidden' name='checkout_items[]' value='" + cb.value + "'>";
    });
    html += "</ul>";
    checkoutItemsList.innerHTML = html;
    modalTotal.textContent = total.toFixed(2);

    checkoutModal.style.display = "flex";
});

document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function(){
        proofUpload.required = (this.value === 'gcash');
        document.getElementById('proofContainer').style.display = (this.value === 'gcash') ? 'block' : 'none';
        document.getElementById('cashOption').classList.toggle('selected', this.value==='cash');
        document.getElementById('gcashOption').classList.toggle('selected', this.value==='gcash');
    });
});

closeModal.addEventListener('click', () => { checkoutModal.style.display = "none"; });
</script>

<?php else: ?>
<p class="empty">
    Your cart is empty.<br>
    <a href="merchandise.php">Go to Merchandise</a>
</p>
<?php endif; ?>

</body>
</html>
