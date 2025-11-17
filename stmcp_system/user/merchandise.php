<?php
require_once '../connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// -----------------------------
// Handle Add to Cart
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $size = $_POST['size'] ?? 'none';

    if ($quantity > 0) {
        $check = mysqli_query($conn, 
            "SELECT * FROM user_cart 
             WHERE user_id='$user_id' 
             AND product_id='$product_id' 
             AND size='$size'"
        );

        if (mysqli_num_rows($check) > 0) {
            mysqli_query($conn, 
                "UPDATE user_cart 
                 SET quantity = quantity + $quantity 
                 WHERE user_id='$user_id' 
                 AND product_id='$product_id' 
                 AND size='$size'"
            );
        } else {
            mysqli_query($conn, 
                "INSERT INTO user_cart (user_id, product_id, quantity, size) 
                 VALUES ('$user_id', '$product_id', '$quantity', '$size')"
            );
        }

        header("Location: merchandise.php?added=1");
        exit;
    }
}

// -----------------------------
// Fetch Products
// -----------------------------
$products = mysqli_query($conn, "SELECT * FROM products WHERE status='active'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>STMCP Merchandise</title>
<link href="../css/js/userstyle.css" rel="stylesheet">
</head>
<body>

<h2>STMCP Merchandise</h2>

<!-- Navigation Buttons -->
<div class="nav-buttons">
    <a href="cart.php"><button class="nav-cart">Go to Cart</button></a>
    <a href="my_orders.php"><button class="nav-orders">My Orders</button></a>
    <a href="logout.php"><button class="nav-logout">Logout</button></a>
</div>

<div class="product-grid">
<?php if(mysqli_num_rows($products) > 0): ?>
    <?php while($p = mysqli_fetch_assoc($products)): ?>
    <div class="product-card">
        <img src="../uploads/<?= $p['image']; ?>" alt="<?= htmlspecialchars($p['name']); ?>">
        <h3><?= htmlspecialchars($p['name']); ?></h3>
        <p>₱<?= number_format($p['price'],2); ?></p>
        <button class="view-btn" onclick="openModal(<?= $p['id']; ?>)">View Details</button>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center; color:#777;">No products available.</p>
<?php endif; ?>
</div>

<!-- Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span id="modal_close" onclick="closeModal()">&times;</span>
        <form method="POST">
            <input type="hidden" name="product_id" id="modal_product_id">
            <img id="modal_img" src="" alt="">
            <h3 id="modal_name"></h3>
            <p id="modal_price"></p>
            <p id="modal_desc"></p>
            <label>Quantity:</label>
            <input type="number" name="quantity" value="1" min="1" required><br>
            <div id="modal_size_container">
                <label>Size:</label>
                <select name="size" required>
                    <option value="">Select Size</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                </select>
            </div>
            <button type="submit" name="add_to_cart" class="add-cart">Add to Cart</button>
        </form>
    </div>
</div>

<!-- Toast -->
<div id="toast">✅ Item added to cart!</div>

<script>
// Products data for modal
let products = {};
<?php
mysqli_data_seek($products,0);
while($p = mysqli_fetch_assoc($products)){
    $needs_size = stripos($p['name'],'sticker')===false ? 'true':'false';
    echo "products[{$p['id']}] = {
        name: '".addslashes($p['name'])."',
        price: '".number_format($p['price'],2)."',
        desc: '".addslashes($p['description'])."',
        img: '../uploads/{$p['image']}',
        needs_size: $needs_size
    };\n";
}
?>

function openModal(id){
    const data = products[id];
    const modal = document.getElementById('productModal');
    document.getElementById('modal_product_id').value = id;
    document.getElementById('modal_img').src = data.img;
    document.getElementById('modal_name').innerText = data.name;
    document.getElementById('modal_price').innerText = "₱"+data.price;
    document.getElementById('modal_desc').innerText = data.desc;

    const sizeContainer = document.getElementById('modal_size_container');
    if(data.needs_size){
        sizeContainer.style.display = 'block';
        sizeContainer.querySelector('select').required = true;
    } else {
        sizeContainer.style.display = 'none';
        sizeContainer.querySelector('select').required = false;
        sizeContainer.querySelector('select').value = 'none';
    }

    modal.style.display = 'flex';
    setTimeout(()=> modal.style.opacity = 1, 50);
}

function closeModal(){
    const modal = document.getElementById('productModal');
    modal.style.opacity = 0;
    setTimeout(()=> modal.style.display='none', 300);
}

// Toast notification
<?php if(isset($_GET['added'])): ?>
const toast = document.getElementById('toast');
toast.style.display = 'block';
setTimeout(()=> toast.style.opacity = 1, 50);
setTimeout(()=> {
    toast.style.opacity = 0;
    setTimeout(()=> toast.style.display='none', 500);
}, 3000);

// Remove ?added=1 from URL
if(window.history.replaceState){
    let cleanUrl = window.location.href.split('?')[0];
    window.history.replaceState(null,null,cleanUrl);
}
<?php endif; ?>
</script>

</body>
</html>
