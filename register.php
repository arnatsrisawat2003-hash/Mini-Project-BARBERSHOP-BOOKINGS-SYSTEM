<?php
include('database.php'); // รวมไฟล์เชื่อมต่อฐานข้อมูล

// ฟังก์ชั่นตรวจสอบความถูกต้องของอีเมล
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ฟังก์ชั่นตรวจสอบว่าเบอร์โทรศัพท์ถูกต้อง
function validate_phone($phone) {
    return preg_match("/^[0-9]{10}$/", $phone);
}

// ฟังก์ชั่นตรวจสอบชื่อผู้ใช้ซ้ำ
function is_username_exists($conn, $username) {
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// ฟังก์ชั่นตรวจสอบอีเมลซ้ำ
function is_email_exists($conn, $email) {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// เมื่อมีการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'customer'; // กำหนดค่า role เป็น 'customer' โดยอัตโนมัติ
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    // ตรวจสอบว่าอีเมลและเบอร์โทรศัพท์ถูกต้องหรือไม่
    if (!validate_email($email)) {
        $error_message = "อีเมลไม่ถูกต้อง กรุณากรอกใหม่";
    } elseif (!validate_phone($phone_number)) {
        $error_message = "เบอร์โทรศัพท์ไม่ถูกต้อง กรุณากรอกใหม่";
    } elseif (is_username_exists($conn, $username)) {
        $error_message = "ชื่อผู้ใช้มีอยู่ในระบบแล้ว กรุณาเลือกชื่อผู้ใช้ใหม่";
    } elseif (is_email_exists($conn, $email)) {
        $error_message = "อีเมลนี้มีอยู่ในระบบแล้ว กรุณากรอกอีเมลใหม่";
    } else {
        // เข้ารหัสรหัสผ่าน
        $password_hashed = password_hash($password, PASSWORD_DEFAULT); 

        // คำสั่ง SQL สำหรับการเพิ่มข้อมูลลงในตาราง users
        $sql = "INSERT INTO users (username, password, role, name, email, phone_number)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $password_hashed, $role, $name, $email, $phone_number);

        if ($stmt->execute()) {
            // หากลงทะเบียนสำเร็จ
            $success_message = "ลงทะเบียนสำเร็จ! กรุณาล็อกอินเพื่อใช้งาน";
            header("Location: login.php"); // Redirect ไปยังหน้า login
            exit();
        } else {
            $error_message = "เกิดข้อผิดพลาดในการลงทะเบียน: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/table.css" rel="stylesheet" />
    <link href="css/responsive.css" rel="stylesheet" />

    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">
    <link href="css/main.css" rel="stylesheet" media="all">
</head>
<body>
<div class="page-wrapper p-t-100 p-b-100 font-robo">
    <div class="wrapper wrapper--w680">
        <div class="card card-1">
            <div class="card-heading"></div>
            <div class="card-body">
                <!-- แสดงข้อความแจ้งเตือน -->
                <?php if (isset($error_message)) { ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php } elseif (isset($success_message)) { ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php } ?>

                <form method="POST" action="">
                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="USERNAME" name="username" required><br>
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="password" placeholder="PASSWORD" name="password" required><br>
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="NAME" name="name"><br>
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="email" placeholder="EMAIL" name="email"><br>
                    </div>
                    <div class="input-group">
                        <input type="text" class="input--style-1" name="phone_number" placeholder="PHONE"><br>
                    </div>
                    <div style="text-align: center;">
                        <button class="btn btn--radius btn--green" type="submit">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/select2/select2.min.js"></script>
<script src="vendor/datepicker/moment.min.js"></script>
<script src="vendor/datepicker/daterangepicker.js"></script>
<script src="js/global.js"></script>

</body>
</html>
