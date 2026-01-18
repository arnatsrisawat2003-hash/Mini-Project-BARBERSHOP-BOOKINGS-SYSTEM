<?php
session_start();

include('database.php');

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php"); // ถ้าไม่ใช่แอดมินจะถูกนำไปที่หน้าเข้าสู่ระบบ
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

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />
  <style>
   .service_section {
  position: relative;
}

.service_section .box {
  margin-top: 30px;
  text-align: center;
  -webkit-box-shadow: 0 0 5px 2px rgba(0, 0, 0, 0.15);
          box-shadow: 0 0 5px 2px rgba(0, 0, 0, 0.15);
  padding: 25px 15px;
  -webkit-transition: all .3s;
  transition: all .3s;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
      -ms-flex-direction: column;
          flex-direction: column;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
}

.service_section .box .img-box {
  width: 65px;
  height: 65px;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
      -ms-flex-pack: center;
          justify-content: center;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
}

.service_section .box .img-box img {
  max-height: 100%;
  max-width: 100%;
  -webkit-transition: all .3s;
  transition: all .3s;
}

.service_section .box .detail-box {
  margin-top: 15px;
}

.service_section .box .detail-box h5 {
  font-weight: bold;
}

.service_section .box .detail-box p {
  margin: 0;
}

.service_section .box:hover {
  background-color: #da7426;
  color: #ffffff;
}

.service_section .box:hover .img-box img {
  -webkit-filter: brightness(0) invert(1);
          filter: brightness(0) invert(1);
}

.service_section .btn-box {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
      -ms-flex-pack: center;
          justify-content: center;
  margin-top: 45px;
}

.service_section .btn-box a {
  display: inline-block;
  padding: 10px 45px;
  background-color: #da7426;
  color: #ffffff;
  border-radius: 0;
  -webkit-transition: all .3s;
  transition: all .3s;
  border: 1px solid #da7426;
}

.service_section .btn-box a:hover {
  background-color: transparent;
  color: #da7426;
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
  <section class="service_section layout_padding">
    <div class="container ">
      <div class="heading_container heading_center">
        <h2>ADD & EDIT BARBER</h2>
      </div>
      <div class="row">
        <div class="col-sm-6 col-md-4">
        <a href="add_barber.php" style="color: black;">
          <div class="box ">
            <div class="img-box">
              <img src="img/beard-1299274_640.png" alt="" />
            </div>
            <div class="detail-box">
              <h5>
                Add Barber
              </h5>
              <p>
                Add the barber's name and phone number
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-4">
        <a href="add_barber_time.php" style="color: black;">
          <div class="box ">
            <div class="img-box">
              <img src="img/clock-1032653_640.png" alt="" />
            </div>
            <div class="detail-box">
              <h5>
                Add Time
              </h5>
              <p>
                Increase the appropriate working hours for hairdressers
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-4">
        <a href="view_barbers.php" style="color: black;">
          <div class="box ">
            <div class="img-box">
              <img src="img/approval-6491198_640.png" alt="" />
            </div>
            <div class="detail-box">
              <h5>
                Check the barber's
              </h5>
              <p>
                Check barber information and edit various information.
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-4">
        <a href="check_time_barber.php" style="color: black;">
          <div class="box ">
            <div class="img-box">
              <img src="img/write-2935375_640.png" alt="" />
            </div>
            <div class="detail-box">
              <h5>
                Check the Time barber's
              </h5>
              <p>
                Check barber information and edit various information.
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-4">
        <a href="edit_Time_barber.php" style="color: black;">
          <div class="box ">
            <div class="img-box">
              <img src="img/edit-1103598_640.png" alt="" />
            </div>
            <div class="detail-box">
              <h5>
                Check the EDIT Time barber's
              </h5>
              <p>
                Check AND EDIT barber information and edit various information.
              </p>
            </div>
          </div>
        </div>
  </section>
</body>

</html>