<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

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

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE user_id = $user_id";
    $result = $conn->query($sql);
    $barber = $result->fetch_assoc();
}

// ดึงข้อมูลประเภทบริการ
$sql_service_types = "SELECT * FROM service_types";
$result_service_types = $conn->query($sql_service_types);

// ดึงบริการที่ช่างเคยเลือกไว้
$sql_selected_services = "SELECT service_id FROM barber_services WHERE user_id = $user_id";
$result_selected = $conn->query($sql_selected_services);
$selected_services = [];
while ($row = $result_selected->fetch_assoc()) {
    $selected_services[] = $row['service_id'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $services = isset($_POST['services']) ? $_POST['services'] : [];

    $sql_update_user = "UPDATE users SET username = '$username', name = '$name', email = '$email', phone_number = '$phone_number' WHERE user_id = $user_id";

    if ($conn->query($sql_update_user) === TRUE) {
        $conn->query("DELETE FROM barber_services WHERE user_id = $user_id");
        foreach ($services as $service_id) {
            $conn->query("INSERT INTO barber_services (user_id, service_id) VALUES ('$user_id', '$service_id')");
        }
        $success_message = "อัพเดตข้อมูลช่างสำเร็จ!";
    } else {
        $error_message = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลช่าง</title>
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
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            font-family: "Bebas Neue", sans-serif;
            max-width: 600px;
            margin-top: 50px;
        }
        .card {
            border-radius: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btnless {
            background-color: #000;
            border: 0.5px solid black;
            color: #fff;
            padding: 10px 30px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            margin-top: 20px;
        }

        .btnless:hover {
            background-color: white;
            color: black;
        }   
    </style>
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
                <li class="nav-item active">
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

  <section class="service_section layout_padding ">
      <div class="heading_container heading_center">
        <h2>
            EDIT INFO BARBER
        </h2>
  </section>

<div class="container">
    <div class="card p-4">
        <h2 class="text-center mb-4">Edit barber information</h2>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">UserName:</label>
                <input type="text" name="username" class="form-control" value="<?php echo $barber['username']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Barber Name:</label>
                <input type="text" name="name" class="form-control" value="<?php echo $barber['name']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">EMAIL:</label>
                <input type="email" name="email" class="form-control" value="<?php echo $barber['email']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone NumBer:</label>
                <input type="text" name="phone_number" class="form-control" value="<?php echo $barber['phone_number']; ?>" required>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btnless">Update information</button>
                <a href="view_barbers.php" class="btn  btn-outline-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
<br><br>
</body>
</html>

<?php $conn->close(); ?>
