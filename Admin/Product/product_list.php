<?php
session_start();
include('../../inc/admin/main.php');
include('../../Config/connection.php');
?>

<!-- Dismissible Alerts -->
<div class="col-md">
  
            <div class="alert alert-primary alert-dismissible" role="alert">
                <?php echo $_SESSION['success'] ?? ''; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
      
</div>
<!--/ Dismissible Alerts -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Tables /</span> Product List
    </h4>

    <a href="product_add.php" class="btn btn-primary mb-5">Add Product</a>

    <div class="card">
        <h5 class="card-header">Manage Products</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    // JOIN query taaki category ID ki jagah category name show ho
                    $query = "SELECT *
                              FROM products 
                              LEFT JOIN categories ON products.category_id = categories.id
                              ORDER BY products.p_id DESC";

                    $res = mysqli_query($conn, $query);

                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            ?>
                            <tr>
                                <td>
                                    <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                        <li class="avatar avatar-xs pull-up">
                                            <img src="../../uploads/<?php echo $row['image']; ?>" alt="Product"
                                                class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        </li>
                                    </ul>
                                </td>
                                <td><strong><?php echo $row['product_name']; ?></strong></td>
                                <td><span class="badge bg-label-info me-1"><?php echo $row['category_name']; ?></span></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo substr($row['description'], 0, 30); ?>...</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="product_edit.php?editId=<?php echo $row['p_id']; ?>">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item" href="product_delete.php?delId=<?php echo $row['p_id']; ?>"
                                                onclick="return confirm('Are you sure you want to delete this product?');">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No products found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include('../../inc/admin/footer.php'); ?>