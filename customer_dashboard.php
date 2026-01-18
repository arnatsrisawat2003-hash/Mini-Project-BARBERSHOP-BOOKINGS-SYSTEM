<?php
session_start();
include('database.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าได้ล็อกอินเป็นผู้ใช้หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
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

// ดึงข้อมูลผู้รักษาความปลอดภัยจากฐานข้อมูล
$sql_guards = "SELECT user_id, name, role, profile_image FROM users WHERE role = 'barber'"; // ดึงเฉพาะช่าง (barber)
$result_guards = $conn->query($sql_guards);

$sql = "SELECT * FROM service_types";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

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

    <title>Bbshop</title>

    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap"
        rel="stylesheet" />

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

<body>
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
                        <a class="navbar-brand" href="customer_dashboard.php">
                            <span>BBshop</span>
                        </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
                            <ul class="navbar-nav">
                                <li class="nav-item active">
                                    <a class="nav-link" href="customer_dashboard.php">Home <span class="sr-only">(current)</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="service_type.php">Services</a>
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

        <!-- Slider Section -->
        <section class="slider_section">
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="detail-box">
                                        <h1>
                                        Your Style <br>
                                        <span>
                                        Our Expertise
                                        </span>
                                        </h1>
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                            sed do eiusmod magna aliqua. Ut enim ad minim veniam.
                                        </p>
                                        <div class="btn-box">
                                            <a href="service_type.php" class="btn-1">Book Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <section class="about_section layout_padding">
    <div class="container">
      <div class="row">
        <div class="col-md-6 px-0">
          <div class="img_container">
            <div class="img-box">
              <img src="img/pexels-cottonbro-3992861.jpg" alt="" />
            </div>
          </div>
        </div>
        <div class="col-md-6 px-4">
          <div class="detail-box">
            <div class="heading_container ">
              <h2>
                 What is BBshop?
              </h2>
            </div>
            <p>
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
              eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
              enim ad minim veniam, quis nostrud exercitation ullamco laboris
              nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor
              in reprehenderit in voluptate velit
            </p>
            <div class="btn-box">
              <a href="service_type.php">
                Book Now
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

 <!-- Service Section -->
 <section class="service_section layout_padding">
            <div class="container">
                <div class="heading_container heading_center">
                    <h2>Our <span>Services</span></h2>
                </div>
                <div class="row">
                    <?php 
                    // ดึงข้อมูลประเภทบริการ
                    while ($row = $result->fetch_assoc()) { 
                        // ตรวจสอบว่ามีข้อมูลภาพหรือไม่
                        $image_path = !empty($row['image']) ? 'img/service/' . $row['image'] : 'img/Womens_haircut.png'; 
                    ?>
                    <div class="col-sm-6 col-md-4">
                        <a href="choose_service.php?type_id=<?php echo $row['type_id']; ?>" style="color: black;">
                            <div class="box">
                                <div class="img-box">
                                    <img src="<?php echo $image_path; ?>" alt="Service Image" />
                                </div>
                                <div class="detail-box">
                                    <h5>
                                        <?php echo htmlspecialchars($row['type_name']); ?>
                                    </h5>
                                    <p>
                                        <?php echo htmlspecialchars($row['description']); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </section>


    </div>
</section>

<section class="team_section layout_padding">
        <div class="container">
            <div class="heading_container heading_center">
                <h2>Our BarBer</h2>
                <p>Lorem ipsum dolor sit amet, non odio tincidunt ut ante, lorem a euismod suspendisse vel, sed quam nulla mauris iaculis. Erat eget vitae malesuada, tortor tincidunt porta lorem lectus.</p>
            </div>
            <div class="row">
                <?php
                if ($result_guards->num_rows > 0) {
                    while ($row = $result_guards->fetch_assoc()) {
                        $image_path = !empty($row['profile_image']) ? 'img/profiles/' . $row['profile_image'] : 'img/default_avatar.jpg';
                ?>
                <div class="col-md-4 col-sm-6 mx-auto">
                    <div class="box">
                        <div class="img-box">
                            <img src="<?php echo $image_path; ?>" alt="Guard Image">
                        </div>
                        <div class="detail-box">
                            <h5>
                                <a href="barber_details.php?user_id=<?php echo $row['user_id']; ?>" style="color: black;">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </a>
                            </h5>
                            <h6 class="">
                                <?php echo htmlspecialchars($row['role']); ?>
                            </h6>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<p>No guards found</p>';
                }
                ?>
            </div>
        </div>
    </section>

</body>

</html>
