<?php
session_start();
include('../../Config/connection.php');

// --- Validation Functions ---
function validate_product_name($product_name){
    $product_name = trim($product_name);
    if(empty($product_name)) return "Product name is required";
    if(!preg_match("/^[a-zA-Z0-9 ]{3,100}$/", $product_name)) return "Only letters and numbers allowed (3–100 characters)";
    return true;
}

function validate_price($price){
    $price = trim($price);
    if(empty($price)) return "Price is required";
    if(!is_numeric($price) || $price <= 0) return "Price must be a number greater than 0";
    return true;
}

function validate_description($description){
    $description = trim($description);
    if(empty($description)) return "Description is required";
    if(strlen($description) > 250) return "Description must be less than 250 characters";
    return true;
}

// --- Logic Flow ---
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: product_add.php");
    exit;
}

// Initialize session error array
$_SESSION['error'] = [];

//values get from form
$product_name = $_POST['product_name'] ?? '';
$price = $_POST['price'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$description = $_POST['product_description'] ?? '';

// Check Name
$res_name = validate_product_name($product_name);
if($res_name !== true) $_SESSION['error']['product_name'] = $res_name;

// Check Price
$res_price = validate_price($price);
if($res_price !== true) $_SESSION['error']['price'] = $res_price;

// Check Description
$res_desc = validate_description($description);
if($res_desc !== true) $_SESSION['error']['description'] = $res_desc;

// Check Category
if(empty($category_id)) $_SESSION['error']['category_id'] = "Category is required";

// Check Image
$new_name = ""; // placeholder
if(isset($_FILES['image']) && $_FILES['image']['error'] !== 4) {
    $image = $_FILES['image'];
    $allowed = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

    if(!in_array($ext, $allowed)) {
        $_SESSION['error']['image'] = "Only jpg, jpeg, png and gif allowed";
    } elseif($image['size'] > 2 * 1024 * 1024) {
        $_SESSION['error']['image'] = "Image must be less than 2MB";
    } else {
        $new_name = time().'_'.$image['name'];
    }
} else {
    $_SESSION['error']['image'] = "Image is required";
}

// --- THE FIX: If any errors exist, redirect back ONCE ---
if(!empty($_SESSION['error'])){
    header("Location: product_add.php");
    exit;
}

// --- Success Path ---
// Move file only if validation passed
move_uploaded_file($_FILES['image']['tmp_name'], '../../uploads/'.$new_name);



$query = "INSERT INTO products (product_name, price, category_id, description, image) 
          VALUES ('$product_name', '$price', '$category_id', '$description', '$new_name')";

if(mysqli_query($conn, $query)){
    $_SESSION['success'] = "Product added successfully!";
    header("Location: product_list.php"); // Or your product list page
} else {
    $_SESSION['error']['db'] = "Database error: " . mysqli_error($conn);
    header("Location: product_add.php");
}
exit;