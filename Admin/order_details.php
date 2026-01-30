<?php
include '../Config/connection.php';

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<div class="alert alert-danger">Order ID not provided</div>';
    exit();
}

$order_id = (int)$_GET['id'];

// Get order details with user information
$order_query = "SELECT o.*, u.full_name as username, u.email as user_email
               FROM orders o 
               LEFT JOIN user u ON o.user_id = u.id 
               WHERE o.id = $order_id";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    echo '<div class="alert alert-danger">Order not found</div>';
    exit();
}

// Get order items with product details
$items_query = "SELECT oi.*, p.product_name, p.image
               FROM order_items oi 
               LEFT JOIN products p ON oi.product_id = p.p_id 
               WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);
?>

<div class="order-details">
    <!-- Order Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6>Order Information</h6>
            <table class="table table-sm">
                <tr>
                    <td><strong>Order ID:</strong></td>
                    <td>#<?php echo $order['id']; ?></td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td><?php echo date('M d, Y H:i A', strtotime($order['created_at'])); ?></td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                        <span class="badge bg-<?php 
                            echo match($order['order_status']) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'shipped' => 'info',
                                'cancelled' => 'danger',
                                default => 'secondary'
                            };
                        ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Total Amount:</strong></td>
                    <td><strong>RS <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6>Customer Information</h6>
            <table class="table table-sm">
                <tr>
                    <td><strong>Name:</strong></td>
                    <td><?php echo htmlspecialchars($order['username'] ?? 'Guest User'); ?></td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td><?php echo htmlspecialchars($order['user_email'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td><strong>Phone:</strong></td>
                    <td>N/A</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Order Items -->
    <h6>Order Items</h6>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($items_result) > 0): ?>
                    <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($item['image']): ?>
                                        <img src="../uploads/<?php echo $item['image']; ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                             class="rounded me-3" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="bx bx-image text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td>RS <?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><strong>RS <?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No items found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total:</th>
                    <th><strong>RS <?php echo number_format($order['total_amount'], 2); ?></strong></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Order Actions -->
    <div class="mt-4">
        <div class="row">
            <div class="col-md-6">
                <h6>Update Order Status</h6>
                <form method="POST" action="orders.php" id="updateStatusForm">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <input type="hidden" name="update_status" value="1">
                    <div class="mb-3">
                        <select name="status" class="form-select" required>
                            <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $order['order_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-refresh"></i> Update Status
                    </button>
                </form>
            </div>
            <div class="col-md-6">
                <h6>Quick Actions</h6>
                <div class="d-grid gap-2">
                    <button class="btn btn-success" onclick="testQuickUpdate(<?php echo $order['id']; ?>, 'paid')">
                        <i class="bx bx-check-circle"></i> Mark as Paid
                    </button>
                    <button class="btn btn-info" onclick="testQuickUpdate(<?php echo $order['id']; ?>, 'shipped')">
                        <i class="bx bx-package"></i> Mark as Shipped
                    </button>
                    <button class="btn btn-danger" onclick="testQuickUpdate(<?php echo $order['id']; ?>, 'cancelled')">
                        <i class="bx bx-x-circle"></i> Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testQuickUpdate(orderId, status) {
    alert('Button clicked! Order ID: ' + orderId + ', Status: ' + status);
    
    // Try multiple ways to find the dropdown
    let statusSelect = null;
    
    // Method 1: Standard query
    statusSelect = document.querySelector('select[name="status"]');
    alert('Method 1 found: ' + (statusSelect ? 'Yes' : 'No'));
    
    // Method 2: By ID if exists
    if (!statusSelect) {
        statusSelect = document.getElementById('status');
        alert('Method 2 found: ' + (statusSelect ? 'Yes' : 'No'));
    }
    
    // Method 3: By class
    if (!statusSelect) {
        statusSelect = document.querySelector('.form-select');
        alert('Method 3 found: ' + (statusSelect ? 'Yes' : 'No'));
    }
    
    // Method 4: Inside modal
    if (!statusSelect) {
        const modal = document.querySelector('.modal-body');
        if (modal) {
            statusSelect = modal.querySelector('select[name="status"]');
            alert('Method 4 found: ' + (statusSelect ? 'Yes' : 'No'));
        }
    }
    
    if (statusSelect) {
        statusSelect.value = status;
        alert('Dropdown updated to: ' + status + ' | Current value: ' + statusSelect.value);
    } else {
        alert('Dropdown not found! Debugging...');
        // Log all select elements
        const allSelects = document.querySelectorAll('select');
        alert('Total select elements found: ' + allSelects.length);
        for (let i = 0; i < allSelects.length; i++) {
            alert('Select ' + i + ': name=' + allSelects[i].name + ' id=' + allSelects[i].id);
        }
    }
    
    // Simple form submission
    if (confirm('Update order status to ' + status + '?')) {
        // Create form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../orders.php';  // Try relative path
        
        const input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = 'order_id';
        input1.value = orderId;
        
        const input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'status';
        input2.value = status;
        
        const input3 = document.createElement('input');
        input3.type = 'hidden';
        input3.name = 'update_status';
        input3.value = '1';
        
        form.appendChild(input1);
        form.appendChild(input2);
        form.appendChild(input3);
        
        document.body.appendChild(form);
        alert('Form created, submitting...');
        form.submit();
    }
}

function quickUpdateOrderStatus(orderId, status) {
    console.log('Function called with orderId:', orderId, 'status:', status);
    
    // Update the dropdown value
    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        statusSelect.value = status;
        console.log('Dropdown updated to:', status);
    }
    
    // Show confirmation
    if (confirm('Update order status to ' + status + '?')) {
        console.log('Creating and submitting form...');
        
        // Create a new form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'orders.php';
        
        // Add order_id
        const orderIdInput = document.createElement('input');
        orderIdInput.type = 'hidden';
        orderIdInput.name = 'order_id';
        orderIdInput.value = orderId;
        
        // Add status
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        
        // Add update_status
        const updateInput = document.createElement('input');
        updateInput.type = 'hidden';
        updateInput.name = 'update_status';
        updateInput.value = '1';
        
        form.appendChild(orderIdInput);
        form.appendChild(statusInput);
        form.appendChild(updateInput);
        
        document.body.appendChild(form);
        form.submit();
    } else {
        console.log('User cancelled the update');
    }
}
</script>
