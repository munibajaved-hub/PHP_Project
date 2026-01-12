<?php  include('../../Config/connection.php');

$id = $_GET['delId']; //10
$query = "Delete from products where p_id = $id";
mysqli_query($conn, $query);

header("Location: product_list.php");
?>

