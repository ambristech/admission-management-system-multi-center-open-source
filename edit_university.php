<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

$id = sanitize_input($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM universities WHERE id = ?");
$stmt->execute([$id]);
$university = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uni_name = sanitize_input($_POST['uni_name']);
    $website = sanitize_input($_POST['website']);
    $state = sanitize_input($_POST['state']);
    $country = sanitize_input($_POST['country']);
    $address = sanitize_input($_POST['address']);

    try {
        $sql = "UPDATE universities SET uni_name = ?, website = ?, state = ?, country = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uni_name, $website, $state, $country, $address, $id]);
        $success = "University updated successfully!";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit University</title>
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
            <h2 class="mb-4">Edit University</h2>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">University Name</label>
                    <input type="text" name="uni_name" class="form-control" value="<?php echo htmlspecialchars($university['uni_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control" value="<?php echo htmlspecialchars($university['website']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($university['address']); ?></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($university['state']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($university['country']); ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update University</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>