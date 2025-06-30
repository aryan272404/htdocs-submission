<?php
// signup.php - Handles user registration and redirects to profile creation

// Include the database connection file
require_once 'db.php';

// Start a session to store user information for redirection
session_start();

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // --- 1. Retrieve and Sanitize Inputs from the HTML form ---
    // These names ('email', 'userid', 'password') must match your signup.html exactly.
    $email = trim($_POST['email'] ?? '');
    $userid = trim($_POST['userid'] ?? ''); // Matches name="userid" from your HTML
    $password = $_POST['password'] ?? '';

    // --- 2. Basic Validation for Required Fields ---
    if (empty($email) || empty($userid) || empty($password)) {
        $_SESSION['error_message'] = "Please fill in all fields.";
        header("Location: signup.php"); // Redirect back to signup form with error
        exit;
    }

    // --- 3. Check for Existing User ID or Email in the 'users' table ---
    // Assuming your 'users' table has columns named 'userid' and 'email'.
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE userid = ? OR email = ?");
    if ($stmt_check === false) {
        error_log("Failed to prepare user check statement: " . $conn->error);
        $_SESSION['error_message'] = "An internal error occurred. Please try again later.";
        header("Location: signup.php");
        exit;
    }
    $stmt_check->bind_param("ss", $userid, $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // User ID or Email already exists, redirect back to signup page with error
        $_SESSION['error_message'] = "User ID or Email already exists! Please choose a different one.";
        header("Location: signup.php");
        exit;
    }
    $stmt_check->close();

    // --- 4. Hash Password and Insert New User into the 'users' table ---
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Assuming your 'users' table has columns named 'email', 'userid', and 'password'.
    $stmt_insert = $conn->prepare("INSERT INTO users (email, userid, password) VALUES (?, ?, ?)");
    if ($stmt_insert === false) {
        error_log("Failed to prepare user insertion statement: " . $conn->error);
        $_SESSION['error_message'] = "An internal error occurred during registration. Please try again.";
        header("Location: signup.php");
        exit;
    }
    $stmt_insert->bind_param("sss", $email, $userid, $hashed_password);

    if ($stmt_insert->execute()) {
        // Get the ID of the newly inserted user from the 'users' table
        $new_user_db_id = $conn->insert_id;

        // Store necessary information in session for the profile creation page (profile.php)
        $_SESSION['new_user_db_id'] = $new_user_db_id;
        $_SESSION['new_user_id_string'] = $userid; // The user ID string itself from the form
        $_SESSION['new_user_email'] = $email;

        // Redirect to the profile creation page upon successful registration
        header("Location: profile.php");
        exit;
    } else {
        // Handle other SQL errors during insertion
        error_log("Error inserting new user: " . $stmt_insert->error);
        $_SESSION['error_message'] = "Registration failed. Please try again.";
        header("Location: signup.php");
        exit;
    }

    $stmt_insert->close();
}

// Close the database connection when script finishes (or if not posted)
if ($conn instanceof mysqli && !$conn->connect_error) {
    $conn->close();
}

// If someone directly accesses signup.php without POST data, or if an error occurred and
// was redirected back, the signup.html content will be displayed.
// This part is crucial for displaying error messages on signup.html itself.
// This means you should NOT have a separate signup.html file unless you want
// to redirect to it explicitly on initial load.
// If you want to integrate the HTML directly into signup.php to show errors,
// you can do that by placing the HTML block below this PHP code block.
// However, given your use of 'header("Location: signup.html")' for errors,
// it implies 'signup.html' is a separate file.

// If there's an error message from a previous attempt, prepare it for display on signup.html
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the message after retrieving
}

// Below is the HTML structure that your signup.html would ideally contain,
// modified to display the error message.
// If your signup.html is a *separate static file*, you'll need JavaScript in signup.html
// to retrieve and display the error message from a cookie or localStorage,
// or use PHP within signup.html to echo the session variable.
// Given the current flow with header redirects, the separate signup.html is assumed.
// For demonstration, I'm providing a minimal HTML structure here.
// In a real setup, your actual signup.html should be served by the web server,
// and the PHP script handles the POST request.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Signup | Easy Study App</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet"/>
  <style>
    /* Your existing CSS for signup.html */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body, html {
      height: 100%;
      overflow: hidden;
    }

    #particles-js {
      position: absolute;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      z-index: -1;
    }

    .container {
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: white;
      padding: 0 20px;
      animation: fadeIn 1s ease-in-out;
    }

    h1 {
      font-size: 2.5rem;
      margin-bottom: 30px;
      text-align: center;
    }

    form {
      width: 100%;
      max-width: 400px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    label {
      font-size: 1rem;
      margin-bottom: 5px;
      text-align: left;
    }

    input {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid rgba(255, 255, 255, 0.6);
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.1);
      color: white;
      font-size: 1rem;
    }

    input::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }

    input:focus {
      border-color: #ffffff;
      background: rgba(255, 255, 255, 0.2);
      outline: none;
    }

    .buttons {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn {
      flex: 1;
      padding: 12px 0;
      background: rgba(255, 255, 255, 0.1);
      color: white;
      border: 2px solid white;
      border-radius: 25px;
      text-align: center;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s ease;
      cursor: pointer;
    }

    .btn:hover {
      background: white;
      color: #1e3c72;
    }

    .error-message {
        color: #ffcccc; /* Light red for dark background */
        background-color: rgba(255, 0, 0, 0.3);
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
        animation: slideIn 0.5s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <div id="particles-js"></div>

  <div class="container">
    <h1>Create Your Account</h1>
    <?php if ($error_message): ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <form action="signup.php" method="POST">
      <div>
        <label for="email">Enter Email</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" required />
      </div>
      <div>
        <label for="userid">Create Unique User ID</label>
        <input type="text" id="userid" name="userid" placeholder="your_user_id" required />
      </div>
      <div>
        <label for="password">Create Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required />
      </div>
      <div class="buttons">
        <button type="submit" class="btn">Sign Up</button>
        <a href="login.html" class="btn">Already have an account?</a>
      </div>
    </form>
  </div>

  <!-- Particle.js Library -->
  <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>

  <!-- Particle.js Config -->
  <script>
    particlesJS("particles-js", {
      "particles": {
        "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
        "color": { "value": "#ffffff" },
        "shape": { "type": "circle", "stroke": { "width": 0, "color": "#000000" } },
        "opacity": { "value": 0.5 },
        "size": { "value": 3, "random": true },
        "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 },
        "move": { "enable": true, "speed": 4 }
      },
      "interactivity": {
        "events": {
          "onhover": { "enable": true, "mode": "grab" },
          "onclick": { "enable": true, "mode": "push" }
        },
        "modes": {
          "grab": { "distance": 200, "line_linked": { "opacity": 1 } },
          "push": { "particles_nb": 4 }
        }
      },
      "retina_detect": true
    });
  </script>
</body>
</html>
