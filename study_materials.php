<?php include 'header.php'; ?>

<?php
// Database connection
// It's generally better to use constants or configuration files for database credentials.
// Assuming 'config.php' might contain this, but replicating here as it was in the original snippet.
$conn = new mysqli("sql307.infinityfree.com", "if0_39203974", "CD67rvMYDo", "if0_39203974_easystudy");
if ($conn->connect_error) {
    // Log the error instead of dying directly in a production environment
    error_log("Database connection failed: " . $conn->connect_error);
    echo '<div class="text-red-500 p-4 bg-red-100 rounded-md text-center">Failed to connect to the database. Please try again later.</div>';
    exit(); // Exit gracefully
}

// Fetch all materials initially. This will be replaced by AJAX for filtering.
// ORDER BY FIELD is used to ensure standards appear in a logical order.
$query = "
    SELECT * FROM study_materials
    ORDER BY FIELD(standard, '10th', '11th', '12th'), subject, set_name
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Materials - Easy Study</title>
    <!-- Tailwind CSS CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
        }
        .material-card {
            min-width: 180px; /* Minimum width for cards */
            flex-grow: 1; /* Allow cards to grow */
        }
        /* Custom styles for dark mode if needed, assuming Tailwind handles most */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #111827; /* Darker background */
                color: #e5e7eb; /* Light text */
            }
            .bg-white {
                background-color: #1f2937; /* Darker card background */
            }
            .text-gray-900 {
                color: #e5e7eb; /* Light text for titles */
            }
            .text-gray-500 {
                color: #9ca3af; /* Lighter gray for secondary text */
            }
            .border-gray-200 {
                border-color: #4b5563; /* Darker border */
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 transition-all duration-300">

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h2 class="text-3xl font-bold mb-6 text-blue-800 dark:text-blue-400 text-center">📁 All Study Materials</h2>

    <!-- Search and Filter Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <input type="text" id="searchKeyword" placeholder="Search by keyword..."
               class="p-3 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 w-full sm:w-auto flex-grow focus:ring-blue-500 focus:border-blue-500 transition-colors">
        
        <select id="filterStandard"
                class="p-3 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 w-full sm:w-auto focus:ring-blue-500 focus:border-blue-500 transition-colors">
            <option value="">All Standards</option>
            <option value="10th">10th</option>
            <option value="11th">11th</option>
            <option value="12th">12th</option>
        </select>
        
        <select id="filterSubject"
                class="p-3 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 w-full sm:w-auto focus:ring-blue-500 focus:border-blue-500 transition-colors">
            <option value="">All Subjects</option>
            <option value="Mathematics">Mathematics</option>
            <option value="Science">Science</option>
            <option value="Physics">Physics</option>
            <option value="Chemistry">Chemistry</option>
            <option value="Biology">Biology</option>
            <option value="English">English</option>
            <option value="Gujarati">Gujarati</option>
            <option value="Computer">Computer</option>
        </select>
        
        <button id="applyFilterBtn"
                class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-semibold rounded-md shadow-md transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
            Search & Filter
        </button>
    </div>

    <!-- Materials Container -->
    <div id="materialsContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="material-card border-2 border-blue-600 dark:border-blue-500 p-4 rounded-xl bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                    <div class="text-center font-bold text-blue-800 dark:text-blue-400 mb-3 text-lg">
                        <?= htmlspecialchars($row['set_name'] ?: 'SET') ?>
                    </div>
                    <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank"
                       class="flex flex-col items-center text-center no-underline">
                        <!-- Reverted to scroll-blue.png as per user request -->
                        <!-- Ensure 'scroll-blue.png' exists in your web root or appropriate path -->
                        <img src="scroll-blue.png" alt="PDF Icon"
                             class="w-20 h-20 object-contain mb-3 rounded-md">
                        <span class="text-gray-900 dark:text-gray-100 font-bold text-base block mt-2"><?= strtoupper(htmlspecialchars($row['subject'])) ?></span>
                    </a>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 text-center">
                        Standard: <?= htmlspecialchars($row['standard']) ?>
                    </div>
                    <?php if (!empty($row['file_name'])): ?>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1 text-center truncate">
                            File: <?= htmlspecialchars($row['file_name']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="col-span-full text-center text-gray-600 dark:text-gray-400 text-lg">No study materials found.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('#applyFilterBtn').on('click', function(){
        var searchKeyword = $('#searchKeyword').val();
        var filterStandard = $('#filterStandard').val();
        var filterSubject = $('#filterSubject').val();

        $.ajax({
            url: 'fetch_materials.php', // A new PHP file to handle AJAX requests for filtered data
            type: 'POST',
            data: {
                keyword: searchKeyword,
                standard: filterStandard,
                subject: filterSubject
            },
            success: function(response){
                // Update the materials container with the new results
                $('#materialsContainer').html(response);
            },
            error: function(xhr, status, error){
                console.error("AJAX Error: " + status + ", " + error);
                $('#materialsContainer').html('<p class="col-span-full text-center text-red-500">Error loading materials. Please try again.</p>');
            }
        });
    });
});
</script>

<?php
// Close the database connection at the very end of the script
if ($conn instanceof mysqli && !$conn->connect_error) {
    $conn->close();
}
?>
</body>
</html>
<?php include 'footer.php'; ?>
