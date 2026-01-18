<?php
session_start(); // เริ่มต้น session เพื่อเก็บข้อมูลการเข้าสู่ระบบ

include('database.php'); // รวมไฟล์เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // คำสั่ง SQL สำหรับค้นหาผู้ใช้ที่มีชื่อผู้ใช้ที่ตรงกัน
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // ผู้ใช้ที่ตรงกันถูกพบ
        $row = $result->fetch_assoc();
        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $row['password'])) {
            // การเข้าสู่ระบบสำเร็จ
            $_SESSION['user_id'] = $row['user_id']; // เก็บ user_id ใน session
            $_SESSION['role'] = $row['role']; // เก็บ role ใน session
            $_SESSION['username'] = $row['username']; // เก็บ username ใน session

            // เช็คประเภทผู้ใช้และนำไปยังหน้าที่เหมาะสม
            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($row['role'] == 'barber') {
                header("Location: barber_dashboard.php");
            } elseif ($row['role'] == 'customer') {
                header("Location: customer_dashboard.php");
            }
        } else {
            echo "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        echo "ไม่พบผู้ใช้";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title>Au Register Forms by Colorlib</title>

    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/table.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/main.css" rel="stylesheet" media="all">
    
</head>

<body class="sub_page">
    <div class="page-wrapper  p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <div style="text-align: center;">
                        <h2 class="title">Login Form</h2>
                    </div>
                    <?php if (!empty($message)) { ?>
                        <div class="alert <?php echo (strpos($message, '✅') !== false) ? 'success' : ''; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php } ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="input-group">
                            <input class="input--style-1" type="text" placeholder="USERNAME" name="username" required>
                        </div>
                        <div class="input-group">
                            <input class="input--style-1" type="password" placeholder="PASSWORD" name="password" required>
                        </div>
                            <div style="text-align: center;">
                                <button class="btn btn--radius btn--green" type="submit">Submit</button>
                            </div>
                            <br>
                            <div style="text-align: right; ">
                                <a href="register.php">Don't have an account? Register here.</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/datepicker/moment.min.js"></script>
    <script src="vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
    <script src="js/global.js"></script>

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
