<?php
// เริ่มต้น session
session_start();
include('database.php');

// ตรวจสอบว่าเป็น Admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
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

// อัปเดตสถานะคำร้อง
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    // อัปเดตสถานะในฐานข้อมูล
    $query = "UPDATE change_requests SET status = '$status' WHERE request_id = '$request_id'";
    if ($conn->query($query)) {
        $success = "คำร้องที่คุณเลือกได้ถูกอัปเดตเป็นสถานะ $status แล้ว!";
    } else {
        $error = "เกิดข้อผิดพลาดในการอัปเดตสถานะ!";
    }
}

// ดึงข้อมูลคำร้องที่ยังรอการดำเนินการ (pending)
$query_pending = "SELECT cr.request_id, u.name AS barber_name, cr.request_type, cr.request_details, cr.status 
                  FROM change_requests cr
                  JOIN users u ON cr.user_id = u.user_id
                  WHERE cr.status = 'pending'";
$result_pending = $conn->query($query_pending);

// ดึงข้อมูลคำร้องที่ถูกยืนยันแล้ว (approved หรือ rejected)
$query_approved = "SELECT cr.request_id, u.name AS barber_name, cr.request_type, cr.request_details, cr.status 
                   FROM change_requests cr
                   JOIN users u ON cr.user_id = u.user_id
                   WHERE cr.status IN ('approved', 'rejected')";
$result_approved = $conn->query($query_approved);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคำร้อง</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap"
        rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">   

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/table.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />
</head>
<body class="sub_page">
    <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
    
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
                        <a class="navbar-brand" href="index.html">
                            <span>BBshop</span>
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
    </div>
    
    <section class="service_section layout_padding ">
        <div class="container">
            <div class="heading_container heading_center">
                <h2>Manage requests</h2>
            </div>
    </section>

    <br><br>
    <div class="container">
        <div class="booking-section">
            <div class="heading_container heading_center">
                <h2>requests</h2>
            </div>
            <br>
            <?php if ($result_pending->num_rows > 0): ?>
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NAME</th>
                            <th>TYPE</th>
                            <th>discretion</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        <?php while ($row = $result_pending->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($row['barber_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['request_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['request_details']); ?></td>
                                <td class="status <?php echo htmlspecialchars($row['status']); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </td>
                                <td>
                                    <form action="manage_requests.php" method="POST">
                                        <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                        <select name="status">
                                            <option value="approved">Approve</option>
                                            <option value="rejected">Reject</option>
                                        </select>
                                        <button type="submit" class="btn btn-warning btn-sm">Confirm</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">ไม่มีคำร้องที่รอการดำเนินการ</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
