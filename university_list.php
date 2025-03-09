<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM universities");
$universities = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['delete'])) {
    $id = sanitize_input($_GET['delete']);
    $conn->prepare("DELETE FROM universities WHERE id = ?")->execute([$id]);
    header("Location: university_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>University List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin-top: 20px; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    <div class="container">
        <div class="card p-4">
            <h2 class="mb-4">University List</h2>
            <table class="table table-striped table-responsive">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Website</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($universities as $uni): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($uni['uni_name']); ?></td>
                        <td><?php echo htmlspecialchars($uni['website']); ?></td>
                        <td><?php echo htmlspecialchars($uni['country']); ?></td>
                        <td><?php echo htmlspecialchars($uni['state']); ?></td>
                        <td><?php echo htmlspecialchars($uni['address']); ?></td>
                        <td>
                            <a href="edit_university.php?id=<?php echo $uni['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="?delete=<?php echo $uni['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="add_university.php" class="btn btn-primary">Add New University</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>