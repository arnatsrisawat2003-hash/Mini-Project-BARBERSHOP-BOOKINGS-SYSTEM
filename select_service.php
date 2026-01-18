<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php"); // ถ้าไม่ใช่แอดมินจะถูกนำไปที่หน้าเข้าสู่ระบบ
    exit();
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
                  <a class="nav-link" href="about.html">User</a>
                </li>
                <li class="nav-item active">
                  <a class="nav-link" href="service.html">Barber</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="guard.html"> Guards </a>
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
        <h2>Our <span>Services</span></h2>
      </div>
      <div class="row">
        <div class="col-sm-6 col-md-4">
        <a href="add_service_type.php" style="color: black;">
          <div class="box ">
            <div class="img-box">
              <img src="img/icon-2073970_640.png" alt="" />
            </div>
            <div class="detail-box">
              <h5>
                Add a service type
              </h5>
              <p>
                Add various services as requested by the barber.
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-4">
        <a href="add_service.php" style="color: black;">
          <div class="box ">
            <div class="img-box">
              <img src="img/pc-7286574_640.png" alt="" />
            </div>
            <div class="detail-box">
              <h5>
                Add a service
              </h5>
              <p>
                Add various services related to the service type as requested 
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-4">
        <a href="testservice.php" style="color: black;">
          <div class="box ">
            <div class="img-box">
              <img src="img/barber.png" alt="" />
            </div>
            <div class="detail-box">
              <h5>
                Add barber service 
              </h5>
              <p>
                Add services that the technician wants to provide or is good at.
              </p>
            </div>
          </div>
        </div><div class="col-sm-6 col-md-4">
        <a href="view_services.php" style="color: black;">
          <div class="box ">
            <div class="img-box">
              <img src="img/barber.png" alt="" />
            </div>
            <div class="detail-box">
              <h5>
                Add barber service 
              </h5>
              <p>
                Add services that the technician wants to provide or is good at.
              </p>
            </div>
          </div>
        </div>
  </section>
</body>

</html>