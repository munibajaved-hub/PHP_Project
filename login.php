<?php

session_start();
include ('Config/connection.php');
$error = "";

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "select * from user where email  = '$email' and status = 1";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user['password'])) {

            $_SESSION['id'] = $user['id'];
            
            $_SESSION['role'] = $user['role'];

            if($user['role'] == 'customer') {
                header("Location: index.php");
            }
            else{
                header("Location: Admin/index.php");
            }
            
        }

    }
    else{
        echo "User Not Found";
    }
 
}

?>


<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ogani | Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/user/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="assets/user/css/style.css" type="text/css">

    <style>
        /* Agar loader screen se nahi hat raha toh ye force hide kar dega */
        #preloder { display: none !important; }

        .login-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
        }
        .login-form {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
        .login-form h4 {
            text-align: center;
            margin-bottom: 30px;
            color: #7fad39;
            font-weight: 700;
        }
        /* Bootstrap input-group fix */
        .input-group {
            margin-bottom: 20px;
            display: flex !important;
        }
        .input-group-addon {
            background: #f5f5f5;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            border-right: none;
            display: flex;
            align-items: center;
        }
        .login-form .form-control {
            border-left: none !important;
            height: 45px;
        }
        .site-btn {
            width: 100%;
            background: #7fad39;
            color: #fff;
            border: none;
            padding: 12px;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <section class="login-section">
        <div class="login-form">
            <h4>Login to Your Account</h4>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Email" required name = "email">
                </div>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required name = "password">
                </div>
                <div class="checkout__input__checkbox">
                    <label for="remember">
                        Remember me
                        <input type="checkbox" id="remember">
                        <span class="checkmark"></span>
                    </label>
                </div>
                <button type="submit" name="login" class="site-btn">LOGIN</button>
                <p>Don't have an account? <a href="register.php" style="color:#7fad39;">Register here</a></p>
            </form>
        </div>
    </section>

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