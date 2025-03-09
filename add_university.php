<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uni_name = sanitize_input($_POST['uni_name']);
    $website = sanitize_input($_POST['website']);
    $state = sanitize_input($_POST['state']);
    $country = sanitize_input($_POST['country']);
    $address = sanitize_input($_POST['address']);

    try {
        $sql = "INSERT INTO universities (uni_name, website, state, country, address) 
                VALUES (:uni_name, :website, :state, :country, :address)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':uni_name' => $uni_name,
            ':website' => $website,
            ':state' => $state,
            ':country' => $country,
            ':address' => $address
        ]);
        $success = "University added successfully!";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add University</title>
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
            <h2 class="mb-4">Add New University</h2>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">University Name</label>
                    <input type="text" name="uni_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3" required></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add University</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>