<?php
session_start();

// Optional: Restrict access to admins only
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     die("Unauthorized access.");
// }

// DB credentials
$host = "sql307.infinityfree.com";
$dbname = "if0_39203974_easystudy";
$dbuser = "if0_39203974";
$dbpass = "CD67rvMYDo";

// DB connection
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

$upload_success = '';
$upload_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $standard = $_POST['standard'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $set_name = $_POST['set_name'] ?? '';

    if (!empty($standard) && !empty($subject) && !empty($set_name) && isset($_FILES['pdf_file'])) {
        $file = $_FILES['pdf_file'];
        $target_dir = "uploads/";
        $filename = basename($file['name']);
        $filepath = $target_dir . time() . "_" . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Save in DB
            $stmt = $conn->prepare("INSERT INTO study_materials (standard, subject, set_name, file_name, file_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $standard, $subject, $set_name, $filename, $filepath);
            if ($stmt->execute()) {
                $upload_success = "✅ File uploaded successfully!";
            } else {
                $upload_error = "❌ DB insert error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $upload_error = "❌ Failed to upload file.";
        }
    } else {
        $upload_error = "❌ All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Study Material</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 30px;
        }
        form {
            background: #fff;
            padding: 20px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 10px #ccc;
            border-radius: 10px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 12px;
        }
        button {
            padding: 10px 20px;
            background: #23538b;
            color: white;
            border: none;
            margin-top: 15px;
            cursor: pointer;
        }
        button:hover {
            background: #1a3f6b;
        }
        .message {
            margin-top: 10px;
            font-weight: bold;
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">📤 Upload Study Material (Admin Panel)</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Standard:</label>
    <select name="standard" required>
        <option value="">Select</option>
        <option value="10th">10th</option>
        <option value="11th">11th</option>
        <option value="12th">12th</option>
    </select>

    <label>Subject:</label>
    <input type="text" name="subject" placeholder="e.g., Maths, Science" required>

    <label>Set Name:</label>
    <input type="text" name="set_name" placeholder="e.g., Set 1, Set A" required>

    <label>PDF File:</label>
    <input type="file" name="pdf_file" accept="application/pdf" required>

    <button type="submit">Upload</button>

    <?php if ($upload_success): ?>
        <p class="message"><?php echo $upload_success; ?></p>
    <?php elseif ($upload_error): ?>
        <p class="message error"><?php echo $upload_error; ?></p>
    <?php endif; ?>
</form>

</body>
</html>
