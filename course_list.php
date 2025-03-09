<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM courses");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SESSION['role'] == 'super_admin' && isset($_GET['delete'])) {
    $id = sanitize_input($_GET['delete']);
    $conn->prepare("DELETE FROM courses WHERE id = ?")->execute([$id]);
    header("Location: course_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .container { max-width: 1000px; margin-top: 20px; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    <div class="container">
        <div class="card p-4">
            <h2 class="mb-4">Course List</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Details</th>
                        <th>Eligibility</th>
                        <?php if($_SESSION['role'] == 'super_admin'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['course_detail']); ?></td>
                        <td><?php echo htmlspecialchars($course['eligibility']); ?></td>
                        <?php if($_SESSION['role'] == 'super_admin'): ?>
                            <td>
                                <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="?delete=<?php echo $course['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if($_SESSION['role'] == 'super_admin'): ?>
                <a href="add_course.php" class="btn btn-primary">Add New Course</a>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>