<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'center_admin', 'student'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $filepath = realpath($file);

    // Security: Ensure file is within the uploads directory
    $base_dir = realpath("uploads");
    if ($filepath && strpos($filepath, $base_dir) === 0 && file_exists($filepath)) {
        // Check role-based access
        if ($_SESSION['role'] == 'center_admin') {
            $stmt = $conn->prepare("SELECT id_card FROM students WHERE id_card = ? AND center_id = ?");
            $stmt->execute([$file, $_SESSION['center_id']]);
            if (!$stmt->fetchColumn()) {
                die("Access denied");
            }
        } elseif ($_SESSION['role'] == 'student') {
            $stmt = $conn->prepare("SELECT id_card FROM students WHERE id_card = ? AND id = ?");
            $stmt->execute([$file, $_SESSION['student_id']]);
            if (!$stmt->fetchColumn()) {
                die("Access denied");
            }
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit();
    } else {
        die("File not found or access denied");
    }
}
?>