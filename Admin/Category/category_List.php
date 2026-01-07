<?php include('../../inc/admin/main.php');
include('../../Config/connection.php');
?>

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Tables /</span> Basic Tables</h4>

  <a href="category_add.php" class="btn btn-primary mb-5">Add Category</a>
    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">Table Basic</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category Id</th>
                        <th>Category Name</th>

                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">

                    <?php
                    $query = "select * from categories";
                    $res = mysqli_query($conn, $query);
                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                    <strong><?php echo $row['category_name']; ?></strong>
                                </td>


                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="category_edit.php?editId=<?php echo $row['id']; ?>">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>

                                            <a class="dropdown-item" href="category_delete.php?delId=<?php echo $row['id']; ?>"
                                                onclick="return confirm('Are you sure you want to delete?');">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </a>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }


                    } else {
                        echo "No data found";
                    }
                    ?>


                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Content -->
<?php include('../../inc/admin/footer.php'); ?>