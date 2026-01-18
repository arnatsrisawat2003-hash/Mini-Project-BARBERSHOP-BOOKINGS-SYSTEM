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

// รับข้อมูล service_id และ barber_id ที่เลือกจาก URL
$service_id = $_GET['service_id'];
$barber_id = $_GET['barber_id'];

// ดึงข้อมูลบริการ
$sql = "SELECT service_name FROM services WHERE service_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();
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
            margin-bottom: 20px;
        }

        h2 {
            color: #555;
            margin-bottom: 20px;
        }

        .date-picker {
            display: inline-block;
            margin-top: 20px;
        }

        input[type="date"] {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #dcd011 ;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #dcd011 ;
        }
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

.service_section .box {
        margin-top: 30px;
        text-align: center;
        -webkit-box-shadow: 0 0 5px 2px rgba(0, 0, 0, 0.15);
                box-shadow: 0 0 5px 2px rgba(0, 0, 0, 0.15);
        padding: 25px 15px;
        -webkit-transition: all .3s;
        transition: all .3s;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .service_section .box .img-box {
        width: 65px;
        height: 65px;
        display: flex;
        justify-content: center;
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
        display: flex;
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

    /* Table styles */
  table {
    border-collapse: collapse;
    width: 80%; /* ปรับความกว้างของตาราง */
    margin: 20px auto; /* ทำให้ตารางอยู่กึ่งกลาง */
    text-align: center;
    background-color: black; /* พื้นหลังสีดำ */
    color: white; /* ตัวอักษรสีขาว */
    border-radius: 10px; /* มุมโค้ง */
    overflow: hidden;
}

th, td {
    padding: 12px;
    text-align: center;
}

th {
    background-color: #333; /* สีหัวตารางเป็นเทาเข้ม */
    color: white;
}

tr:nth-child(even) {
    background-color: #222; /* สีแถวคู่ */
}

tr:nth-child(odd) {
    background-color: #111; /* สีแถวคี่ */
}

td span {
    font-weight: bold;
}
/*

Template 2099 Scenic

http://www.tooplate.com/view/2099-scenic

*/


@import url('https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,700');

body {
  background: #ffffff;
  font-family: 'Source Sans Pro', sans-serif;
  overflow-x: hidden;
}

html, body {
  width: 100%;
  height: 100%;
}

/*---------------------------------------
  TYPOGRAPHY              
-----------------------------------------*/

h1,h2,h3,h4,h5,h6 {
  font-weight: 300;
}

h1 {
  font-size: 40px;
  font-weight: 200;
  line-height: 50px;
}

h2 {
  font-size: 30px;
  line-height: 40px;
  margin-top: 0;
}

h3 {
  font-size: 20px;
  font-weight: bold;
  line-height: 32px;
}

h4 {
  color: #505050;
  font-size: 18px;
  line-height: 28px;
}

p {
  color: #757575;
  font-size: 16px;
  font-weight: 300;
  line-height: 29px;
  letter-spacing: 0.5px;
}


/*---------------------------------------
  BUTTONS               
-----------------------------------------*/

.section-btn {
  margin: 32px 0 0 0;
  padding: 0;
}

.section-btn a,
.section-btn button {
  line-height: 45px;
  -webkit-perspective: 1000px;
  -moz-perspective: 1000px;
  perspective: 1000px;
  color: #ffffff;
  font-weight: normal;
}

.section-btn a span,
.section-btn button span {
  position: relative;
  display: inline-block;
  font-size: 18px;
  font-weight: normal;
  letter-spacing: 0.5px;
  padding: 0 25px;
  background: #4dc47d;
  border-radius: 1px;
  -webkit-transition: -webkit-transform 0.3s;
  -moz-transition: -moz-transform 0.3s;
  transition: transform 0.3s;
  -webkit-transform-origin: 50% 0;
  -moz-transform-origin: 50% 0;
  transform-origin: 50% 0;
  -webkit-transform-style: preserve-3d;
  -moz-transform-style: preserve-3d;
  transform-style: preserve-3d;
}

.csstransforms3d .section-btn a span::before,
.csstransforms3d .section-btn button span::before {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  height: 100%;
  background: #000000;
  border-radius: 1px;
  color: #ffffff;
  padding: 0 25px;
  content: attr(data-hover);
  -webkit-transition: background 0.3s;
  -moz-transition: background 0.3s;
  transition: background 0.3s;
  -webkit-transform: rotateX(-90deg);
  -moz-transform: rotateX(-90deg);
  transform: rotateX(-90deg);
  -webkit-transform-origin: 50% 0;
  -moz-transform-origin: 50% 0;
  transform-origin: 50% 0;
}

.section-btn a:hover span,
.section-btn a:focus span,

.section-btn button:hover span,
.section-btn button:focus span {
  -webkit-transform: rotateX(90deg) translateY(-22px);
  -moz-transform: rotateX(90deg) translateY(-22px);
  transform: rotateX(90deg) translateY(-22px);
}

.csstransforms3d .section-btn a:hover span::before,
.csstransforms3d .section-btn a:focus span::before,

.csstransforms3d .section-btn button:hover span::before,
.csstransforms3d .section-btn button:hover span::before {
  background: #000000;  
}


/*---------------------------------------
  GENERAL               
-----------------------------------------*/

html{
  -webkit-font-smoothing: antialiased;
}

a {
  color: #4dc47d;
  -webkit-transition: 0.5s;
  transition: 0.5s;
}

a:hover, a:active, a:focus {
  color: #000000;
  outline: none;
}

* {
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

*:before,
*:after {
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

::-webkit-scrollbar{
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-thumb {
  cursor: pointer;
  background: #252525;
}

.section-title {
  position: relative;
  padding-bottom: 22px;
}

.parallax-section {
  background-attachment: fixed !important;
  background-size: cover !important;
}

#about, #project,
#team, #contact, footer {
  background: #ffffff;
}

#about,
#team, #contact {
  padding-top: 100px;
  padding-bottom: 100px;
  text-align: center;
}


/*---------------------------------------
  PRE LOADER              
-----------------------------------------*/

.preloader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 99999;
  display: flex;
  flex-flow: row nowrap;
  justify-content: center;
  align-items: center;
  background: none repeat scroll 0 0 #ffffff;
}

.spinner {
  border: 1px solid transparent;
  border-radius: 5px;
  position: relative;
}

.spinner:before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  top: 50%;
  left: 50%;
  width: 45px;
  height: 45px;
  margin-top: -10px;
  margin-left: -10px;
  border-radius: 50%;
  border: 1px solid #959595;
  border-top-color: #ffffff;
  animation: spinner .9s linear infinite;
}

@-webkit-@keyframes spinner {
  to {transform: rotate(360deg);}
}

@keyframes spinner {
  to {transform: rotate(360deg);}
}


/*---------------------------------------
  MENU             
-----------------------------------------*/

.custom-navbar {
  margin-bottom: 0;
  background-color: #4dc47d;
  padding: 20px 0;
}

.custom-navbar .navbar-brand {
  color: #f9f9f9;
  font-weight: normal;
  font-size: 25px;
}

.custom-navbar .nav li a {
  font-size: 14px;
  font-weight: normal;
  color: #f9f9f9;
  letter-spacing: 0.5px;
  -webkit-transition: all ease-in-out 0.4s;
  transition: all ease-in-out 0.4s;
  padding: 0;
  margin: 15px 20px;
  text-transform: uppercase;
}

.custom-navbar .navbar-nav > li > a:hover,
.custom-navbar .navbar-nav > li > a:focus {
  background-color: transparent;
  color: #ffffff;
}

.custom-navbar .navbar-nav li a:after {
  content: "";
  position: absolute;
  display: block;
  width: 0px;
  height: 2px;
  margin: auto;
  background: transparent;
  transition: width .3s ease, background-color .3s ease;
}

.custom-navbar .navbar-nav li a:hover:after,
.custom-navbar .nav li.active > a:after {
  background: #ffffff;
  color: #ffffff;
  width: 100%;
}

.custom-navbar .nav li.active > a {
  background-color: transparent;
  color: #ffffff;
}

.custom-navbar .navbar-toggle {
  border: none;
  padding-top: 10px;
}

.custom-navbar .navbar-toggle {
  background-color: transparent;
}

.custom-navbar .navbar-toggle .icon-bar {
  background: #ffffff;
  border-color: transparent;
}

@media(min-width:768px) {
  .custom-navbar {
    background: 0 0; 
  }
  .custom-navbar.top-nav-collapse {
    background: #4dc47d;
    padding: 15px 0;
  }
}


/*---------------------------------------
  HOME             
-----------------------------------------*/

#home {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
   align-items: center;
  height: 100vh;
  position: relative;
  padding-top: 10em;
}

#home h1 {
  color: #ffffff;
  font-size: 6em;
  line-height: 1.2em;
}

#home p {
  color: rgba(250,250,250,0.7);
}

#home .overlay {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.75);
}

#home video {
  position: fixed;
  top: 50%;
  left: 50%;
  min-width: 100%;
  min-height: 100%;
  width: auto;
  height: auto;
  z-index: -100;
  transform: translateX(-50%) translateY(-50%);
  background-size: cover;
  transition: 1s opacity;
}


/*---------------------------------------
  ABOUT              
-----------------------------------------*/

#about {
  padding-top: 200px;
  padding-bottom: 150px;
}

.about-info h3 {
  font-size: 14px;
  letter-spacing: 12px;
  text-transform: uppercase;
  margin: 0;
}


/*---------------------------------------
  PROJECT              
-----------------------------------------*/

.project-item {
  overflow: hidden;
  position: relative;
  margin-bottom: 25px;
  padding: 0;
  vertical-align: middle;
  text-align: center;
}

.project-overlay {
  background: rgba(0,0,0,0.5);
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  opacity: 0;
  -webkit-transition: all ease-in-out 0.4s;
  transition: all ease-in-out 0.4s;
}

.project-info {
  padding: 12em 0;
}

.project-item img {
  -webkit-transition: all ease-in-out 0.4s;
  transition: all ease-in-out 0.4s;
}
.project-item:hover img {
  transform: scale(1.1);
}

.project-overlay h1 {
  color: #ffffff;
  margin: 0;
}
.project-overlay h3 {
  color: #d9d9d9;
  font-size: 14px;
  letter-spacing: 0.2px;
  margin-top: 0;
}

.project-item:hover .project-overlay {
  opacity: 1;
}


/*---------------------------------------
   TEAM             
-----------------------------------------*/

#team .item {
  display: block;
  width: 100%;
  margin-bottom: 22px;
}

#team h3,
#team p {
  margin: 0;
}

.team-item {
  overflow: hidden;
  position: relative;
  margin-top: 34px;
  margin-bottom: 16px;
}

.team-item img {
  -webkit-transition: all ease-in-out 0.4s;
  transition: all ease-in-out 0.4s;
}
.team-item:hover img {
  transform: scale(1.1);
}

.team-overlay {
  background: rgba(0,0,0,0.5);
  opacity: 0;
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  -webkit-transition: all ease-in-out 0.4s;
  transition: all ease-in-out 0.4s;
}

.team-overlay .social-icon {
  position: relative;
  top: 45%;
}
.team-overlay .social-icon li a {
  background: #ffffff;
  color: #191919;
  width: 35px;
  height: 35px;
  line-height: 35px;
  text-align: center;
}

.team-item:hover .team-overlay {
  opacity: 1;
}


/*---------------------------------------
  CONTACT             
-----------------------------------------*/

#contact {
  background: url('../images/contact-bg.jpg') 50% 0 repeat-y fixed;
  background-size: cover;
  background-position: center center;
}

#contact-form {
  padding-top: 22px;
}

#contact .text-success,
#contact .text-danger {
  display: none;
  padding: 0 0 5px 20px;
}

#contact .form-control {
  border: none;
  box-shadow: none;
  font-size: 18px;
  font-weight: normal;
  margin-bottom: 22px;
  -webkit-transition: all ease-in-out 0.4s;
  transition: all ease-in-out 0.4s;
}

#contact .form-control:focus {
  border-color: #d9d9d9;
}

#contact input {
  height: 55px;
  line-height: 45px;
}

#contact .section-btn {
  margin: 5px 0 0 0;
}

#contact button#cf-submit {
  background: transparent;
  border: none;
  padding: 0;
  line-height: 50px;
}


/*---------------------------------------
  FOOTER              
-----------------------------------------*/

footer {
  padding-top: 120px;
  padding-bottom: 120px;
}

footer h4 {
  padding-top: 12px;
}

footer a {
  color: #757575;
}

footer .copyright-text {
  padding-top: 42px;
}


/*---------------------------------------
  SOCIAL ICON             
-----------------------------------------*/

.social-icon {
  position: relative;
  padding: 0;
  margin: 0;
  text-align: center;
}

.social-icon li {
  display: inline-block;
  list-style: none;
}

.social-icon li a {
  border-radius: 100%;
  color: #292929;
  cursor: pointer;
  font-size: 18px;
  text-decoration: none;
  -webkit-transition: all ease-in-out 0.3s;
  transition: all ease-in-out 0.3s;
  text-align: center;
  position: relative;
  margin: 4px 8px 0 8px;
}

.social-icon li a:hover {
  -webkit-transform: scale(1.2);
  transform: scale(1.2);
}


/*---------------------------------------
  MOBILE RESPONSIVE              
-----------------------------------------*/

@media (max-width: 992px) {
  #home h1 {
    font-size: 4em;
    line-height: normal;
  }
}

@media (max-width: 980px) {
  h1 {
    font-size: 30px;
    line-height: inherit;
  }

  #home {
    padding-top: 0em;
  }

  #about {
    padding-top: 120px;
    padding-bottom: 100px;
  }

  .project-info {
    padding: 4em 0;
  }

  footer {
    text-align: center;
  }
  footer .social-icon {
    margin-top: 32px;
  }
}

@media (max-width: 770px) {
  #home h1 {
    font-size: 3.5em;
  }
}

@media (max-width: 767px) {
  .custom-navbar {
    padding: 10px 0;
  }
  .custom-navbar .nav li a {
    display: inline-block;
    line-height: 15px;
    margin-bottom: 0;
  }
  .custom-navbar .nav li:last-child a {
    margin-bottom: 5px;
  }

  .project-info {
    padding: 10em 0;
  }

  .footer-info {
    padding: 22px 0 22px 0;
  }
}


@media (max-width: 580px) {
  h1 {
    font-size: 26px;
  }

  #home {
    height: 100vh;
  }

  .about-info h3 {
    font-size: 12px;
    letter-spacing: 6px;
  }

  .project-info {
    padding: 5em 0;
  }
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
  <div class="container">
    <h1>เลือกวันที่สำหรับบริการ: <?php echo htmlspecialchars($service['service_name']); ?></h1>

    <form method="GET" action="choose_time.php">
        <label for="booking_date">เลือกวันที่:</label>
        <input type="date" id="booking_date" name="booking_date" required>

        <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
        <input type="hidden" name="barber_id" value="<?php echo $barber_id; ?>">
        <button type="submit">เลือกเวลา</button>
    </form>
</div>

<section id="project" class="parallax-section">
     <div class="container">
          <div class="row">

               <div class="col-md-6 col-sm-6">
                    <!-- PROJECT ITEM -->
                    <div class="project-item">
                         <a href="img/barber-shop-6818718_640.jpg" class="image-popup">
                              <img src="img/barber-shop-6818718_640.jpg" class="img-responsive" alt="">
                         
                              <div class="project-overlay">
                                   <div class="project-info">
                                        <h1>Beautiful Women</h1>
                                        <h3>Click to pop up!</h3>
                                   </div>
                              </div>
                         </a>
                    </div>
               </div>

               <div class="col-md-6 col-sm-6">
                    <!-- PROJECT ITEM -->
                    <div class="project-item">
                         <a href="img/pexels-cottonbro-3998397.jpg" class="image-popup">
                              <img src="img/pexels-cottonbro-3998397.jpg" class="img-responsive" alt="">
                         
                              <div class="project-overlay">
                                   <div class="project-info">
                                        <h1>Nulla efficitur quam</h1>
                                        <h3>Sed ligula accumsan</h3>
                                   </div>
                              </div>
                         </a>
                    </div>
               </div>

               <div class="col-md-6 col-sm-6">
                    <!-- PROJECT ITEM -->
                    <div class="project-item">
                         <a href="img/pexels-enginakyurt-3065171.jpg" class="image-popup">
                              <img src="img/pexels-enginakyurt-3065171.jpg" class="img-responsive" alt="">
                         
                              <div class="project-overlay">
                                   <div class="project-info">
                                        <h1>Large Sea Wave</h1>
                                        <h3>Nam feugiat dui in nisi</h3>
                                   </div>
                              </div>
                         </a>
                    </div>
               </div>

               <div class="col-md-6 col-sm-6">
                    <!-- PROJECT ITEM -->
                    <div class="project-item">
                         <a href="img/pexels-rdne-7755215.jpg" class="image-popup">
                              <img src="img/pexels-rdne-7755215.jpg" class="img-responsive" alt="">
                         
                              <div class="project-overlay">
                                   <div class="project-info">
                                        <h1>Lorem ipsum dolor</h1>
                                        <h3>Mollis aliquam faucibus urna</h3>
                                   </div>
                              </div>
                         </a>
                    </div>
               </div>               

          </div>
     </div>
</section>

<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.parallax.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/magnific-popup-options.js"></script>
<script src="js/modernizr.custom.js"></script>
<script src="js/smoothscroll.js"></script>
<script src="js/custom.js"></script>

</body>
</html>