<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php"); // ถ้าไม่ใช่แอดมินจะถูกส่งไปที่หน้าเข้าสู่ระบบ
    exit();
}

// เชื่อมต่อกับฐานข้อมูล
include('database.php');


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


// ดึงข้อมูลประเภทบริการ (service types)
$sql_types = "SELECT * FROM service_types";
$result_types = $conn->query($sql_types);

// ถ้ากดปุ่ม "เพิ่มบริการให้ช่าง"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barber_id = $_POST['barber_id'];
    $service_ids = $_POST['service_ids']; 

    foreach ($service_ids as $service_id) {
        $insert_sql = "INSERT IGNORE INTO barber_services (user_id, service_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ii", $barber_id, $service_id);
        if (!$stmt->execute()) {
            echo "เพิ่มบริการไม่สำเร็จ!";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มบริการให้กับช่าง</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/table.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <style>
.service-form {
    font-family: "Bebas Neue", sans-serif;
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #ffff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.service-form h2 {
    text-align: center;
    margin-bottom: 20px;
}

.service-form label {
    display: block;
    margin: 10px 0 5px;
}

.service-form select, .service-form input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;  /* เพิ่มระยะห่างที่นี่ */
    border-radius: 5px;
    border: 1px solid #ccc;
}

.service-form input[type="submit"] {
    background-color: #eac210;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

.service-form input[type="submit"]:hover {
    background-color: #c59e10;  /* เพิ่มสีเมื่อ hover */
}

.checkbox-group label {
    display: block;
}
.service-form input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin-top: 30px;  /* เพิ่มระยะห่างที่นี่ */
    margin-bottom: 40px;  /* เพิ่มระยะห่างที่นี่ */
    border-radius: 5px;
    background-color: #eac210;
    color: white;
    font-size: 16px;
    cursor: pointer;
}
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
    // เมื่อเลือกประเภทบริการแล้ว
    $(document).ready(function() {
        // Initialize Select2
        $('#services').select2();

        // เมื่อเลือกประเภทบริการแล้ว
        $('#service_type').change(function() {
            var type_id = $(this).val(); // เก็บค่าที่เลือกจาก dropdown
            if (type_id) {
                $.ajax({
                    url: 'fetch_services.php', // ใช้ไฟล์ PHP ที่จะดึงข้อมูลบริการ
                    method: 'POST',
                    data: { type_id: type_id },
                    success: function(response) {
                        // ตรวจสอบข้อมูลที่ได้รับจาก AJAX
                        console.log(response);  // เพิ่มการตรวจสอบข้อมูลที่ได้รับจากเซิร์ฟเวอร์
                        $('#services').html(response); // แสดงบริการที่เกี่ยวข้อง
                        $('#services').select2(); // รีเฟรช Select2
                    },
                    error: function(xhr, status, error) {
                        console.log('เกิดข้อผิดพลาดในการดึงข้อมูลบริการ: ' + error);
                    }
                });
            } else {
                $('#services').html('<option value="">กรุณาเลือกประเภทบริการก่อน</option>');
                $('#services').select2();
            }
        });
    });
</script>
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
  <br><br>
  <body class="sub_page">
    <div class="service-form">
        <h2>Add services to barbers</h2>

        <form method="POST" action="">
    <label for="barber_id">select barber:</label>
    <select name="barber_id" required>
        <option value="">select barber</option>
        <?php
        // ดึงข้อมูลรายชื่อช่างจากฐานข้อมูล
        $sql_barbers = "SELECT user_id, name FROM users WHERE role = 'barber'";
        $result_barbers = $conn->query($sql_barbers);
        while ($row = $result_barbers->fetch_assoc()) {
            echo "<option value='" . $row['user_id'] . "'>" . $row['name'] . "</option>";
        }
        ?>
    </select>

    <label for="service_type">Select service type:</label>
    <select name="service_type" id="service_type" required>
        <option value="">Select service type</option>
        <?php
        // ดึงประเภทบริการจากฐานข้อมูล
        $result_types = $conn->query($sql_types);
        if ($result_types->num_rows > 0) {
            while ($row = $result_types->fetch_assoc()) {
                echo "<option value='" . $row['type_id'] . "'>" . $row['type_name'] . "</option>";
            }
        }
        ?>
    </select>

    <label for="services">Choose the service that the barber can perform.:</label>
    <select name="service_ids[]" id="services" multiple="multiple" required>
        <!-- บริการที่เกี่ยวข้องจะแสดงที่นี่ -->
        <option value="">Please select the service type first.</option>
    </select>

    <input type="submit" value="บันทึกการเลือกบริการ">
    </form>
    </div>
    <script>
    $(document).ready(function() {
        // เมื่อคลิกปุ่ม "บันทึกการเลือกบริการ"
        $('form').submit(function(event) {
            // แสดงกล่องข้อความยืนยัน
            var confirmAction = confirm("คุณแน่ใจหรือไม่ที่จะเพิ่มบริการให้กับช่าง?");
            
            // ถ้าผู้ใช้ไม่ยืนยัน ให้ยกเลิกการส่งฟอร์ม
            if (!confirmAction) {
                event.preventDefault(); // ยกเลิกการส่งฟอร์ม
                return false;
            }

            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.includes("สำเร็จ")) {
                        alert("เพิ่มบริการให้ช่างสำเร็จ!");

                        // รีเซ็ตฟอร์ม
                        form[0].reset();
                        // รีเซ็ต Select2
                        $('#services').val(null).trigger('change');
                        $('#service_type').val('').trigger('change');
                    } else {
                        alert("เพิ่มบริการให้ช่างไม่สำเร็จ!");
                    }
                },
                error: function(xhr, status, error) {
                    alert("เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์");
                }
            });

            event.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ
        });
    });
</script>

</body>
</html>