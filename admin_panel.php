<?php include 'header.php'; ?>

<?php
// DB Connection
$conn = new mysqli("sql307.infinityfree.com", "if0_39203974", "CD67rvMYDo", "if0_39203974_easystudy");
if ($conn->connect_error) die("DB Error");

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
  $subject = $_POST['subject'];
  $standard = $_POST['standard'];
  $set = $_POST['set'];
  $file = $_FILES['file'];
  $filename = basename($file['name']);
  $dest = 'uploads/' . $filename;

  if (move_uploaded_file($file['tmp_name'], $dest)) {
    $stmt = $conn->prepare("INSERT INTO study_materials (subject, standard, set_name, file_name, file_path, uploaded_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $subject, $standard, $set, $filename, $dest);
    $stmt->execute();
    echo "<p style='color:green;'>✅ File uploaded successfully</p>";
  } else {
    echo "<p style='color:red;'>❌ Upload failed</p>";
  }
}

// Handle delete
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM study_materials WHERE id = $id");
  echo "<p style='color:red;'>🗑️ Material deleted.</p>";
}

// Statistics
$total = $conn->query("SELECT COUNT(*) AS c FROM study_materials")->fetch_assoc()['c'];
$subjects = $conn->query("SELECT COUNT(DISTINCT subject) AS c FROM study_materials")->fetch_assoc()['c'];
$latest = $conn->query("SELECT MAX(uploaded_at) AS d FROM study_materials")->fetch_assoc()['d'];
$stds = ['10th','11th','12th'];
$std_counts = [];
foreach ($stds as $st) {
  $std_counts[$st] = $conn->query("SELECT COUNT(*) AS c FROM study_materials WHERE standard = '$st'")->fetch_assoc()['c'];
}

// Search
$filter = '';
if (!empty($_GET['q'])) {
  $q = $conn->real_escape_string($_GET['q']);
  $filter = "WHERE subject LIKE '%$q%' OR standard LIKE '%$q%'";
}
$materials = $conn->query("SELECT * FROM study_materials $filter ORDER BY uploaded_at DESC");
?>

<!-- Statistics Cards -->
<div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:30px;">
  <div style="flex:1;padding:15px;background:#23538b;color:white;border-radius:8px;">
    <h3>Total Materials</h3><p style="font-size:22px;"><?= $total ?></p>
  </div>
  <div style="flex:1;padding:15px;background:#337ab7;color:white;border-radius:8px;">
    <h3>Subjects</h3><p style="font-size:22px;"><?= $subjects ?></p>
  </div>
  <div style="flex:1;padding:15px;background:#5cb85c;color:white;border-radius:8px;">
    <h3>Latest Upload</h3><p><?= $latest ?></p>
  </div>
  <?php foreach($std_counts as $std => $count): ?>
    <div style="flex:1;padding:15px;background:#<?= $std == '10th' ? 'f0ad4e' : ($std == '11th' ? 'd9534f' : '6f42c1') ?>;color:white;border-radius:8px;">
      <h3><?= $std ?> Std</h3><p style="font-size:22px;"><?= $count ?></p>
    </div>
  <?php endforeach; ?>
</div>

<!-- Upload Form -->
<h2>📤 Upload New Study Material</h2>
<form method="POST" enctype="multipart/form-data" style="margin-bottom:30px;">
  <input type="text" name="subject" placeholder="Subject" required>
  <select name="standard" required>
    <option value="">Select Standard</option>
    <option value="10th">10th</option>
    <option value="11th">11th</option>
    <option value="12th">12th</option>
  </select>
  <input type="text" name="set" placeholder="Set Name (e.g. Set 10)">
  <input type="file" name="file" required>
  <button type="submit">Upload</button>
</form>

<!-- Filter/Search Bar -->
<form method="GET" style="margin-bottom:20px;">
  <input type="text" name="q" placeholder="Search by subject or class" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
  <button type="submit">Search</button>
</form>

<!-- Listing Table -->
<h2>📚 Uploaded Materials</h2>
<table border="1" cellpadding="8" cellspacing="0" style="width:100%; background:white;">
  <tr style="background:#eee;">
    <th>ID</th><th>Subject</th><th>Standard</th><th>Set</th><th>File</th><th>Uploaded At</th><th>Actions</th>
  </tr>
  <?php while($row = $materials->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['subject']) ?></td>
      <td><?= $row['standard'] ?></td>
      <td><?= $row['set_name'] ?></td>
      <td><a href="<?= $row['file_path'] ?>" target="_blank">View</a></td>
      <td><?= $row['uploaded_at'] ?></td>
      <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this?')">🗑️ Delete</a></td>
    </tr>
  <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>
