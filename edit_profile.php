<?php
session_start();
include('database.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit();
}

$name = $user['name'];
$email = $user['email'];

// อัปเดตข้อมูลเมื่อมีการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $update_sql = "UPDATE users SET name = ?, email = ?, phone_number = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $name, $email, $phone, $user_id);

    if ($update_stmt->execute()) {
        // เด้งกลับตาม role
        $role = $user['role'];
        $redirect_page = 'login.php';

        if ($role === 'customer') {
            $redirect_page = 'customer_profile.php';
        } elseif ($role === 'barber') {
            $redirect_page = 'barber_profile.php';
        } elseif ($role === 'admin') {
            $redirect_page = 'admin_profile.php';
        }

        echo "<script>
            alert('อัปเดตข้อมูลสำเร็จ!');
            window.location.href = '$redirect_page';
        </script>";
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }
}
?>


<!DOCTYPE html>
<html lang="th">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap"rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลผู้ใช้</title>
    <style>
        .edit-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 5px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
        }

        button:hover {
            background-color: #f1db25;
            color: black;
        }

        a.cancel-btn {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #777;
            text-decoration: none;
        }
        .center-wrapper {
            font-family: "Bebas Neue", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 50vh; /* ทำให้เต็มจอแนวตั้ง */
            padding-top: 100px; /* เผื่อเฮดเดอร์ */
            box-sizing: border-box;
        }

    </style>
</head>
<body class="sub_page">
  <div class="hero_area">
    <!-- header section strats -->
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
              <span>
                bbshop
              </span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class=""></span>
            </button>

            <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
              <ul class="navbar-nav  ">
                <li class="nav-item ">
                  <a class="nav-link" href="customer_dashboard.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="service_type.php"> Services </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="history.php">History</a>
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

  <div class="center-wrapper">
    <div class="edit-container">
        <h2>Edit user information</h2>
        <form method="POST" onsubmit="return confirmUpdate();">
            <label for="name">name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone">phone number:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

            <button type="submit">save</button>
            <a href="customer_profile.php?user_id=<?php echo $user['user_id']; ?>" class="cancel-btn">cancel</a>
        </form>
    </div>
</div>

<script>
function confirmUpdate() {
    return confirm("คุณแน่ใจหรือไม่ว่าต้องการบันทึกการเปลี่ยนแปลง?");
}
</script>
</body>
</html>
