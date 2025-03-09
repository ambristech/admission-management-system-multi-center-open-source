<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $center_name = sanitize_input($_POST['center_name']);
    $mobile = sanitize_input($_POST['mobile']);
    $alt_mobile = sanitize_input($_POST['alt_mobile']);
    $email = sanitize_input($_POST['email']);
    $username = sanitize_input($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = sanitize_input($_POST['address']);
    $district = sanitize_input($_POST['district']);
    $pincode = sanitize_input($_POST['pincode']);
    $state = sanitize_input($_POST['state']);
    
    $image_path = "";
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $image_path = "uploads/centers/" . $new_filename;
            if (!file_exists('uploads/centers')) mkdir('uploads/centers', 0777, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        } else {
            $error = "Invalid image format";
        }
    }

    if (!isset($error)) {
        try {
            $conn->beginTransaction();
            $sql = "INSERT INTO centers (center_name, mobile, alt_mobile, email, username, password, address, district, pincode, state, image) 
                    VALUES (:center_name, :mobile, :alt_mobile, :email, :username, :password, :address, :district, :pincode, :state, :image)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':center_name' => $center_name,
                ':mobile' => $mobile,
                ':alt_mobile' => $alt_mobile,
                ':email' => $email,
                ':username' => $username,
                ':password' => $password,
                ':address' => $address,
                ':district' => $district,
                ':pincode' => $pincode,
                ':state' => $state,
                ':image' => $image_path
            ]);
            $center_id = $conn->lastInsertId();

            $sql = "INSERT INTO users (username, password, role, center_id) VALUES (:username, :password, 'center_admin', :center_id)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':password' => $password,
                ':center_id' => $center_id
            ]);

            $conn->commit();
            $success = "Center added successfully!";
        } catch(PDOException $e) {
            $conn->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .container { max-width: 800px; margin-top: 20px; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    <div class="container">
        <div class="card p-4">
            <h2 class="mb-4">Add New Center</h2>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Center Name</label>
                        <input type="text" name="center_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Alternate Mobile</label>
                        <input type="text" name="alt_mobile" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3" required></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">District</label>
                        <input type="text" name="district" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Center</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>