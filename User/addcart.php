<?php
ob_start(); 

include '../Config/connection.php';




include '../inc/user/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = mysqli_query($conn, "SELECT c.*, p.product_name, p.price, p.image 
              FROM cart c JOIN products p ON c.product_id = p.p_id 
              WHERE c.user_id = $user_id");
$total_amount = 0;

// --- 2. HANDLE REMOVAL ---
if (isset($_GET['remove_id'])) {
    $user_id = $_SESSION['user_id'];
    $remove_id = (int)$_GET['remove_id'];
    mysqli_query($conn, "DELETE FROM cart WHERE id = $remove_id AND user_id = $user_id");
    header("Location: addcart.php");
    exit();
}

// --- 1. AJAX REAL-TIME UPDATE (New Logic) ---
if (isset($_POST['ajax_update'])) {
    $user_id = $_SESSION['user_id'];
    $cart_id = (int)$_POST['cart_id'];
    $qty = (int)$_POST['qty'];

    if ($qty > 0) {
        $update_sql = "UPDATE cart SET quantity = $qty WHERE id = $cart_id AND user_id = $user_id";
        if(mysqli_query($conn, $update_sql)) {
            echo "Success";
        }
    }
    exit(); // Background request ke baad yahan stop ho jaye
}

?>

<section class="shoping-cart spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="shoping__cart__table">
                    <table>
                        <thead>
                            <tr>
                                <th class="shoping__product">Products</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($cart_items) > 0): ?>
                                <?php while ($item = mysqli_fetch_assoc($cart_items)): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $total_amount += $subtotal;
                                ?>
                                    <tr>
                                        <td class="shoping__cart__item">
                                            <img src="../uploads/<?php echo $item['image']; ?>" width="80">
                                            <h5><?php echo $item['product_name']; ?></h5>
                                        </td>
                                        <td class="shoping__cart__price">
                                            RS: <span class="unit-price"><?php echo $item['price']; ?></span>
                                        </td>
                                        <td class="shoping__cart__quantity">
                                            <div class="quantity">
                                                <div class="pro-qty">
                                                    <input type="text" 
                                                           data-id="<?php echo $item['id']; ?>" 
                                                           value="<?php echo $item['quantity']; ?>" 
                                                           class="cart-qty-input" readonly>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="shoping__cart__total">
                                            RS: <span class="row-total"><?php echo number_format($subtotal, 2, '.', ''); ?></span>
                                        </td>
                                        <td class="shoping__cart__item__close">
                                            <a href="addcart.php?remove_id=<?php echo $item['id']; ?>" 
                                               onclick="return confirm('Remove this item?')" class="icon_close"></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">Your cart is empty</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="shoping__cart__btns">
                    <a href="../index.php" class="primary-btn cart-btn">CONTINUE SHOPPING</a>
                </div>
            </div>
            <div class="col-lg-6 offset-lg-6">
                <div class="shoping__checkout">
                    <h5>Cart Total</h5>
                    <ul>
                        <li>Total <span>RS: <span id="grand-total"><?php echo number_format($total_amount, 2, '.', ''); ?></span></span></li>
                    </ul>
                    <a href="checkout.php" class="primary-btn">PROCEED TO CHECKOUT</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../inc/user/footer.php'; ?>

<script>
$(document).ready(function() {
    // Jab plus ya minus button click ho
    $('.pro-qty').on('click', '.qtybtn', function() {
        var $button = $(this);
        var $input = $button.parent().find('input');
        
        // 150ms ka delay taake Ogani theme ki apni JS pehle value change karle
        setTimeout(function() {
            var newVal = $input.val();
            var cartId = $input.data('id');

            // --- 1. AJAX call database update karne ke liye ---
            $.ajax({
                url: 'addcart.php',
                method: 'POST',
                data: {
                    ajax_update: 1,
                    cart_id: cartId,
                    qty: newVal
                },
                success: function(response) {
                    console.log("Database Sync: " + response);
                }
            });

            // --- 2. Real-time screen update ---
            updateTotal($input);
            
        }, 150);
    });

    function updateTotal(inputElement) {
        var qty = parseInt(inputElement.val()) || 1;
        var price = parseFloat(inputElement.closest('tr').find('.unit-price').text());
        var rowTotal = qty * price;

        inputElement.closest('tr').find('.row-total').text(rowTotal.toFixed(2));

        var grandTotal = 0;
        $('.row-total').each(function() {
            grandTotal += parseFloat($(this).text());
        });
        $('#grand-total').text(grandTotal.toFixed(2));
    }
});
</script>