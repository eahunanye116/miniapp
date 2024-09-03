<?php
session_start();
// Include the database configuration file
include 'dbconn.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $investorID = $_POST['investorID'];
    $password = $_POST['password'];

    // Prepare the SQL statement to select the user
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE investorid = ?");
    $stmt->bind_param("s", $investorID);

    // Execute the statement
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $stored_password);
        $stmt->fetch();

        // Verify the password
        if ($password === $stored_password) {  // If using plain text passwords
            // Set session variable
            $_SESSION['user_id'] = $id;

            // Redirect to the dashboard or success page
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that ID.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Investor Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Custom styles */
    body {
      background-color: #f8f9fa;
      font-family: Arial, sans-serif;
    }

    .login-container {
      max-width: 400px;
      margin: 50px auto;
      padding: 20px;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    .login-header {
      margin-bottom: 20px;
      text-align: center;
    }

    .form-control {
      margin-bottom: 15px;
    }

    .btn-custom {
      background-color: #343a40;
      color: #fff;
      border-radius: 25px;
    }

    .btn-custom:hover {
      background-color: #212529;
    }

    .form-footer {
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-header">
      <h2>INVESTOR LOGIN</h2>
      <p>Please enter your credentials to access your account</p>
    </div>
    <form method="POST" action="index.php">
      <div class="mb-3">
        <label for="investorID" class="form-label">Investor ID</label>
        <input type="text" class="form-control" name="investorID" id="investorID" placeholder="Enter your ID" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn btn-custom w-100">Login</button>
    </form>

    <div class="form-footer">
      <p>If you do not have your login credentials, please contact our administrators via Telegram to obtain access.</p>
    </div>

    <a href="adminlogin.php"><button type="submit" class="btn btn-custom w-100">Administrator Login</button></a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>