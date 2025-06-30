<?php
// Database connection (same as your main file)
$conn = new mysqli("sql307.infinityfree.com", "if0_39203974", "CD67rvMYDo", "if0_39203974_easystudy");
if ($conn->connect_error) die("Database connection failed.");

// Get filter parameters from AJAX request
$keyword = $_POST['keyword'] ?? '';
$standard = $_POST['standard'] ?? '';
$subject = $_POST['subject'] ?? '';

// Build the WHERE clause for the query
$whereClause = [];
$params = [];
$types = '';

if (!empty($keyword)) {
    $whereClause[] = "(set_name LIKE ? OR subject LIKE ? OR standard LIKE ?)";
    $params[] = '%' . $keyword . '%';
    $params[] = '%' . $keyword . '%';
    $params[] = '%' . $keyword . '%';
    $types .= 'sss';
}
if (!empty($standard)) {
    $whereClause[] = "standard = ?";
    $params[] = $standard;
    $types .= 's';
}
if (!empty($subject)) {
    $whereClause[] = "subject = ?";
    $params[] = $subject;
    $types .= 's';
}

$sql = "SELECT * FROM study_materials";
if (!empty($whereClause)) {
    $sql .= " WHERE " . implode(" AND ", $whereClause);
}
$sql .= " ORDER BY FIELD(standard, '10th', '11th', '12th'), subject, set_name";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        ?>
        <div style="border:2px solid #23538b; padding:10px; width:180px; border-radius:8px; background:#fff;">
          <div style="text-align:center; font-weight:bold; color:#23538b; margin-bottom:8px;">
            <?= htmlspecialchars($row['set_name'] ?: 'SET') ?>
          </div>
          <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" style="display:flex; flex-direction:column; align-items:center; text-decoration:none;">
            <img src="scroll-blue.png" alt="PDF" style="width:80px; margin-bottom:8px;">
            <span style="color:#1a2e44; font-weight:bold;"><?= strtoupper(htmlspecialchars($row['subject'])) ?></span>
          </a>
          <div style="font-size:12px; color:#555; margin-top:6px; text-align:center;">
            <?= htmlspecialchars($row['standard']) ?>
          </div>
        </div>
        <?php
    }
} else {
    echo '<p>No study materials found matching your criteria.</p>';
}

$stmt->close();
$conn->close();
?>