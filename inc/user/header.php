
<?php


include_once($_SERVER['DOCUMENT_ROOT'] . '/2504C1_PHP/Project/Config/connection.php');

session_start();
// Database connection aur session pehle se include honi chahiye
$cartCount = 0;
$totalPrice = 0.00;

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $userName = $_SESSION['user_name'];

    // 1. Count nikalne ke liye
    $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM cart WHERE user_id = $uid");
    $row = mysqli_fetch_assoc($res);
    $cartCount = $row['total'];

    // 2. Total price nikalne ke liye (Optional par behtar hai)
    $sumRes = mysqli_query($conn, "SELECT SUM(c.quantity * p.price) as grand_total 
                                   FROM cart c JOIN products p ON c.product_id = p.p_id 
                                   WHERE c.user_id = $uid");
    $sumRow = mysqli_fetch_assoc($sumRes);
    $totalPrice = $sumRow['grand_total'] ? $sumRow['grand_total'] : 0.00;
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
    <title>Ogani | Template</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="/2504C1_PHP/Project/assets/user/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/2504C1_PHP/Project/assets/user/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/2504C1_PHP/Project/assets/user/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="/2504C1_PHP/Project/assets/user/css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="/2504C1_PHP/Project/assets/user/css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="/2504C1_PHP/Project/assets/user/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="/2504C1_PHP/Project/assets/user/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="/2504C1_PHP/Project/assets/user/css/style.css" type="text/css">
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Humberger Begin -->
    <div class="humberger__menu__overlay"></div>
    <div class="humberger__menu__wrapper">
        <div class="humberger__menu__logo">
            <a href="#"><img src="img/logo.png" alt=""></a>
        </div>
        <div class="humberger__menu__cart">
            <ul>
                <li><a href="#"><i class="fa fa-heart"></i> <span>0</span></a></li>
                <li><a href="/2504C1_PHP/Project/User/addcart.php"><i class="fa fa-shopping-bag"></i> <span><?php echo $cartCount; ?></span></a></li>
            </ul>
            <div class="header__cart__price">item: <span>RS: <?php echo number_format($totalPrice, 2); ?></span></div>
        </div>
        <div class="humberger__menu__widget">
            <div class="header__top__right__language">
                <img src="img/language.png" alt="">
                <div>English</div>
                <span class="arrow_carrot-down"></span>
                <ul>
                    <li><a href="#">Spanis</a></li>
                    <li><a href="#">English</a></li>
                </ul>
            </div>
            <div class="header__top__right__auth">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="/2504C1_PHP/Project/logout.php"><i class="fa fa-user"></i> <?php echo htmlspecialchars($userName); ?> (Logout)</a>
                <?php else: ?>
                    <a href="/2504C1_PHP/Project/login.php"><i class="fa fa-user"></i> Login</a>
                <?php endif; ?>
            </div>
        </div>
        <nav class="humberger__menu__nav mobile-menu">
            <ul>
                <li><a href="/2504C1_PHP/Project/index.php">Home</a></li>
                <li><a href="/2504C1_PHP/Project/shop-grid.html">Shop</a></li>
                <li><a href="#">Pages</a>
                    <ul class="header__menu__dropdown">
                        <li><a href="/2504C1_PHP/Project/shop-details.html">Shop Details</a></li>
                        <li><a href="/2504C1_PHP/Project/User/addcart.php">Shoping Cart</a></li>
                        <li><a href="/2504C1_PHP/Project/User/checkout.php">Check Out</a></li>
                        <li><a href="/2504C1_PHP/Project/blog-details.html">Blog Details</a></li>
                    </ul>
                </li>
                <li><a href="/2504C1_PHP/Project/blog.html">Blog</a></li>
                <li><a href="/2504C1_PHP/Project/contact.html">Contact</a></li>
            </ul>
        </nav>
        <div id="mobile-menu-wrap"></div>
        <div class="header__top__right__social">
            <a href="#"><i class="fa fa-facebook"></i></a>
            <a href="#"><i class="fa fa-twitter"></i></a>
            <a href="#"><i class="fa fa-linkedin"></i></a>
            <a href="#"><i class="fa fa-pinterest-p"></i></a>
        </div>
        <div class="humberger__menu__contact">
            <ul>
                <li><i class="fa fa-envelope"></i> hello@colorlib.com</li>
                <li>Free Shipping for all Order of $99</li>
            </ul>
        </div>
    </div>
    <!-- Humberger End -->

    <!-- Header Section Begin -->
    <header class="header">
        <div class="header__top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="header__top__left">
                            <ul>
                                <li><i class="fa fa-envelope"></i> hello@colorlib.com</li>
                                <li>Free Shipping for all Order of $99</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="header__top__right">
                            <div class="header__top__right__social">
                                <a href="#"><i class="fa fa-facebook"></i></a>
                                <a href="#"><i class="fa fa-twitter"></i></a>
                                <a href="#"><i class="fa fa-linkedin"></i></a>
                                <a href="#"><i class="fa fa-pinterest-p"></i></a>
                            </div>
                            <div class="header__top__right__language">
                                <img src="img/language.png" alt="">
                                <div>English</div>
                                <span class="arrow_carrot-down"></span>
                                <ul>
                                    <li><a href="#">Spanis</a></li>
                                    <li><a href="#">English</a></li>
                                </ul>
                            </div>
                            <div class="header__top__right__auth">
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <a href="/2504C1_PHP/Project/logout.php"><i class="fa fa-user"></i> <?php echo htmlspecialchars($userName); ?> (Logout)</a>
                                <?php else: ?>
                                    <a href="/2504C1_PHP/Project/login.php"><i class="fa fa-user"></i> Login</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="header__logo">
                        <a href="/2504C1_PHP/Project/index.php"><img src="img/logo.png" alt=""></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <nav class="header__menu">
                        <ul>
                            <li class="active"><a href="/2504C1_PHP/Project/index.php">Home</a></li>
                            <li><a href="/2504C1_PHP/Project/shop-grid.html">Shop</a></li>
                            <li><a href="#">Pages</a>
                                <ul class="header__menu__dropdown">
                                    <li><a href="/2504C1_PHP/Project/shop-details.html">Shop Details</a></li>
                                    <li><a href="/2504C1_PHP/Project/User/addcart.php">Shoping Cart</a></li>
                                    <li><a href="/2504C1_PHP/Project/User/checkout.php">Check Out</a></li>
                                    <li><a href="/2504C1_PHP/Project/blog-details.html">Blog Details</a></li>
                                </ul>
                            </li>
                            <li><a href="/2504C1_PHP/Project/blog.html">Blog</a></li>
                            <li><a href="/2504C1_PHP/Project/contact.html">Contact</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3">
                    <div class="header__cart">
                        <ul>
                            <li><a href="#"><i class="fa fa-heart"></i> <span>0</span></a></li>
                            <li><a href="/2504C1_PHP/Project/User/addcart.php"><i class="fa fa-shopping-bag"></i>
                                    <span><?php echo $cartCount; ?></span></a></li>
                        </ul>
                        <div class="header__cart__price">item: <span>RS:
                                <?php echo number_format($totalPrice, 2); ?></span></div>
                    </div>
                </div>
            </div>
            <div class="humberger__open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>
    <!-- Header Section End -->