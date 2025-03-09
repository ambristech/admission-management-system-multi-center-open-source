<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: login.php");
    exit();
}

// Fetch all universities for the dropdown
$stmt = $conn->query("SELECT id, uni_name FROM universities ORDER BY uni_name");
$universities = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = sanitize_input($_POST['course_name']);
    $course_detail = sanitize_input($_POST['course_detail']);
    $eligibility = sanitize_input($_POST['eligibility']);
    $university_ids = $_POST['university_id'] ?? []; // Array of selected university IDs
    $fees = $_POST['fees'] ?? []; // Array of fees corresponding to universities

    // Validate inputs
    if (empty($course_name)) {
        $error = "Course name is required";
    } elseif (count($university_ids) !== count(array_unique($university_ids))) {
        $error = "Duplicate universities selected for the same course";
    } else {
        try {
            $conn->beginTransaction();

            // Insert the course
            $sql = "INSERT INTO courses (course_name, course_detail, eligibility) VALUES (:course_name, :course_detail, :eligibility)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':course_name' => $course_name,
                ':course_detail' => $course_detail,
                ':eligibility' => $eligibility
            ]);
            $course_id = $conn->lastInsertId();

            // Insert university associations with fees
            if (!empty($university_ids)) {
                $sql = "INSERT INTO course_university (course_id, university_id, fees) VALUES (:course_id, :university_id, :fees)";
                $stmt = $conn->prepare($sql);
                for ($i = 0; $i < count($university_ids); $i++) {
                    $university_id = sanitize_input($university_ids[$i]);
                    $fee = !empty($fees[$i]) ? floatval($fees[$i]) : null;
                    $stmt->execute([
                        ':course_id' => $course_id,
                        ':university_id' => $university_id,
                        ':fees' => $fee
                    ]);
                }
            }

            $conn->commit();
            $success = "Course and university associations added successfully!";
        } catch(PDOException $e) {
            $conn->rollBack();
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'unique_course_university') !== false) {
                $error = "One or more universities are already associated with this course";
            } else {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .container { max-width: 800px; margin-top: 20px; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .university-row { margin-bottom: 15px; }
        .remove-btn { margin-top: 32px; }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    <div class="container">
        <div class="card p-4">
            <h2 class="mb-4">Add New Course</h2>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST" id="courseForm">
                <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" value="<?php echo isset($_POST['course_name']) ? htmlspecialchars($_POST['course_name']) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Course Details</label>
                    <textarea name="course_detail" class="form-control" rows="3"><?php echo isset($_POST['course_detail']) ? htmlspecialchars($_POST['course_detail']) : ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Eligibility</label>
                    <textarea name="eligibility" class="form-control" rows="3"><?php echo isset($_POST['eligibility']) ? htmlspecialchars($_POST['eligibility']) : ''; ?></textarea>
                </div>

                <h4 class="mb-3">Associate Universities & Fees</h4>
                <div id="university-container">
                    <div class="row university-row">
                        <div class="col-md-6">
                            <label class="form-label">University</label>
                            <select name="university_id[]" class="form-control university-select" required>
                                <option value="">Select University</option>
                                <?php foreach($universities as $uni): ?>
                                    <option value="<?php echo $uni['id']; ?>"><?php echo htmlspecialchars($uni['uni_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fees (optional)</label>
                            <input type="number" name="fees[]" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-btn" style="display:none;">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-2" id="add-university">Add Another University</button>
                <button type="submit" class="btn btn-primary mt-3">Add Course</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('add-university').addEventListener('click', function() {
            const container = document.getElementById('university-container');
            const row = container.querySelector('.university-row').cloneNode(true);
            row.querySelector('select').value = '';
            row.querySelector('input').value = '';
            row.querySelector('.remove-btn').style.display = 'inline-block';
            row.querySelector('.remove-btn').addEventListener('click', function() {
                row.remove();
            });
            container.appendChild(row);
        });

        // Validate no duplicate universities on submit
        document.getElementById('courseForm').addEventListener('submit', function(e) {
            const selects = document.querySelectorAll('.university-select');
            const values = Array.from(selects).map(select => select.value).filter(value => value !== '');
            const uniqueValues = new Set(values);
            if (values.length !== uniqueValues.size) {
                e.preventDefault();
                alert('Duplicate universities selected. Each university can only be associated once.');
            }
        });
    </script>
</body>
</html>