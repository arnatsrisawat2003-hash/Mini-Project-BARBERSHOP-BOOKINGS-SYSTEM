<?php
session_start();
include('database.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ดึง role ของผู้ใช้จากข้อมูลที่ได้มา

// รับ user_id จาก URL
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$user_id = $_SESSION['user_id']; // user_id จาก session
$sql = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
} else {
    $name = "Guest";
    $email = "N/A";
}

// อัปเดตข้อมูลเมื่อมีการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $update_sql = "UPDATE users SET name = ?, email = ?, phone_number = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $name, $email, $phone, $user_id);

    if ($update_stmt->execute()) {
        header("Location: customer_profile.php?user_id=$user_id");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลผู้ใช้ - <?php echo htmlspecialchars($user['name']); ?></title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap"rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />
    <style>
    body, html {
        height: 100%;
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .page-content {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 50vh;
    }

    .card {
        border-radius: 5px;
        box-shadow: 0 1px 20px 0 rgba(69,90,100,0.08);
        border: none;
    }

    .user-card-full {
        width: 100%;
        max-width: 700px;
        overflow: hidden;
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

    .kuy:link, .kuy:visited {
        background-color: #f1db25;
        color: white;
        border: 2px solid #f1db25;
        padding: 5px 30px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        transition: 0.3s ease;
    }

    .kuy:hover, .kuy:active {
        background-color: white;
        color: #f1db25;
    }

    a.back-link {
        display: block;
        text-align: left;
        margin-top: 10px;
        color: #007bff;
        text-decoration: none;
    }
</style>
</head>
<body class="sub_page">
<div class="hero_area">
        <!-- header section starts -->
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
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
                            <ul class="navbar-nav">
                                <li class="nav-item ">
                                    <a class="nav-link" href="barber_dashboard.php">Home <span class="sr-only">(current)</span></a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="customer_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>"><?php echo htmlspecialchars($email); ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="logout.php">Log out</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </header>
    </div>

    <div class="page-content" id="page-content">
    <div class="card user-card-full">
        <div class="row no-gutters">
            <div class="col-sm-4 bg-c-lite-green user-profile">
                <div class="card-block">
                    <div class="m-b-25">
                        <img src="https://img.icons8.com/bubbles/100/000000/user.png" alt="User-Profile-Image">
                    </div>
                    <h6 class="f-w-600"><?php echo htmlspecialchars($user['name']); ?></h6>
                    <p><?php echo htmlspecialchars(ucfirst($user['role'])); ?></p>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="card-block">
                    <h6 class="m-b-20 p-b-5 b-b-default f-w-600">Information</h6>
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="m-b-10 f-w-600">Email</p>
                            <h6 class="text-muted f-w-400"><?php echo htmlspecialchars($user['email']); ?></h6>
                        </div>
                        <div class="col-sm-6">
                            <p class="m-b-10 f-w-600">Phone</p>
                            <h6 class="text-muted f-w-400"><?php echo htmlspecialchars($user['phone_number']); ?></h6>
                        </div>
                    </div>
                    <ul class="social-link list-unstyled m-t-10 m-b-10">
                        <li><a href="#"><i class="mdi mdi-facebook feather icon-facebook"></i></a></li>
                        <li><a href="#"><i class="mdi mdi-twitter feather icon-twitter"></i></a></li>
                        <li><a href="#"><i class="mdi mdi-instagram feather icon-instagram"></i></a></li>
                    </ul>
                    <a href="edit_profile.php?user_id=<?php echo $user['user_id']; ?>" class="kuy" style="display: block; margin: 0 auto; width: fit-content;">EDIT</a>
                    <a href="javascript:history.back()" class="back-link">← Back</a>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>
