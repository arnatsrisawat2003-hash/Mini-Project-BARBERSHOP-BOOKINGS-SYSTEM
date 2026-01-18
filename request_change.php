<?php
// เริ่ม session
session_start();
include('database.php');

// ตรวจสอบว่าเป็น Barber
if ($_SESSION['role'] !== 'barber') {
    header('Location: login.php');
    exit;
}

$admin_sql = "SELECT email, phone_number FROM users WHERE role = 'admin' LIMIT 1";
$admin_result = $conn->query($admin_sql);
$admin_email = "N/A";
$admin_phone = "N/A";

if ($admin_result && $admin_result->num_rows > 0) {
    $admin_row = $admin_result->fetch_assoc();
    $admin_email = $admin_row['email'] ?? "N/A";
    $admin_phone = $admin_row['phone_number'] ?? "N/A";
}


$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$name = $row['name'] ?? "Guest";
$email = $row['email'] ?? "N/A";

$success = '';
$error = '';

// เมื่อส่งแบบฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_type = $_POST['request_type'] ?? '';
    $request_details = $_POST['request_details'] ?? '';

    $query = "INSERT INTO change_requests (user_id, request_type, request_details, status) 
              VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $user_id, $request_type, $request_details);

    if ($stmt->execute()) {
        $success = "คำร้องขอถูกส่งสำเร็จแล้ว!";
    } else {
        $error = "เกิดข้อผิดพลาดในการส่งคำร้อง: " . $conn->error;
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link rel="stylesheet" href="request/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Oswald:300,400|Roboto+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="request/style.css">
    <link href="css/responsive.css" rel="stylesheet" />
    <title>Request Form</title>
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
            <a class="navbar-brand" href="barber_dashboard.php">
              <span>BBshop</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class=""></span>
            </button>
            <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a class="nav-link" href="barber_dashboard.php">Home</a>
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
  </div>

    <div class="content">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <div class="row mb-5">
              <div class="col-md-4 mr-auto">
                <h3 class="thin-heading mb-4">Request</h3>
                <p>Request changes to data<br>that cannot be changed directly.</p>
              </div>
              <div class="col-md-6 ml-auto">
                <h3 class="thin-heading mb-4">Contact Info</h3>
                    <p>T: <?= htmlspecialchars($admin_phone) ?> <br> 
                     E: <?= htmlspecialchars($admin_email) ?></p>
                </div>
            </div>
            <div class="row justify-content-center">
              <div class="col-md-12">
                <h3 class="thin-heading mb-4">Message Us</h3>

                <?php if ($success): ?>
                  <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                  <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="post" action="request_change.php" id="contactForm" name="contactForm" onsubmit="return confirmSubmit()">
                  <div class="row">
                    <div class="col-md-12 form-group">
                      <label for="request_type">Petition type</label>
                      <select class="form-control" name="request_type" id="request_type" required>
                        <option value="">-- Select Request Type --</option>
                        <option value="modify_service">Change The Service</option>
                        <option value="modify_booking">Change Time</option>
                        <option value="other">Other</option>
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12 form-group">
                      <label for="request_details">description</label>
                      <textarea class="form-control" name="request_details" id="request_details" cols="30" rows="3" placeholder="Specify the description of your request" required></textarea>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      <input type="submit" value="Send Message" class="btn btn-primary rounded-0 py-2 px-4">
                      <span class="submitting"></span>
                    </div>
                  </div>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
  function confirmSubmit() {
    const confirmed = confirm("คุณแน่ใจหรือไม่ว่าต้องการส่งคำร้อง?");
    if (confirmed) {
      // รอ PHP ดำเนินการก่อนค่อย reset
      setTimeout(() => {
        document.getElementById('contactForm').reset();
      }, 100);
    }
    return confirmed; // ถ้า true -> ส่งฟอร์ม, ถ้า false -> ยกเลิก
  }
</script>

  </body>
</html>
