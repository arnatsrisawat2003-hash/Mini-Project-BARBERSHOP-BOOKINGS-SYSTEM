<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
include('database.php');

$user_id = $_SESSION['user_id'];
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

// ถ้ากดปุ่ม "เพิ่มบริการ"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];
    $price = $_POST['price'];
    $type_id = $_POST['type_id'];

    // จัดการอัปโหลดรูปภาพ
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "img/man/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
        }
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    // เพิ่มข้อมูลลงฐานข้อมูล
    $sql = "INSERT INTO services (service_name, service_description, price, type_id, image) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdis", $service_name, $service_description, $price, $type_id, $image_name);

    if ($stmt->execute()) {
        echo "<p class='success-message'>เพิ่มบริการสำเร็จ</p>";
    } else {
        echo "<p class='error-message'>เกิดข้อผิดพลาด: " . $stmt->error . "</p>";
    }
}

// ดึงประเภทบริการ
$sql_types = "SELECT * FROM service_types";
$result_types = $conn->query($sql_types);
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มบริการใหม่</title>
    <!-- เชื่อมโยง CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
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

    <style>
        /* สไตล์พื้นฐาน */
body {
    margin: 0;
    padding: 0;
}

/* กล่องฟอร์ม */
.container_rtr {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
}

.form-box {
    font-family: "Bebas Neue", sans-serif;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 30px;
    text-align: center;
}

/* หัวข้อ */
h2 {
    font-size: 24px;
    margin-bottom: 20px;
}

/* การตกแต่งฟอร์ม */
.form-group {
    margin-bottom: 20px;
    text-align: left;
}

.form-group label {
    font-size: 16px;
    color: #555;
    margin-bottom: 5px;
    display: block;
}

.form-input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    margin-top: 5px;
}

textarea.form-input {
    height: 120px;
    resize: vertical;
}

/* ปุ่ม */
.submit-btn {
    background-color: black;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
}

.submit-btn:hover {
    background-color: #f1db25;
    color: black;
}

/* ข้อความสำเร็จหรือข้อผิดพลาด */
.success-message {
    color: green;
    font-size: 18px;
    text-align: center;
    margin-top: 20px;
}

.error-message {
    color: red;
    font-size: 18px;
    text-align: center;
    margin-top: 20px;
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
            <a class="navbar-brand" href="index.html">
              <span>
                BBshop
              </span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class=""></span>
            </button>

            <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
              <ul class="navbar-nav  ">
                <li class="nav-item ">
                  <a class="nav-link" href="admin_dashboard.php">Home<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="customer_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>"><?php echo htmlspecialchars($email); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="logout.php">Loggut</a>
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </header>
    <!-- end header section -->
  </div>

  <section class="service_section layout_padding ">
    <div class="container">
      <div class="heading_container heading_center">
        <h2>
            ADD SERVICE
        </h2>
      </div>
  </section>
    <div class="container_rtr">
        <div class="form-box">
            <h2>Add new service</h2>
            <form method="POST" action="add_service.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="service_name">Service name:</label>
                    <input type="text" name="service_name" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="service_description">description:</label>
                    <textarea name="service_description" class="form-input"></textarea>
                </div>

                <div class="form-group">
                    <label for="price">price:</label>
                    <input type="number" name="price" step="0.01" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="type_id">services type:</label>
                    <select name="type_id" required class="form-input">
                        <?php
                        // ตรวจสอบว่า query ได้ผลลัพธ์หรือไม่
                        if ($result_types->num_rows > 0) {
                            // วนลูปเพื่อแสดงรายการประเภทบริการใน dropdown
                            while($row = $result_types->fetch_assoc()) {
                                echo "<option value='" . $row['type_id'] . "'>" . $row['type_name'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>ไม่มีประเภทบริการ</option>";
                        }
                        ?>
                    </select>
                </div>
    ...
                 <div class="form-group">
                        <label for="image">Image:</label>
                        <input type="file" name="image" class="form-input" accept="image/*">
                </div>
    ...
                <div class="form-group">
                    <input type="submit" value="Add service" class="submit-btn">
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
// ปิดการเชื่อมต่อฐานข้อมูลหลังจากการทำงานเสร็จ
$conn->close();
?>
