<?php
session_start();
// Errors array ko handle karne ke liye
$errors = $_SESSION['error'] ?? [];
unset($_SESSION['error']);

include('../../inc/admin/main.php');
include('../../Config/connection.php');

// URL se Product ID uthana
$id = $_GET['editId'];

// Product ka purana data fetch karna
$query = "SELECT * FROM products WHERE p_id = $id";
$res = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($res);

// Agar product nahi milta
if (!$product) {
    echo "Product not found!";
    exit;
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms/</span> Edit Product</h4>

    <div class="row">
        <div class="col-lg">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Product Details</h5>
                    <small class="text-muted float-end">Update Information</small>
                </div>
                <div class="card-body">
                    <form action="product_update.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?php echo $product['p_id']; ?>">

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Product Name</label>
                            <div class="col-sm-10">
                                <input type="text" name="product_name" class="form-control" value="<?php echo $product['product_name']; ?>" />
                                <span class="text-danger"><?php echo $errors['product_name'] ?? ''; ?></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Description</label>
                            <div class="col-sm-10">
                                <textarea name="product_description" class="form-control"><?php echo $product['description']; ?></textarea>
                                <span class="text-danger"><?php echo $errors['description'] ?? ''; ?></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Price</label>
                            <div class="col-sm-10">
                                <input type="number" name="price" class="form-control" value="<?php echo $product['price']; ?>" />
                                <span class="text-danger"><?php echo $errors['price'] ?? ''; ?></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Category</label>
                            <div class="col-sm-10">
                                <select name="category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    <?php
                                    $cat_query = "SELECT * FROM categories";
                                    $cat_res = mysqli_query($conn, $cat_query);
                                    while ($cat = mysqli_fetch_assoc($cat_res)) {
                                        $selected = ($cat['id'] == $product['category_id']) ? "selected" : "";
                                        echo "<option value='{$cat['id']}' $selected>{$cat['category_name']}</option>";
                                    }
                                    ?>
                                </select>
                                <span class="text-danger"><?php echo $errors['category_id'] ?? ''; ?></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Product Image</label>
                            <div class="col-sm-10">
                                <div class="mb-2">
                                    <img src="../../uploads/<?php echo $product['image']; ?>" width="100" class="rounded shadow-sm">
                                </div>
                                <input type="file" name="image" class="form-control" />
                                <small class="text-muted">Nayi image upload karein ya purani rehne dein.</small>
                                <span class="text-danger"><?php echo $errors['image'] ?? ''; ?></span>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Update Product</button>
                                <a href="product_list.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../inc/admin/footer.php'); ?>