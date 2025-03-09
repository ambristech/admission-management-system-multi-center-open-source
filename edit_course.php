<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

$id = sanitize_input($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = sanitize_input($_POST['course_name']);
    $course_detail = sanitize_input($_POST['course_detail']);
    $eligibility = sanitize_input($_POST['eligibility']);

    try {
        $sql = "UPDATE courses SET course_name = ?, course_detail = ?, eligibility = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$course_name, $course_detail, $eligibility, $id]);
        $success = "Course updated successfully!";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Course</title>
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
            <h2 class="mb-4">Edit Course</h2>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Course Details</label>
                    <textarea name="course_detail" class="form-control" rows="3"><?php echo htmlspecialchars($course['course_detail']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Eligibility</label>
                    <textarea name="eligibility" class="form-control" rows="3"><?php echo htmlspecialchars($course['eligibility']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Course</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>