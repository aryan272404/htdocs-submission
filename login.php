<?php
session_start();
require_once 'db.php'; // include your DB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userid = trim($_POST["userid"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($userid) || empty($password)) {
        echo "Please fill in all fields.";
        exit;
    }

    // Prevent SQL injection
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE userid = ?");
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Login success
            $_SESSION["userid"] = $userid;
            $_SESSION["user_id"] = $id;

            header("Location: dashboard.php");
            exit;
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User ID not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
