<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $userid = trim($_POST["userid"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($email) || empty($userid) || empty($password)) {
        echo "Please fill in all fields.";
        exit;
    }

    // Check for existing user ID or email
    $stmt = $conn->prepare("SELECT id FROM users WHERE userid = ? OR email = ?");
    $stmt->bind_param("ss", $userid, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('User ID or Email already exists!'); window.location.href = 'signup.html';</script>";
        exit;
    }

    $stmt->close();

    // Hash password and insert
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (email, userid, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $userid, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>
            alert('🎉 Congratulations! You are now registered.');
            window.location.href = 'profile.php';
        </script>";
        exit;
    } else {
        echo "Something went wrong: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
