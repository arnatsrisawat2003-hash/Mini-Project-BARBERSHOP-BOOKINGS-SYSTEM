<?php
session_start();
include('database.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าได้ล็อกอินเป็นผู้ใช้หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

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

// รับ service_id ที่เลือกจาก URL
$service_id = $_GET['service_id'];

// ดึงข้อมูลช่างที่ให้บริการในบริการนั้นๆ จากตาราง barber_services
$sql = "SELECT u.user_id, u.name, u.email, u.phone_number, u.profile_image 
        FROM users u 
        JOIN barber_services bs ON u.user_id = bs.user_id 
        WHERE bs.service_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

// ดึงข้อมูลบริการที่เลือกมาแสดง
$sql_service = "SELECT service_name FROM services WHERE service_id = ?";
$stmt_service = $conn->prepare($sql_service);
$stmt_service->bind_param("i", $service_id);
$stmt_service->execute();
$result_service = $stmt_service->get_result();
$service = $result_service->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกช่างตัดผม - <?php echo htmlspecialchars($service['service_name']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="css/responsive.css" rel="stylesheet" />
  <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />

  <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 20px auto;
    text-align: center;
}

h1 {
    color: #333;
    margin-bottom: 30px;
}

.barber-card {
    display: inline-block;
    width: 320px; /* ขยายขนาดของการ์ด */
    height: auto; /* ให้ปรับความสูงอัตโนมัติ */
    margin: 20px;
    background-color: #fff;
    border-radius: 0; /* สี่เหลี่ยมคม */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s;
    overflow: hidden; /* ป้องกันการล้น */
}

.barber-card:hover {
    transform: scale(1.05);
}

.barber-card img {
    width: 100%; /* ให้รูปภาพขยายเต็มการ์ด */
    height: 380px; /* ปรับขนาดรูปให้สูงขึ้น */
    object-fit: cover; /* ให้รูปเต็มพื้นที่ */
}

.barber-card .text-container {
    background-color: #fff;
    padding: 15px;
}

.barber-card h3 {
    margin: 10px 0;
    color: black;
}

.barber-card p {
    color: #555;
    font-size: 14px;
    margin: 5px 0;
}

.barber-card button {
    margin-top: 10px;
    padding: 12px;
    width: 100%; /* ปุ่มเต็มความกว้าง */
    background-color: #ffcc00; /* สีเหลืองอ่อน */
    color: white;
    border: none;
    border-radius: 0; /* ปุ่มสี่เหลี่ยมคม */
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

.barber-card button:hover {
    background-color: #e6b800;
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
                BBshop
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
                <li class="nav-item active">
                  <a class="nav-link" href="service_type.php"> Services </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="history.php">History</a>
                </li>
                <li class="nav-item">
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
    <!-- end header section -->
  </div>
<section class="team_section layout_padding">
<div class="container">
<div class="container">
    <h1>เลือกช่างตัดผมสำหรับบริการ: <?php echo htmlspecialchars($service['service_name']); ?></h1>
    
<div class="barber-cards">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="barber-card">
            <?php if (!empty($row['profile_image'])): ?>
                <img src="img/profiles/<?php echo htmlspecialchars($row['profile_image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            <?php else: ?>
                <img src="https://via.placeholder.com/320x200" alt="<?php echo htmlspecialchars($row['name']); ?>">
            <?php endif; ?>
                <div class="text-container">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p>Email: <?php echo htmlspecialchars($row['email']); ?></p>
                    <p>Phone: <?php echo htmlspecialchars($row['phone_number']); ?></p>
                    <button onclick="window.location.href='choose_date.php?service_id=<?php echo $service_id; ?>&barber_id=<?php echo $row['user_id']; ?>'">
                    เลือกช่างนี้
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</div>
</section>
</body>
</html>
<?php
$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>
