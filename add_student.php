<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = sanitize_input($_POST['student_name']);
    $mobile = sanitize_input($_POST['mobile']);
    $alt_mobile = sanitize_input($_POST['alt_mobile']);
    $email = sanitize_input($_POST['email']);
    $username = sanitize_input($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = sanitize_input($_POST['address']);
    $district = sanitize_input($_POST['district']);
    $pincode = sanitize_input($_POST['pincode']);
    $state = sanitize_input($_POST['state']);
    $country = sanitize_input($_POST['country']);
    $center_id = sanitize_input($_POST['center_id']);
    
    $image_path = "";
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $image_path = "uploads/students/" . $new_filename;
            if (!file_exists('uploads/students')) mkdir('uploads/students', 0777, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        } else {
            $error = "Invalid image format";
        }
    }

    $id_card_path = "";
    if(isset($_FILES['id_card']) && $_FILES['id_card']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['id_card']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $new_filename = uniqid() . '_id.' . $ext;
            $id_card_path = "uploads/id_cards/" . $new_filename;
            if (!file_exists('uploads/id_cards')) mkdir('uploads/id_cards', 0777, true);
            move_uploaded_file($_FILES['id_card']['tmp_name'], $id_card_path);
        } else {
            $error = "Invalid ID card format";
        }
    }

    if (!isset($error)) {
        try {
            $conn->beginTransaction();
            $sql = "INSERT INTO students (student_name, mobile, alt_mobile, email, username, password, address, district, pincode, state, country, image, id_card, center_id) 
                    VALUES (:student_name, :mobile, :alt_mobile, :email, :username, :password, :address, :district, :pincode, :state, :country, :image, :id_card, :center_id)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':student_name' => $student_name,
                ':mobile' => $mobile,
                ':alt_mobile' => $alt_mobile,
                ':email' => $email,
                ':username' => $username,
                ':password' => $password,
                ':address' => $address,
                ':district' => $district,
                ':pincode' => $pincode,
                ':state' => $state,
                ':country' => $country,
                ':image' => $image_path,
                ':id_card' => $id_card_path,
                ':center_id' => $center_id
            ]);
            $student_id = $conn->lastInsertId();

            $sql = "INSERT INTO users (username, password, role, student_id) VALUES (:username, :password, 'student', :student_id)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':password' => $password,
                ':student_id' => $student_id
            ]);

            $conn->commit();
            $success = "Student added successfully!";
        } catch(PDOException $e) {
            $conn->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }
}

$centers = $conn->query("SELECT id, center_name FROM centers")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
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
            <h2 class="mb-4">Add New Student</h2>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Student Name</label>
                        <input type="text" name="student_name" class="form-control" required>
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
                    <label class="form-label">Center</label>
                    <select name="center_id" class="form-control" required>
                        <option value="">Select Center</option>
                        <?php foreach($centers as $center): ?>
                            <option value="<?php echo $center['id']; ?>"><?php echo htmlspecialchars($center['center_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png">
                </div>
                <div class="mb-3">
                    <label class="form-label">ID Card (JPG, PNG, PDF)</label>
                    <input type="file" name="id_card" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
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
                <div class="mb-3">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Student</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>