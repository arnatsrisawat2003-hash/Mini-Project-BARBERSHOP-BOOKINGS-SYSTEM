<?php
session_start();
include('database.php');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่าได้รับ service_id จาก URL หรือไม่
if (!isset($_GET['service_id'])) {
    header("Location: edit_services.php"); // ถ้าไม่มีก็จะส่งกลับไปที่หน้าจัดการบริการ
    exit();
}

// ดึงข้อมูลบริการที่ต้องการแก้ไข
$service_id = $_GET['service_id'];
$sql_service = "SELECT * FROM services WHERE service_id = ?";
$stmt = $conn->prepare($sql_service);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result_service = $stmt->get_result();

if ($result_service->num_rows == 0) {
    echo "บริการไม่พบ";
    exit();
}

$service = $result_service->fetch_assoc();

// การแก้ไขบริการ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];
    $price = $_POST['price'];
    $type_id = $_POST['type_id'];

    // อัปเดตข้อมูลบริการ
    $update_sql = "UPDATE services SET service_name = ?, service_description = ?, price = ?, type_id = ? WHERE service_id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("ssdii", $service_name, $service_description, $price, $type_id, $service_id);
    $stmt_update->execute();

    header("Location: edit_services.php"); // รีเฟรชหน้า
    exit();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลบริการ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .card {
            font-family: "Bebas Neue", sans-serif;
            max-width: 600px;
            margin: 50px auto;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
        }
        .btn-primary {
            width: 100%;
        }
        .btn-secondary {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-4">
        <h1 class="card-title mb-4">Edit service information</h1>

        <form action="edit_service.php?service_id=<?php echo $service['service_id']; ?>" method="POST">
            <div class="mb-3">
                <label for="service_name" class="form-label">Service Name:</label>
                <input type="text" class="form-control" id="service_name" name="service_name" 
                       value="<?php echo $service['service_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="service_description" class="form-label">Description:</label>
                <textarea class="form-control" id="service_description" name="service_description" rows="3" required><?php echo $service['service_description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price (บาท):</label>
                <input type="number" class="form-control" id="price" name="price" 
                       value="<?php echo $service['price']; ?>" required>
            </div>
            <div class="mb-4">
                <label for="type_id" class="form-label">Services Type:</label>
                <select class="form-select" id="type_id" name="type_id" required>
                    <option value="">-- Select Services Type --</option>
                    <?php
                    $sql_types = "SELECT * FROM service_types";
                    $stmt_types = $conn->prepare($sql_types);
                    $stmt_types->execute();
                    $result_types = $stmt_types->get_result();
                    while ($row = $result_types->fetch_assoc()) {
                        $selected = ($row['type_id'] == $service['type_id']) ? 'selected' : '';
                        echo "<option value='{$row['type_id']}' {$selected}>{$row['type_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mb-2"> Save the Edits.</button>
        </form>

        <a href="barber_services.php" class="btn btn-secondary"> Back</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
