<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'center_admin' || !isset($_SESSION['center_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch courses linked to universities (center admins can view all available courses)
$stmt = $conn->query("
    SELECT c.id, c.course_name, c.course_detail, c.eligibility, u.uni_name, cu.fees 
    FROM courses c
    LEFT JOIN course_university cu ON c.id = cu.course_id
    LEFT JOIN universities u ON cu.university_id = u.id
");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Center Course List</title>
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
            <h2 class="mb-4">Available Courses</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Details</th>
                        <th>Eligibility</th>
                        <th>University</th>
                        <th>Fees</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['course_detail']); ?></td>
                        <td><?php echo htmlspecialchars($course['eligibility']); ?></td>
                        <td><?php echo htmlspecialchars($course['uni_name'] ?: 'Not Assigned'); ?></td>
                        <td><?php echo htmlspecialchars($course['fees'] ? number_format($course['fees'], 2) : 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>