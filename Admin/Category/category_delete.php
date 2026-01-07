<?php  include('../../Config/connection.php');

$id = $_GET['delId']; //10
$query = "Delete from categories where id = $id";
mysqli_query($conn, $query);

header("Location: category_List.php");
?>

