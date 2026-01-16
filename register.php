<?php
session_start();
include ('Config/connection.php');

$errors = [];

if (isset($_POST['register'])) {

    $name  = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    /* ================= VALIDATIONS ================= */

    if (empty($name)) {
        $errors['name'] = "Full name is required";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    /* ========== CHECK EMAIL EXISTS (only if email entered) ========== */

    if (!empty($email)) {
        $check_email = "SELECT id FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);

        if (mysqli_num_rows($result) > 0) {
            $errors['email'] = "Email already exists";
        }
    }

    /* ================= INSERT DATA ================= */

    if (empty($errors)) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $insert = "INSERT INTO user (full_name, email, password)
                   VALUES ('$name', '$email', '$hash')";

        mysqli_query($conn, $insert);

        $_SESSION['success'] = "Registration successful";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Ogani Template">
    <meta name="keywords" content="Ogani, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ogani | Register</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="assets/user/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/style.css" type="text/css">
    <style>
        .register-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
        }
        .register-form {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
        .register-form h4 {
            text-align: center;
            margin-bottom: 30px;
            color: #7fad39;
        }
        .register-form .input-group {
            margin-bottom: 20px;
        }
        .register-form .input-group-addon {
            background: #f5f5f5;
            border: none;
            padding: 10px;
        }
        .register-form input {
            border-left: none;
        }
        .register-form .site-btn {
            width: 100%;
            margin-top: 10px;
        }
        .register-form p {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Register Section Begin -->
    <section class="register-section">
        <div class="register-form">
            <h4>Create an Account</h4>
            <form action="register.php" method = "post">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" class="form-control" placeholder="Full Name" name = "full_name">
                </div>
                <span style="color:red"><?php echo $errors['name'] ?? ''; ?></span>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                    <input type="email" class="form-control" placeholder="Email" name = "email">
                </div>
                <span style="color:red"><?php echo $errors['email'] ?? ''; ?></span>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                    <input type="password" class="form-control" placeholder="Password"  name = "password">
                </div>
                <span style="color:red"><?php echo $errors['password'] ?? ''; ?></span>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                    <input type="password" class="form-control" placeholder="Confirm Password"  name = "confirm_password">
                </div>
                <span style="color:red"><?php echo $errors['confirm_password']  ?? ''; ?></span>
                <button type="submit" class="site-btn" name = "register">REGISTER</button>
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </section>
    <!-- Register Section End -->

    <!-- Js Plugins -->
    <script src="assets/user/js/jquery-3.3.1.min.js"></script>
    <script src="assets/user/js/bootstrap.min.js"></script>
    <script src="assets/user/js/jquery.nice-select.min.js"></script>
    <script src="assets/user/js/jquery-ui.min.js"></script>
    <script src="assets/user/js/jquery.slicknav.js"></script>
    <script src="assets/user/js/mixitup.min.js"></script>
    <script src="assets/user/js/owl.carousel.min.js"></script>
    <script src="assets/user/js/main.js"></script>

</body>

</html>