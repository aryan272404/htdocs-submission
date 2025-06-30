<?php
require_once 'db.php'; // Ensure db.php correctly initializes $conn (mysqli object)
session_start(); // Start the session at the very top

// Check if the user has completed the initial signup process.
// These session variables are expected to be set by a previous signup script.
if (!isset($_SESSION['new_user_db_id']) || !isset($_SESSION['new_user_id_string']) || !isset($_SESSION['new_user_email'])) {
    $_SESSION['error_message'] = "Please complete the initial signup process first.";
    header("Location: signup.html"); // Redirect to signup if temporary session data is missing
    exit();
}

// Retrieve temporary user data from session
$user_db_id = $_SESSION['new_user_db_id'];
$user_id_string = $_SESSION['new_user_id_string'];
$user_email = $_SESSION['new_user_email'];

// Initialize profile_data for pre-filling the form in case of validation errors
// This holds values from the last POST submission if there was an error.
$profile_data_to_prefill = [];
if (isset($_SESSION['profile_data_error'])) {
    $profile_data_to_prefill = $_SESSION['profile_data_error'];
    unset($_SESSION['profile_data_error']); // Clear this temporary session data after use
}

// Display any error messages from previous redirects (e.g., if profile creation failed)
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the error message after displaying it
}

// Handle profile form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize all profile data from the POST request
    // Using null coalescing operator (??) to prevent undefined index warnings
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $birth_date = trim($_POST['birth_date'] ?? '');
    $mobile_number = trim($_POST['mobile_number'] ?? '');
    $standard = trim($_POST['standard'] ?? '');
    $school_name = trim($_POST['school_name'] ?? '');
    $school_board = trim($_POST['school_board'] ?? '');
    $school_address = trim($_POST['school_address'] ?? '');
    $school_city = trim($_POST['school_city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');

    // Store current POST data in session in case of an error to re-populate the form
    $_SESSION['profile_data_error'] = $_POST;

    // Basic server-side validation for required profile fields
    // This provides a fallback if client-side HTML 'required' attribute is bypassed.
    if (empty($first_name) || empty($last_name) || empty($birth_date) || empty($mobile_number) ||
        empty($standard) || empty($school_name) || empty($school_board) || empty($school_address) ||
        empty($school_city) || empty($address) || empty($city) || empty($district) ||
        empty($state) || empty($country) || empty($pincode)) {
        $_SESSION['error_message'] = "Please fill in all required profile fields.";
        header("Location: profile.php"); // Redirect back to profile page to show error
        exit();
    }

    // Prepare an INSERT statement for the 'profiles' table
    // Using prepared statements prevents SQL injection vulnerabilities.
    $stmt = $conn->prepare("INSERT INTO profiles (user_id, first_name, middle_name, last_name, birth_date, mobile_number, standard, school_name, school_board, school_address, school_city, address, city, district, state, country, pincode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        // Log database preparation errors for debugging purposes
        error_log("Failed to prepare profile insertion statement: " . $conn->error);
        $_SESSION['error_message'] = "An internal error occurred during profile creation. Please try again.";
        header("Location: profile.php");
        exit();
    }

    // Bind parameters to the prepared statement
    // 'i' for integer (user_db_id), 's' for string (all other fields)
    $stmt->bind_param("issssssssssssssss", $user_db_id, $first_name, $middle_name, $last_name, $birth_date, $mobile_number, $standard, $school_name, $school_board, $school_address, $school_city, $address, $city, $district, $state, $country, $pincode);

    // Execute the prepared statement
    if ($stmt->execute()) {
        // Profile created successfully

        // --- ADJUSTMENT START ---
        // Set the 'user_id' session variable which dashboard.php expects for authentication.
        $_SESSION['user_id'] = $user_db_id; // This is the key change to align with dashboard.php

        // You might also want to set $_SESSION['user_email'] or $_SESSION['display_username']
        // if your header.php or config.php relies on them directly.
        // For example:
        // $_SESSION['user_email'] = $user_email;
        // $_SESSION['display_username'] = $user_id_string; // Assuming this is the display name

        // Unset the temporary signup-related session variables
        unset($_SESSION['new_user_db_id']);
        unset($_SESSION['new_user_id_string']);
        unset($_SESSION['new_user_email']);
        unset($_SESSION['profile_data_error']); // Clear pre-fill data as submission was successful
        // --- ADJUSTMENT END ---

        // Redirect to the app dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Handle SQL errors during insertion
        error_log("Error inserting user profile: " . $stmt->error);
        $_SESSION['error_message'] = "Profile creation failed. Please try again.";
        header("Location: profile.php"); // Redirect back to profile page with error
        exit();
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection if it's open and valid (important to do this after all database operations)
if ($conn instanceof mysqli && !$conn->connect_error) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complete Your Profile - Easy Study</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet"/>
  <style>
    /* General body styling */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #1e3c72, #2a5298); /* Deep blue gradient */
      color: white;
      padding: 20px;
      min-height: 100vh; /* Full viewport height */
      display: flex;
      flex-direction: column;
      align-items: center; /* Center horizontally */
      justify-content: center; /* Center vertically */
    }

    /* Heading and paragraph styles */
    h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      text-align: center;
    }
    p {
        text-align: center;
        margin-bottom: 20px;
        font-size: 1.1rem;
    }

    /* Form container styling */
    form {
      max-width: 600px; /* Max width for readability */
      width: 100%; /* Responsive width */
      background: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
      padding: 30px;
      border-radius: 10px; /* Rounded corners */
      box-shadow: 0 5px 15px rgba(0,0,0,0.3); /* Subtle shadow */
      display: flex;
      flex-direction: column;
      gap: 15px; /* Spacing between form elements */
    }

    /* Label styling */
    label {
      display: block; /* Each label on its own line */
      margin-bottom: 5px;
      font-size: 0.95rem;
      font-weight: bold;
    }

    /* Input and select element styling */
    input[type="text"],
    input[type="date"],
    input[type="tel"],
    input[type="email"],
    select,
    textarea {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid rgba(255, 255, 255, 0.6); /* Semi-transparent border */
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.1); /* Semi-transparent background */
      color: white; /* White text */
      font-size: 1rem;
    }

    /* Placeholder text color */
    input::placeholder, textarea::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }

    /* Focus state for inputs */
    input:focus, select:focus, textarea:focus {
      border-color: #ffffff; /* Solid white border on focus */
      background: rgba(255, 255, 255, 0.2); /* Slightly more opaque background on focus */
      outline: none; /* Remove default outline */
    }

    /* Button styling */
    button {
      padding: 12px 20px;
      background: rgba(255, 255, 255, 0.2); /* Semi-transparent button background */
      color: white;
      border: 2px solid white;
      border-radius: 25px; /* Pill-shaped button */
      font-weight: bold;
      transition: 0.3s ease; /* Smooth transition for hover effects */
      cursor: pointer;
      margin-top: 10px;
    }

    /* Button hover state */
    button:hover {
      background: white;
      color: #1e3c72; /* Dark blue text on white hover */
    }

    /* Error message styling */
    .error-message {
        color: #ffcccc; /* Light red for dark background */
        background-color: rgba(255, 0, 0, 0.3); /* Semi-transparent red background */
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
        animation: slideIn 0.5s ease-out; /* Simple animation for appearance */
    }

    /* Keyframe animation for error message */
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<h1>Complete Your Profile for Easy Study</h1>
<!-- Display user_id_string for a personal welcome message -->
<p>Welcome, <?php echo htmlspecialchars($user_id_string); ?>! Please provide the remaining details.</p>

<?php if ($error_message): ?>
    <!-- Display error message if present -->
    <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
<?php endif; ?>

<form action="profile.php" method="post">
  <!-- Pre-fill fields using data from session or previous POST if an error occurred -->
  <label>First name:</label>
  <input name="first_name" required type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['first_name'] ?? ''); ?>">

  <label>Middle name:</label>
  <input name="middle_name" type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['middle_name'] ?? ''); ?>">

  <label>Last name:</label>
  <input name="last_name" required type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['last_name'] ?? ''); ?>">

  <label>Birth date:</label>
  <input name="birth_date" required type="date" value="<?php echo htmlspecialchars($profile_data_to_prefill['birth_date'] ?? ''); ?>">

  <label>Mobile number:</label>
  <!-- pattern for 10 digits -->
  <input name="mobile_number" required pattern="[0-9]{10}" type="tel" value="<?php echo htmlspecialchars($profile_data_to_prefill['mobile_number'] ?? ''); ?>">

  <label>Email:</label>
  <!-- Email is from the 'users' table, display it for reference, but don't allow editing or re-submission -->
  <input name="email_display" type="email" value="<?php echo htmlspecialchars($user_email); ?>" disabled>

  <label>Studying in which standard:</label>
  <input name="standard" required type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['standard'] ?? ''); ?>">

  <label>School name:</label>
  <input name="school_name" required type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['school_name'] ?? ''); ?>">

  <label>School board:</label>
  <select name="school_board" required>
    <option value="" disabled <?php echo empty($profile_data_to_prefill['school_board']) ? 'selected' : ''; ?>>Select your school board</option>
    <option value="CBSE" <?php echo ($profile_data_to_prefill['school_board'] ?? '') == 'CBSE' ? 'selected' : ''; ?>>CBSE</option>
    <option value="ICSE" <?php echo ($profile_data_to_prefill['school_board'] ?? '') == 'ICSE' ? 'selected' : ''; ?>>ICSE</option>
    <option value="Gujarat Board" <?php echo ($profile_data_to_prefill['school_board'] ?? '') == 'Gujarat Board' ? 'selected' : ''; ?>>Gujarat Board</option>
    <option value="Maharashtra Board" <?php echo ($profile_data_to_prefill['school_board'] ?? '') == 'Maharashtra Board' ? 'selected' : ''; ?>>Maharashtra Board</option>
    <option value="Other" <?php echo ($profile_data_to_prefill['school_board'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
  </select>

  <label>School address:</label>
  <textarea name="school_address" rows="4" required><?php echo htmlspecialchars($profile_data_to_prefill['school_address'] ?? ''); ?></textarea>

  <label>School city:</label>
  <input name="school_city" required type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['school_city'] ?? ''); ?>">

  <label>Address:</label>
  <textarea name="address" rows="4" required><?php echo htmlspecialchars($profile_data_to_prefill['address'] ?? ''); ?></textarea>

  <label>City:</label>
  <input name="city" required type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['city'] ?? ''); ?>">

  <label>District:</label>
  <input name="district" required type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['district'] ?? ''); ?>">

  <label>State:</label>
  <input name="state" required type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['state'] ?? ''); ?>">

  <label>Country:</label>
  <input name="country" required type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['country'] ?? ''); ?>">

  <label>Pincode:</label>
  <!-- pattern for 6 digits -->
  <input name="pincode" required pattern="[0-9]{6}" type="text" value="<?php echo htmlspecialchars($profile_data_to_prefill['pincode'] ?? ''); ?>">

  <br>
  <button type="submit">Complete Profile</button>
</form>

</body>
</html>
