<?php
session_start();
include('database.php');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
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

// ดึงข้อมูลช่างทั้งหมด
$sql_barbers = "SELECT user_id, name FROM users WHERE role = 'barber'";
$stmt = $conn->prepare($sql_barbers);
$stmt->execute();
$result_barbers = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกช่าง</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJx3PQ2nFvP2d1m9lZGxQqH6tQddZ5gkL3RoDgUzF+6yP5vF5H1Rexh4dO2y" crossorigin="anonymous">
    <style>
        /* ปรับปรุงการออกแบบให้ดูเรียบง่ายและทันสมัย */
        body {
        }
        .container{
            font-family: "Bebas Neue", sans-serif;
            max-width: 800px;
            margin-top: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        h1 {
            font-size: 2.5rem;
            color: #343a40;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-label {
            font-size: 0.8rem;
            font-weight: bold;
        }
        .btn-custom {
            background-color: black;
            color: white;
            font-size: 0.8rem;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            color: white;
        }
        .form-select {
            font-size: 1.1rem;
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
      <div class="heading_container heading_center">
        <h2>
            Pending Reservations
        </h2>
      </div>
  </section> 

    <div class="container">
        <form action="edit_barber_time.php" method="GET">
            <div class="mb-4">
                <label for="barber_id" class="form-label">Select Barber:</label>
                <select name="barber_id" id="barber_id" class="form-select">
                    <?php while ($row = $result_barbers->fetch_assoc()): ?>
                        <option value="<?php echo $row['user_id']; ?>"><?php echo $row['name']; ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-custom btn-lg">Select Barber</button>
            </div>
        </form>
    </div>

    <!-- เพิ่มการเชื่อมโยงกับ Bootstrap JS และ Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybSnQbTjmS6cFC7p4jAzDPLGhG4mQZV0QXZv7WrPyy8Pn1V0x" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0jjj5H1a1XQzQvg/cz5Tp1tF5Ff0JzZ9Pp4T6JX3ZIQlffp2" crossorigin="anonymous"></script>

</body>
</html>
