<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .container { max-width: 800px; margin-top: 20px; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .profile-image { max-width: 100px; border-radius: 50%; }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    <div class="container">
        <div class="card p-4">
            <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            
            <?php if($_SESSION['role'] == 'super_admin'): ?>
                <h4>Super Admin Dashboard</h4>
                <p>Manage the entire system:</p>
                <ul class="list-group">
                    <li class="list-group-item"><a href="center_list.php">Manage Centers</a></li>
                    <li class="list-group-item"><a href="university_list.php">Manage Universities</a></li>
                    <li class="list-group-item"><a href="course_list.php">Manage Courses</a></li>
                    <li class="list-group-item"><a href="student_list.php">Manage Students</a></li>
                </ul>
            <?php elseif($_SESSION['role'] == 'center_admin'): ?>
                <h4>Center Admin Dashboard</h4>
                <p>Manage your center's operations:</p>
                <?php
                $stmt = $conn->prepare("SELECT center_name, image FROM centers WHERE id = ?");
                $stmt->execute([$_SESSION['center_id']]);
                $center = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <img src="<?php echo htmlspecialchars($center['image'] ?: 'placeholder.jpg'); ?>" class="profile-image mb-3" alt="Center Image">
                <p>Center: <?php echo htmlspecialchars($center['center_name']); ?></p>
                <ul class="list-group">
                    <li class="list-group-item"><a href="center_course_list.php">View Courses</a></li>
                    <li class="list-group-item"><a href="center_student_list.php">Manage Students</a></li>
                </ul>
            <?php elseif($_SESSION['role'] == 'student'): ?>
                <h4>Student Dashboard</h4>
                <p>Your information:</p>
                <?php
                $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
                $stmt->execute([$_SESSION['student_id']]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <img src="<?php echo htmlspecialchars($student['image'] ?: 'placeholder.jpg'); ?>" class="profile-image mb-3" alt="Student Image">
                <ul class="list-group">
                    <li class="list-group-item">Name: <?php echo htmlspecialchars($student['student_name']); ?></li>
                    <li class="list-group-item">Email: <?php echo htmlspecialchars($student['email']); ?></li>
                    <li class="list-group-item">Mobile: <?php echo htmlspecialchars($student['mobile']); ?></li>
                    <li class="list-group-item"><a href="course_list.php">View Available Courses</a></li>
                    <li class="list-group-item"><a href="student_profile.php">View Full Profile</a></li>
                    <li class="list-group-item"><a href="student_admissions.php">View My Admissions</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>