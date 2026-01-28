<?php
session_start();
include '../Config/connection.php';

// 1. Simple Access Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $p_id    = (int)$_POST['product_id'];
    $qty     = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // 2. Check if already exists
    $check = mysqli_query($conn, "SELECT id FROM cart WHERE user_id = $user_id AND product_id = $p_id");

    if (mysqli_num_rows($check) > 0) {
        // Update existing
        $sql = "UPDATE cart SET quantity = quantity + $qty WHERE user_id = $user_id AND product_id = $p_id";
    } else {
        // Insert new
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $p_id, $qty)";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Cart updated!";
        header("Location: addcart.php"); // Redirect to cart page
        exit(); // Hamesha exit() lagayen redirect ke baad
    } else {
        $_SESSION['error_message'] = "Database Error";
    }
}

// Default redirect agar POST na ho ya error aaye
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();