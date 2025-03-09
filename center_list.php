<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM centers");
$centers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['delete'])) {
    $id = sanitize_input($_GET['delete']);
    $stmt = $conn->prepare("SELECT image FROM centers WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();
    if ($image && file_exists($image)) unlink($image);
    
    $conn->prepare("DELETE FROM users WHERE center_id = ?")->execute([$id]);
    $conn->prepare("DELETE FROM centers WHERE id = ?")->execute([$id]);
    header("Location: center_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Center List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin-top: 20px; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .center-image { max-width: 50px; }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    <div class="container">
        <div class="card p-4">
            <h2 class="mb-4">Center List</h2>
            <table class="table table-striped table-responsive">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Address</th>
                        <th>District</th>
                        <th>Pincode</th>
                        <th>State</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($centers as $center): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($center['image'] ?: 'placeholder.jpg'); ?>" class="center-image" alt="Center Image"></td>
                        <td><?php echo htmlspecialchars($center['center_name']); ?></td>
                        <td><?php echo htmlspecialchars($center['mobile']); ?></td>
                        <td><?php echo htmlspecialchars($center['email']); ?></td>
                        <td><?php echo htmlspecialchars($center['username']); ?></td>
                        <td><?php echo htmlspecialchars($center['address']); ?></td>
                        <td><?php echo htmlspecialchars($center['district']); ?></td>
                        <td><?php echo htmlspecialchars($center['pincode']); ?></td>
                        <td><?php echo htmlspecialchars($center['state']); ?></td>
                        <td>
                            <a href="edit_center.php?id=<?php echo $center['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="?delete=<?php echo $center['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="add_center.php" class="btn btn-primary">Add New Center</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>