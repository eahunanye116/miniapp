<?php
// Include the database configuration file
include 'dbconn.php';

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$successMessage = '';
$errorMessage = '';

// Handle Update Withdrawal Request
if (isset($_POST['update'])) {
  try {
    // Get form data
    $withdrawalID = intval($_POST['id']);
    $amount = floatval($_POST['amount']);
    $crypto = $_POST['crypto'];
    $walletAddress = $_POST['wallet_address'];
    $status = $_POST['status'];

    // Prepare the SQL statement to update the withdrawal request
    $stmt = $conn->prepare("UPDATE requestwithdrawal SET amount = ?, crypto = ?, wallet_address = ?, status = ? WHERE id = ?");
    $stmt->bind_param("dsssi", $amount, $crypto, $walletAddress, $status, $withdrawalID);

    // Execute the statement
    if ($stmt->execute()) {
      $successMessage = "Withdrawal request updated successfully.";
    } else {
      $errorMessage = "Error updating withdrawal request.";
    }
    $stmt->close();
  } catch (Exception $e) {
    $errorMessage = "Error: " . $e->getMessage();
  }
}

if (isset($_GET['delete'])) {
  try {
    // Get withdrawal ID from query string
    $withdrawalID = intval($_GET['delete']);

    // Prepare the SQL statement to delete the withdrawal request
    $stmt = $conn->prepare("DELETE FROM requestwithdrawal WHERE id = ?");
    $stmt->bind_param("i", $withdrawalID);

    // Execute the statement
    if ($stmt->execute()) {
      $successMessage = "Withdrawal request deleted successfully.";
    } else {
      echo '<div class="alert alert-danger" role="alert">Error: Could not delete withdrawal request.</div>';
    }
    $stmt->close();
  } catch (Exception $e) {
    echo '<div class="alert alert-danger" role="alert">Error: ' . $e->getMessage() . '</div>';
  }
}

// Fetch all withdrawal requests with user details
$result = $conn->query("
  SELECT rw.id, rw.amount, rw.crypto, rw.wallet_address, rw.status, u.fullname AS user_name
  FROM requestwithdrawal rw
  JOIN users u ON rw.user_id = u.id
");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Withdrawal</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="investors.css"> <!-- Optional: Custom CSS -->
</head>

<body>

  <div class="container mt-5">
    <h2 class="mb-4 invsthed">Withdrawal Requests</h2>

    <!-- Display success or error messages -->
    <?php if (isset($successMessage) && $successMessage): ?>
      <div class="alert alert-primary" role="alert">
        <?php echo htmlspecialchars($successMessage); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($errorMessage) && $errorMessage): ?>
      <div class="alert alert-primary" role="alert">
        <?php echo htmlspecialchars($errorMessage); ?>
      </div>
    <?php endif; ?>

    <div class="table-responsive investor-div">
      <table class="table table-striped table-bordered">
        <thead class="tablefonts">
          <tr>
            <th>ID</th>
            <th>Investor Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody class="tablefonts">
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td>
                  <a href="withdrawalrequest.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Update</a>
                  <a href="withdrawalrequest.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this withdrawal request?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center">No withdrawal requests found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if (isset($_GET['id'])): ?>
      <?php
      // Fetch withdrawal details for the update form
      $withdrawalID = intval($_GET['id']);
      $stmt = $conn->prepare("SELECT amount, crypto, wallet_address, status FROM requestwithdrawal WHERE id = ?");
      $stmt->bind_param("i", $withdrawalID);
      $stmt->execute();
      $stmt->bind_result($amount, $crypto, $walletAddress, $status);
      $stmt->fetch();
      $stmt->close();
      ?>
      <h2 class="mt-5 header-one">Update Withdrawal Request</h2>
      <form method="POST" action="withdrawalrequest.php" class="investment-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($withdrawalID); ?>">

        <div class="form-group">
          <label for="amount" class="form-label">Amount</label>
          <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?php echo htmlspecialchars($amount); ?>" required>
        </div>

        <div class="form-group">
          <label for="crypto" class="form-label">Crypto</label>
          <input type="text" class="form-control" id="crypto" name="crypto" value="<?php echo htmlspecialchars($crypto); ?>" required>
        </div>

        <div class="form-group">
          <label for="wallet_address" class="form-label">Wallet Address</label>
          <input type="text" class="form-control" id="wallet_address" name="wallet_address" value="<?php echo htmlspecialchars($walletAddress); ?>" required>
        </div>

        <div class="form-group">
          <label for="status" class="form-label">Status</label>
          <input type="text" class="form-control" id="status" name="status" value="<?php echo htmlspecialchars($status); ?>" required>
        </div>

        <button type="submit" name="update" class="btn btn-success">Update</button>
      </form>

    <?php endif; ?>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Close the connection
$conn->close();
?>
