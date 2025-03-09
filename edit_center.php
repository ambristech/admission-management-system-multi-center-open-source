<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

$id = sanitize_input($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM centers WHERE id = ?");
$stmt->execute([$id]);
$center = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $center_name = sanitize_input($_POST['center_name']);
    $mobile = sanitize_input($_POST['mobile']);
    $alt_mobile = sanitize_input($_POST['alt_mobile']);
    $email = sanitize_input($_POST['email']);
    $username = sanitize_input($_POST['username']);
    $address = sanitize_input($_POST['address']);
    $district = sanitize_input($_POST['district']);
    $pincode = sanitize_input($_POST['pincode']);
    $state = sanitize_input($_POST['state']);
    
    $image_path = $center['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            if($image_path && file_exists($image_path)) unlink($image_path);
            $new_filename = uniqid() . '.' . $ext;
            $image_path = "uploads/centers/" . $new_filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        } else {
            $error = "Invalid image format";
        }
    }

    if (!isset($error)) {
        try {
            $sql = "UPDATE centers SET center_name = ?, mobile = ?, alt_mobile = ?, email = ?, username = ?, 
                    address = ?, district = ?, pincode = ?, state = ?, image = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$center_name, $mobile, $alt_mobile, $email, $username, $address, $district, $pincode, $state, $image_path, $id]);
            $success = "Center updated successfully!";
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Center</title>
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
            <h2 class="mb-4">Edit Center</h2>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Center Name</label>
                        <input type="text" name="center_name" class="form-control" value="<?php echo htmlspecialchars($center['center_name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($center['mobile']); ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Alternate Mobile</label>
                        <input type="text" name="alt_mobile" class="form-control" value="<?php echo htmlspecialchars($center['alt_mobile']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($center['email']); ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($center['username']); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Current Image</label><br>
                    <img src="<?php echo htmlspecialchars($center['image'] ?: 'placeholder.jpg'); ?>" class="current-image" alt="Current Image">
                </div>
                <div class="mb-3">
                    <label class="form-label">New Image</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($center['address']); ?></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">District</label>
                        <input type="text" name="district" class="form-control" value="<?php echo htmlspecialchars($center['district']); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="<?php echo htmlspecialchars($center['pincode']); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($center['state']); ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Center</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>