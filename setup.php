<?php
require_once "db_connect.php";

// Check if any users exist
$stmt = $conn->query("SELECT COUNT(*) FROM users");
$count = $stmt->fetchColumn();

if ($count == 1) {
    $username = "admin";
    $password = password_hash("admin123", PASSWORD_DEFAULT); // Default password: admin123
    
    try {
        $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'super_admin')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':password' => $password
        ]);
        echo "Initial admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "Please login and change the password.";
    } catch(PDOException $e) {
        echo "Error creating initial user: " . $e->getMessage();
    }
} else {
    echo "Users already exist in the system.";
}
?>