<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

$id = sanitize_input($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = sanitize_input($_POST['student_name']);
    $mobile = sanitize_input($_POST['mobile']);
    $alt_mobile = sanitize_input($_POST['alt_mobile']);
    $email = sanitize_input($_POST['email']);
    $username = sanitize_input($_POST['username']);
    $address = sanitize_input($_POST['address']);
    $district = sanitize_input($_POST['district']);
    $pincode = sanitize_input($_POST['pincode']);
    $state = sanitize_input($_POST['state']);
    $country = sanitize_input($_POST['country']);
    $center_id = sanitize_input($_POST['center_id']);
    
    $image_path = $student['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            if($image_path && file_exists($image_path)) unlink($image_path);
            $new_filename = uniqid() . '.' . $ext;
            $image_path = "uploads/students/" . $new_filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        } else {
            $error = "Invalid image format";
        }
    }

    $id_card_path = $student['id_card'];
    if(isset($_FILES['id_card']) && $_FILES['id_card']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['id_card']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            if($id_card_path && file_exists($id_card_path)) unlink($id_card_path);
            $new_filename = uniqid() . '_id.' . $ext;
            $id_card_path = "uploads/id_cards/" . $new_filename;
            move_uploaded_file($_FILES['id_card']['tmp_name'], $id_card_path);
        } else {
            $error = "Invalid ID card format";
        }
    }

    if (!isset($error)) {
        try {
            $sql = "UPDATE students SET student_name = ?, mobile = ?, alt_mobile = ?, email = ?, username = ?, 
                    address = ?, district = ?, pincode = ?, state = ?, country = ?, image = ?, id_card = ?, center_id = ? 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$student_name, $mobile, $alt_mobile, $email, $username, $address, $district, $pincode, $state, $country, $image_path, $id_card_path, $center_id, $id]);
            $success = "Student updated successfully!";
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

$centers = $conn->query("SELECT id, center_name FROM centers")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .container { max-width: 800px; margin-top: 20px; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .current-image { max-width: 100px; }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    <div class="container">
        <div class="card p-4">
            <h2 class="mb-4">Edit Student</h2>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Student Name</label>
                        <input type="text" name="student_name" class="form-control" value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($student['mobile']); ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Alternate Mobile</label>
                        <input type="text" name="alt_mobile" class="form-control" value="<?php