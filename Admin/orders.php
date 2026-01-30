<?php
include_once '../Config/connection.php';

// Handle status update - must be before any output
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_query = "UPDATE orders SET order_status = '$new_status' WHERE id = $order_id";
    if (mysqli_query($conn, $update_query)) {
        header("Location: orders.php?success=1");
        exit();
    } else {
        $error_message = "Failed to update order status";
    }
}

include '../inc/admin/main.php';

// Get order statistics
$stats_query = "SELECT 
                COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_orders,
                COUNT(CASE WHEN order_status = 'paid' THEN 1 END) as paid_orders,
                COUNT(CASE WHEN order_status = 'shipped' THEN 1 END) as shipped_orders,
                COUNT(CASE WHEN order_status = 'cancelled' THEN 1 END) as cancelled_orders,
                SUM(total_amount) as total_revenue,
                COUNT(*) as total_orders
                FROM orders";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Get orders with user and product details
$orders_query = "SELECT o.*, u.full_name as username, u.email as user_email,
                 COUNT(oi.id) as item_count,
                 GROUP_CONCAT(CONCAT(p.product_name, ' (', oi.quantity, ' x ', oi.price, ')') SEPARATOR ', ') as products
                 FROM orders o 
                 LEFT JOIN user u ON o.user_id = u.id 
                 LEFT JOIN order_items oi ON o.id = oi.order_id
                 LEFT JOIN products p ON oi.product_id = p.p_id
                 GROUP BY o.id 
                 ORDER BY o.created_at DESC";
$orders_result = mysqli_query($conn, $orders_query);
?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Management</h4>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0"><?php echo $stats['pending_orders']; ?></h4>
                                            <p>Pending Orders</p>
                                        </div>
                                        <div class="avatar">
                                            <i class="bx bx-time-five fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0"><?php echo $stats['paid_orders']; ?></h4>
                                            <p>Paid Orders</p>
                                        </div>
                                        <div class="avatar">
                                            <i class="bx bx-check-circle fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0"><?php echo $stats['shipped_orders']; ?></h4>
                                            <p>Shipped Orders</p>
                                        </div>
                                        <div class="avatar">
                                            <i class="bx bx-package fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">RS <?php echo number_format($stats['total_revenue'], 2); ?></h4>
                                            <p>Total Revenue</p>
                                        </div>
                                        <div class="avatar">
                                            <i class="bx bx-dollar-circle fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible">
                            Order status updated successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Orders Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Products</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($orders_result) > 0): ?>
                                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($order['user_email'] ?? 'N/A'); ?></small>
                                            </td>
                                            <td>
                                                <small><?php echo htmlspecialchars(substr($order['products'], 0, 100)); ?>...</small><br>
                                                <span class="badge bg-secondary"><?php echo $order['item_count']; ?> items</span>
                                            </td>
                                            <td><strong>RS <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <form method="POST" action="orders.php" id="statusForm<?php echo $order['id']; ?>">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                        <select name="status" class="form-select form-select-sm" onchange="updateOrderStatus(<?php echo $order['id']; ?>, this.value)">
                                                            <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="paid" <?php echo $order['order_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                                            <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                            <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                        </select>
                                                        <input type="hidden" name="update_status" value="1">
                                                    </form>
                                                </div>
                                                <div class="mt-1">
                                                    <?php if ($order['order_status'] == 'pending'): ?>
                                                        <button class="btn btn-xs btn-success" onclick="quickUpdate(<?php echo $order['id']; ?>, 'paid')">
                                                            <i class="bx bx-check"></i> Approve
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($order['order_status'] == 'paid'): ?>
                                                        <button class="btn btn-xs btn-info" onclick="quickUpdate(<?php echo $order['id']; ?>, 'shipped')">
                                                            <i class="bx bx-package"></i> Ship
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($order['order_status'] != 'cancelled'): ?>
                                                        <button class="btn btn-xs btn-danger" onclick="quickUpdate(<?php echo $order['id']; ?>, 'cancelled')">
                                                            <i class="bx bx-x"></i> Cancel
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                                    <i class="bx bx-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No orders found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function updateOrderStatus(orderId, newStatus) {
    if (confirm('Update order status to ' + newStatus + '?')) {
        document.getElementById('statusForm' + orderId).submit();
    }
}

function quickUpdate(orderId, newStatus) {
    if (confirm('Update order status to ' + newStatus + '?')) {
        // Create a form dynamically
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'orders.php';
        
        const orderIdInput = document.createElement('input');
        orderIdInput.type = 'hidden';
        orderIdInput.name = 'order_id';
        orderIdInput.value = orderId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = newStatus;
        
        const updateInput = document.createElement('input');
        updateInput.type = 'hidden';
        updateInput.name = 'update_status';
        updateInput.value = '1';
        
        form.appendChild(orderIdInput);
        form.appendChild(statusInput);
        form.appendChild(updateInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function viewOrderDetails(orderId) {
    // You can implement AJAX to load order details or create a separate page
    // For now, let's show a simple message
    document.getElementById('orderDetailsContent').innerHTML = `
        <div class="text-center">
            <i class="bx bx-loader bx-spin fs-2"></i>
            <p>Loading order details...</p>
        </div>
    `;
    
    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    modal.show();
    
    // Load order details via AJAX
    fetch('order_details.php?id=' + orderId)
        .then(response => response.text())
        .then(html => {
            document.getElementById('orderDetailsContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('orderDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    Failed to load order details. Please try again.
                </div>
            `;
        });
}
</script>

<?php include '../inc/admin/footer.php'; ?>
