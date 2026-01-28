<?php
ob_start();

include '../Config/connection.php';
include '../inc/user/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if order details exist in session
if (!isset($_SESSION['order_id']) || !isset($_SESSION['total_amount'])) {
    header("Location: addcart.php");
    exit();
}

$order_id = $_SESSION['order_id'];
$total_amount = $_SESSION['total_amount'];
$order_products = $_SESSION['order_products'] ?? [];
$billing_info = $_SESSION['billing_info'] ?? [];

// Clear order details from session after displaying
unset($_SESSION['order_id']);
unset($_SESSION['total_amount']);
unset($_SESSION['order_products']);
unset($_SESSION['billing_info']);
?>

<!-- Hero Section Begin -->
<section class="hero hero-normal">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="hero__categories">
                    <div class="hero__categories__all">
                        <i class="fa fa-bars"></i>
                        <span>All departments</span>
                    </div>
                    <ul>
                        <li><a href="#">Fresh Meat</a></li>
                        <li><a href="#">Vegetables</a></li>
                        <li><a href="#">Fruit & Nut Gifts</a></li>
                        <li><a href="#">Fresh Berries</a></li>
                        <li><a href="#">Ocean Foods</a></li>
                        <li><a href="#">Butter & Eggs</a></li>
                        <li><a href="#">Fastfood</a></li>
                        <li><a href="#">Fresh Onion</a></li>
                        <li><a href="#">Papayaya & Crisps</a></li>
                        <li><a href="#">Oatmeal</a></li>
                        <li><a href="#">Fresh Bananas</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="hero__search">
                    <div class="hero__search__form">
                        <form action="#">
                            <div class="hero__search__categories">
                                All Categories
                                <span class="arrow_carrot-down"></span>
                            </div>
                            <input type="text" placeholder="What do you need?">
                            <button type="submit" class="site-btn">SEARCH</button>
                        </form>
                    </div>
                    <div class="hero__search__phone">
                        <div class="hero__search__phone__icon">
                            <i class="fa fa-phone"></i>
                        </div>
                        <div class="hero__search__phone__text">
                            <h5>+65 11.188.888</h5>
                            <span>support 24/7 time</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Hero Section End -->

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>Order Confirmation</h2>
                    <div class="breadcrumb__option">
                        <a href="../index.php">Home</a>
                        <span>Order Confirmation</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Checkout Section Begin -->
<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="checkout__form">
                    <div class="text-center mb-4">
                        <i class="fa fa-check-circle" style="font-size: 64px; color: #28a745; margin-bottom: 20px;"></i>
                        <h2>Thank You For Your Order!</h2>
                        <p class="lead">Your order has been placed successfully.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="checkout__order">
                                <h4>Order Details</h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td><strong>Order ID:</strong></td>
                                            <td>#<?php echo $order_id; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td><?php echo ucfirst($billing_info['payment_method'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Order Status:</strong></td>
                                            <td><span class="badge badge-warning">Pending</span></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <h4 class="mt-4">Billing Information</h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td><?php echo htmlspecialchars($billing_info['first_name'] . ' ' . $billing_info['last_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td><?php echo htmlspecialchars($billing_info['email']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td><?php echo htmlspecialchars($billing_info['phone']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td>
                                                <?php 
                                                    echo htmlspecialchars($billing_info['address']);
                                                    if (!empty($billing_info['address2'])) {
                                                        echo ', ' . htmlspecialchars($billing_info['address2']);
                                                    }
                                                    echo '<br>' . htmlspecialchars($billing_info['city'] . ', ' . $billing_info['state']);
                                                    echo '<br>' . htmlspecialchars($billing_info['postcode'] . ', ' . $billing_info['country']);
                                                ?>
                                            </td>
                                        </tr>
                                        <?php if (!empty($billing_info['order_notes'])): ?>
                                        <tr>
                                            <td><strong>Order Notes:</strong></td>
                                            <td><?php echo htmlspecialchars($billing_info['order_notes']); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="checkout__order">
                                <h4>Order Summary</h4>
                                <div class="checkout__order__products">Products <span>Total</span></div>
                                <ul>
                                    <?php foreach ($order_products as $product): ?>
                                        <li><?php echo htmlspecialchars($product['product_name']); ?> 
                                            <span>RS: <?php echo number_format($product['price'] * $product['quantity'], 2); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="checkout__order__subtotal">Subtotal <span>RS: <?php echo number_format($total_amount, 2); ?></span></div>
                                <div class="checkout__order__total">Total <span>RS: <?php echo number_format($total_amount, 2); ?></span></div>
                                
                                <div class="mt-3">
                                    <a href="../index.php" class="primary-btn">Continue Shopping</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Checkout Section End -->

<?php include '../inc/user/footer.php'; ?>

<style>
.badge {
    display: inline-block;
    padding: .25em .4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25rem;
}
.badge-warning {
    color: #212529;
    background-color: #ffc107;
}
.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
}
.table td {
    padding: .75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
.table-responsive {
    display: block;
    width: 100%;
    overflow-x: auto;
}
.lead {
    font-size: 1.25rem;
    font-weight: 300;
}
</style>