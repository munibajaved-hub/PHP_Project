<?php
ob_start();

include '../Config/connection.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please login to place an order";
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $_SESSION['error_message'] = "Invalid request method";
    header("Location: checkout.php");
    exit();
}

// Validate required fields
$required_fields = ['first_name', 'last_name', 'country', 'address', 'city', 'state', 'postcode', 'phone', 'email', 'payment_method'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error_message'] = "Please fill all required fields";
        header("Location: checkout.php");
        exit();
    }
}

// Sanitize input data
$first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
$last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
$country = mysqli_real_escape_string($conn, $_POST['country']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$address2 = mysqli_real_escape_string($conn, $_POST['address2'] ?? '');
$city = mysqli_real_escape_string($conn, $_POST['city']);
$state = mysqli_real_escape_string($conn, $_POST['state']);
$postcode = mysqli_real_escape_string($conn, $_POST['postcode']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
$order_notes = mysqli_real_escape_string($conn, $_POST['order_notes'] ?? '');

// Get cart items
$cart_items = mysqli_query($conn, "SELECT c.*, p.product_name, p.price 
              FROM cart c JOIN products p ON c.product_id = p.p_id 
              WHERE c.user_id = $user_id");

if (mysqli_num_rows($cart_items) == 0) {
    $_SESSION['error_message'] = "Your cart is empty";
    header("Location: addcart.php");
    exit();
}

// Calculate total amount
$total_amount = 0;
$cart_products = [];
while ($item = mysqli_fetch_assoc($cart_items)) {
    $subtotal = $item['price'] * $item['quantity'];
    $total_amount += $subtotal;
    $cart_products[] = $item;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Insert into orders table
    $order_query = "INSERT INTO orders (user_id, total_amount, order_status, created_at) 
                   VALUES ($user_id, $total_amount, 'pending', NOW())";
    
    if (!mysqli_query($conn, $order_query)) {
        throw new Exception("Failed to create order: " . mysqli_error($conn));
    }
    
    $order_id = mysqli_insert_id($conn); //1
    
    // Insert order items
    foreach ($cart_products as $product) {
        $product_id = $product['product_id'];
        $price = $product['price'];
        $quantity = $product['quantity'];
        
        $order_item_query = "INSERT INTO order_items (order_id, product_id, price, quantity) 
                            VALUES ($order_id, $product_id, $price, $quantity)";
        
        if (!mysqli_query($conn, $order_item_query)) {
            throw new Exception("Failed to add order item: " . mysqli_error($conn));
        }
    }
    
    // Store billing information (you might want to create a separate table for this)
    // For now, we'll store it in session for confirmation page
    $_SESSION['billing_info'] = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'country' => $country,
        'address' => $address,
        'address2' => $address2,
        'city' => $city,
        'state' => $state,
        'postcode' => $postcode,
        'phone' => $phone,
        'email' => $email,
        'payment_method' => $payment_method,
        'order_notes' => $order_notes
    ];
    
    // Clear cart after successful order
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Store order details in session for confirmation page
    $_SESSION['order_id'] = $order_id;
    $_SESSION['total_amount'] = $total_amount;
    $_SESSION['order_products'] = $cart_products;
    
    $_SESSION['success_message'] = "Order placed successfully! Order ID: #" . $order_id;
    
    // Redirect to order confirmation page
    header("Location: order_confirmation.php");
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    $_SESSION['error_message'] = "Order failed: " . $e->getMessage();
    header("Location: checkout.php");
    exit();
}
?>