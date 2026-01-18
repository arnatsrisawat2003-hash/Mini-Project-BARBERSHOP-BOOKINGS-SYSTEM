<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php"); // ถ้าไม่ใช่แอดมินจะถูกนำไปที่หน้าเข้าสู่ระบบ
    exit();
}
// เชื่อมต่อกับฐานข้อมูล
include('database.php'); // เชื่อมต่อกับไฟล์ฐานข้อมูล

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



// ดึงข้อมูลบริการทั้งหมดจากฐานข้อมูล
$sql = "SELECT s.service_id, s.service_name, s.service_description, s.price, st.type_name 
        FROM services s 
        LEFT JOIN service_types st ON s.type_id = st.type_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูบริการทั้งหมด</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
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
    <link rel="stylesheet" href="styles.css"> <!-- คุณสามารถใส่สไตล์ของคุณได้ที่นี่ -->
    <style>
        .back-btn {
      background-color: #f1db25;
      border: 0.5px solid #f1db25;
      color: white;
      padding: 5px 20px;
      text-decoration: none;
      text-align: center;
      display: inline-block;
      margin-top: 20px;
    }

    .back-btn:hover {
      background-color: white;
      color: black;
    }.red-btn {
      background-color: red;
      border: 0.5px solid red;
      color: white;
      padding: 5px 20px;
      text-decoration: none;
      text-align: center;
      display: inline-block;
      margin-top: 20px;
    }

    .red-btn:hover {
      background-color: white;
      color: black;
    }
    </style>
</head>
<body class="sub_page">
  <div class="hero_area">
    <!-- header section strats -->
    <div class="hero_bg_box">
      <div class="img-box">
        <img src="images/hero-bg.jpg" alt="">
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
            Views And EDIT Services
        </h2>
      </div>
  </section>
  <br>
  <div class="heading_container heading_center">
        <h2>
            List of all services
        </h2>
        <br>
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No.1</th>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Service Type</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['service_id']; ?></td>
                            <td><?php echo $row['service_name']; ?></td>
                            <td><?php echo $row['service_description']; ?></td>
                            <td><?php echo number_format($row['price'], 2); ?> บาท</td>
                            <td><?php echo $row['type_name']; ?></td>
                            <td>
                                <!-- ปุ่มสำหรับแก้ไขบริการ -->
                                <a href="edit_service.php?service_id=<?php echo $row['service_id']; ?>" class="back-btn">Edit</a>
                                <a href="#" class="red-btn delete-service" data-id="<?php echo $row['service_id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>ยังไม่มีบริการในระบบ</p>
        <?php endif; ?>

        <br>
        <!-- ปุ่มสำหรับกลับไปที่หน้า Dashboard -->
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-service').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const serviceId = this.getAttribute('data-id');
            const row = this.closest('tr');

            if (confirm('คุณต้องการลบบริการนี้?')) {
                fetch('delete_service_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'service_id=' + serviceId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        row.remove(); // ลบแถวออกจากตารางทันที
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + data.message);
                    }
                });
            }
        });
    });
});
</script>

    <?php
    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn->close();
    ?>
</body>
</html>
