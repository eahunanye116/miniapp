<?php
// Include the database configuration file
include 'dbconn.php';

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $adminID = $_POST['investorID'];
    $password = $_POST['password'];

    // Prepare the SQL statement to check the admin credentials
    $stmt = $conn->prepare("SELECT * FROM adminlogin WHERE adminid = ? AND passwords = ?");
    $stmt->bind_param("ss", $adminID, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login successful, redirect to the admin dashboard or another page
        header("Location: investors.php");
        exit();
    } else {
        // Login failed, set an error message
        $loginError = "Invalid Admin ID or Password.";
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
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
      <h2>ADMIN LOGIN</h2>
      <p>Please enter your admin credentials to access your Terminal</p>
    </div>
    <?php if ($loginError): ?>
      <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($loginError); ?>
      </div>
    <?php endif; ?>
    <form method="POST" action="adminlogin.php">
      <div class="mb-3">
        <label for="investorID" class="form-label">Admin ID</label>
        <input type="text" class="form-control" name="investorID" id="investorID" placeholder="Enter your ID" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn btn-custom w-100">Administrator Login</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
