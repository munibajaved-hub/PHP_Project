<?php
session_start();
include('../../Config/connection.php');
// Simple validation function for category with htmlspecialchars
function validate_category($category_name){
    $category_name = trim($category_name);               // remove extra spaces
    $category_name = stripslashes($category_name);       // remove slashes
    $category_name = htmlspecialchars($category_name);   // convert special chars

    if(empty($category_name)){
        return "Category name is required";
    }

    if(!preg_match("/^[a-zA-Z ]{3,50}$/", $category_name)){
        return "Only letters allowed (3–50 characters)";
    }

    return true; // validation passed
}

if($_SERVER['REQUEST_METHOD'] != 'POST'){ //get request
    header("Location: category_add.php");
    exit;
}
$id = $_POST['category_id'];
$category_name = $_POST['category_name']; //Fruit

// ✅ Call validation function
$validate = validate_category($category_name);//fruit


if($validate !== true){
    // validation failed → store in session
    $_SESSION['error'] = $validate;
    // $_SESSION['old_category'] = $category_name;
    header("Location: category_add.php");
    exit;
}

// ✅ Success → clear session
unset($_SESSION['error']);


// ✅ Insert into database if valid
$query = "UPDATE categories SET category_name = '$category_name' WHERE id = $id";
mysqli_query($conn, $query);

// ✅ Success → clear session


header("Location: category_List.php");
exit;


?>

?>