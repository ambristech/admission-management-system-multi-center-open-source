<?php
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <?php 
            switch ($_SESSION['role']) {
                case 'super_admin': echo "Admin Panel"; break;
                case 'center_admin': echo "Center Panel"; break;
                case 'student': echo "Student Panel"; break;
                default: echo "User Panel";
            }
            ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if ($_SESSION['role'] == 'super_admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Centers</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="add_center.php">Add Center</a></li>
                            <li><a class="dropdown-item" href="center_list.php">Center List</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Universities</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="add_university.php">Add University</a></li>
                            <li><a class="dropdown-item" href="university_list.php">University List</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Courses</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="add_course.php">Add Course</a></li>
                            <li><a class="dropdown-item" href="course_list.php">Course List</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Students</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="add_student.php">Add Student</a></li>
                            <li><a class="dropdown-item" href="student_list.php">Student List</a></li>
                        </ul>
                    </li>
                <?php elseif ($_SESSION['role'] == 'center_admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="center_university_list.php">Universities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="center_course_list.php">Courses</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Students</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="center_add_student.php">Add Student</a></li>
                            <li><a class="dropdown-item" href="center_student_list.php">Student List</a></li>
                        </ul>
                    </li>
                <?php elseif ($_SESSION['role'] == 'student'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="student_profile.php">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="student_admissions.php">My Admissions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="course_list.php">Courses</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="change_password.php">Change Password</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>