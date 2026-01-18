<?php
include('database.php'); // เชื่อมต่อกับฐานข้อมูล

if (!isset($_GET['user_id'])) {
    echo "Invalid Barber";
    exit();
}

$barber_id = $_GET['user_id'];
$sql = "SELECT name, email, phone_number, bio FROM users WHERE user_id = ? AND role = 'barber'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $barber_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $barber = $result->fetch_assoc();
} else {
    echo "Barber not found!";
    exit();
}

// ดึงข้อมูลผู้ใช้จาก session
session_start();
$user_id = $_SESSION['user_id']; // user_id จาก session
$sql_user = "SELECT name, email, role, phone_number FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
} else {
    echo "User not found!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barber Details</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
<!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap"rel="stylesheet" />
<!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
<!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />
    <style>
        /* เพิ่มสไตล์เพิ่มเติมจากโค้ด 1 */
        body {
            background-color: #f9f9fa;
            font-family: Arial, sans-serif;
        }
        .padding {
            padding: 3rem !important;
        }
        .user-card-full {
            overflow: hidden;
        }
        .card {
            border-radius: 5px;
            box-shadow: 0 1px 20px 0 rgba(69,90,100,0.08);
            border: none;
            margin-bottom: 30px;
        }
        .user-profile {
            padding: 20px 0;
            background: linear-gradient(to right, #f1db25, #f1db25);
            color: white;
            text-align: center;
        }
        .user-profile img {
            border-radius: 5px;
            width: 100px;
        }
        .card-block {
            padding: 1.25rem;
        }
        .f-w-600 {
            font-weight: 600;
        }
        .text-muted {
            color: #919aa3 !important;
        }
        .b-b-default {
            border-bottom: 1px solid #e0e0e0;
        }
        .social-link li {
            display: inline-block;
            margin-right: 10px;
        }
        .social-link i {
            font-size: 20px;
            color: #555;
        }
        .kuy:link, a:visited {
            background-color: #f1db25;
            color:  white;
            border: 2px solid #f1db25;
            padding: 5px 30px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .kuy:hover, a:active {
            background-color: white;
            color: #f1db25;
        }
    </style>
</head>
<body>
<div class="hero_area">
    <div class="hero_bg_box">
        <div class="img-box">
            <img src="img/haircut-4019676_1920.jpg" alt="">
        </div>
    </div>
    <header class="header_section">
        <div class="header_bottom">
            <div class="container-fluid">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="customer_dashboard.php">
                        <span>BBshop</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a class="nav-link" href="customer_dashboard.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="service_type.php">Services</a></li>
                            <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
                            <li class="nav-item"><a class="nav-link" href="customer_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>"><?php echo htmlspecialchars($user['email']); ?></a></li>
                            <li class="nav-item"><a class="nav-link" href="logout.php">Log out</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>
</div>

<div class="page-content page-container" id="page-content">
    <div class="padding">
        <div class="row container d-flex justify-content-center">
            <div class="col-xl-6 col-md-12">
                <div class="card user-card-full">
                    <div class="row m-l-0 m-r-0">
                        <div class="col-sm-4 bg-c-lite-green user-profile">
                            <div class="card-block">
                                <div class="m-b-25">
                                    <img src="https://img.icons8.com/bubbles/100/000000/user.png" alt="User-Profile-Image">
                                </div>
                                <h6 class="f-w-600"><?php echo htmlspecialchars($barber['name']); ?></h6>
                                <p>Barber</p>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="card-block">
                                <h6 class="m-b-20 p-b-5 b-b-default f-w-600">Information</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="m-b-10 f-w-600">Email</p>
                                        <h6 class="text-muted f-w-400"><?php echo htmlspecialchars($barber['email']); ?></h6>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="m-b-10 f-w-600">Phone</p>
                                        <h6 class="text-muted f-w-400"><?php echo htmlspecialchars($barber['phone_number']); ?></h6>
                                    </div>
                                </div>
                                <p class="m-b-10 f-w-600">Bio</p>
                                <h6 class="text-muted f-w-400"><?php echo nl2br(htmlspecialchars($barber['bio'])); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
</div>

</body>
</html>
