<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM students");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['delete'])) {
    $id = sanitize_input($_GET['delete']);
    $stmt = $conn->prepare("SELECT image, id_card FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $files = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($files['image'] && file_exists($files['image'])) unlink($files['image']);
    if ($files['id_card'] && file_exists($files['id_card'])) unlink($files['id_card']);
    
    $conn->prepare("DELETE FROM users WHERE student_id = ?")->execute([$id]);
    $conn->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
    header("Location: student_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin-top: 20px; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .student-image { max-width: 50px; }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    <div class="container">
        <div class="card p-4">
            <h2 class="mb-4">Student List</h2>
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
                        <th>Country</th>
                        <th>ID Card</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $student): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($student['image'] ?: 'placeholder.jpg'); ?>" class="student-image" alt="Student Image"></td>
                        <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['mobile']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['username']); ?></td>
                        <td><?php echo htmlspecialchars($student['address']); ?></td>
                        <td><?php echo htmlspecialchars($student['district']); ?></td>
                        <td><?php echo htmlspecialchars($student['pincode']); ?></td>
                        <td><?php echo htmlspecialchars($student['state']); ?></td>
                        <td><?php echo htmlspecialchars($student['country']); ?></td>
                        <td>
                            <?php if($student['id_card']): ?>
                                <a href="download.php?file=<?php echo urlencode($student['id_card']); ?>" class="btn btn-sm btn-success">Download ID</a>
                            <?php else: ?>
                                <span>No ID</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="?delete=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="add_student.php" class="btn btn-primary">Add New Student</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>