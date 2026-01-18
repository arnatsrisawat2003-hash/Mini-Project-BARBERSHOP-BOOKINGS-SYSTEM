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

// รับ type_id ที่เลือกจาก URL
$type_id = $_GET['type_id'];

// ดึงข้อมูลบริการตามประเภท
$sql = "SELECT * FROM services WHERE type_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $type_id);
$stmt->execute();
$result = $stmt->get_result();

// ดึงข้อมูลประเภทบริการ
$sql_type = "SELECT * FROM service_types WHERE type_id = ?";
$stmt_type = $conn->prepare($sql_type);
$stmt_type->bind_param("i", $type_id);
$stmt_type->execute();
$result_type = $stmt_type->get_result();
$type = $result_type->fetch_assoc();

$sql = "SELECT s.service_id, s.service_name, s.service_description, s.price, s.image, t.type_name 
        FROM services s
        LEFT JOIN service_types t ON s.type_id = t.type_id
        WHERE s.type_id = ?";
?>

<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

  <title>Guarder</title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <link href="css/service.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

<style>


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
  <div class="container-xxl py-5">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>our <span>Service</span></h2>
        </div>
        <br>
        <div class="row g-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item">
                            <div class="overflow-hidden">
                                <img class="img-fluid" src="img/man/<?php echo $row['image']; ?>" alt="<?php echo $row['service_name']; ?>">
                            </div>
                            <div class="p-4 text-center border border-5 border-light border-top-0">
                                <h4 class="mb-3"><?php echo $row['service_name']; ?></h4>
                                <p><?php echo $row['service_description']; ?></p>
                                <p><strong>Price:</strong> ฿<?php echo number_format($row['price'], 2); ?></p>
                                <a class="fw-medium" href="choose_barber.php?service_id=<?php echo $row['service_id']; ?>">เลือกช่าง <i class="fa fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center'>No services available.</p>";
            }
            $conn->close();
            ?>
        </div>
    </div>
</div>
</body>
</html>