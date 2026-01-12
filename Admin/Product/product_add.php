<?php

session_start();
$errors = $_SESSION['error'] ?? [];
unset($_SESSION['error']);
include('../../inc/admin/main.php');
include('../../Config/connection.php');

?>



<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">

  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Forms /</span> Add Product
  </h4>

  <div class="row">
    <div class="col-lg">
      <div class="card mb-4">

        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="mb-0">Product Form</h5>
          <small class="text-muted float-end">Product Details</small>
        </div>

        <div class="card-body">
          <form action="product_insert.php" method="POST" enctype="multipart/form-data">

            <!-- Product Name -->
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Product Name</label>
              <div class="col-sm-10">
                <input type="text" name="product_name" class="form-control" placeholder="Enter Product Name">
                <span style="color:red"><?php echo $errors['product_name'] ?? ''; ?></span>
              </div>
            </div>
             <!-- Product Description -->
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Product Description</label>
              <div class="col-sm-10">
                <input type="text" name="product_description" class="form-control" placeholder="Enter Product Description">
                <span style="color:red"><?php echo $errors['description'] ?? ''; ?></span>
              </div>
            </div>
            <!-- Price -->
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Price</label>
              <div class="col-sm-10">
                <input type="text" name="price" class="form-control" placeholder="Enter Price">
                <span style="color:red"><?php echo $errors['price'] ?? ''; ?></span>
              </div>
            </div>

            <!-- Category -->
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Category</label>
              <div class="col-sm-10">
                <select name="category_id" id="" class="form-control">
                  <option value="">Select Category</option>
                  <?php
                    $query = "select * from categories";
                    $cat = mysqli_query($conn, $query);
                    while($res = mysqli_fetch_assoc($cat)){
                        ?>
                        <option value=" <?php echo $res['id']?>">

                            <?php echo $res['category_name']?>
                        </option>
                    <?php
                    }
                  
                  ?>
                </select>
                <span style="color:red"><?php echo $errors['category_id'] ?? ''; ?></span>
              </div>
            </div>

            <!-- Product Image -->
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Product Image</label>
              <div class="col-sm-10">
                <input type="file" name="image" class="form-control">
                <span style="color:red"><?php echo $errors['image'] ?? ''; ?></span>
              </div>
            </div>

            <!-- Submit -->
            <div class="row justify-content-end">
              <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">Add Product</button>
              </div>
            </div>

          </form>
        </div>

      </div>
    </div>
  </div>
</div>
<!-- / Content -->



<?php include('../../inc/admin/footer.php'); ?>