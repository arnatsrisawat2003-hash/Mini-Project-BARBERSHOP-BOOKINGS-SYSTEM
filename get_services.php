<?php
include('database.php');

if (isset($_POST['type_id'])) {
    $type_id = $_POST['type_id'];
    $sql = "SELECT * FROM services WHERE type_id = $type_id";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['service_id']}'>{$row['service_name']}</option>";
    }
}
?>
