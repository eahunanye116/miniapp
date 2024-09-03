<?php
// Include the database configuration file
include 'dbconn.php';

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$successMessage = '';
$errorMessage = '';

// Handle Update Request
if (isset($_POST['update'])) {
    try {
        // Get form data
        $userID = intval($_POST['id']);
        $fullname = $_POST['fullname'];
        $investorID = $_POST['investorID'];
        $password = $_POST['password'];
        $dashboardBalance = $_POST['dashboardBalance'];
        $equity = $_POST['equity'];
        $cumulativeProfit = $_POST['cumulative_profit'];

        // Prepare the SQL statement to update the user
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, investorid = ?, password = ?, dashboardbalance = ?, equity = ?, cumulative_profit = ? WHERE id = ?");
        $stmt->bind_param("ssssddi", $fullname, $investorID, $password, $dashboardBalance, $equity, $cumulativeProfit, $userID);

        // Execute the statement
        if ($stmt->execute()) {
            $successMessage = "User updated successfully.";
        } else {
            $errorMessage = "Error updating user.";
        }
        $stmt->close();
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    try {
        // Get user ID from query string
        $userID = intval($_GET['delete']);

        // Prepare the SQL statement to delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userID);

        // Execute the statement
        if ($stmt->execute()) {
            $successMessage = "User deleted successfully.";
        } else {
            $errorMessage = "Error deleting user.";
        }
        $stmt->close();
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Fetch all users
$result = $conn->query("SELECT id, fullname, investorid, password, dashboardbalance, equity, cumulative_profit FROM users");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investors Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="investors.css"> <!-- Optional: Custom CSS -->
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4 invsthed">Investors List</h2>

        <!-- Display success or error messages -->
        <?php if ($successMessage): ?>
            <div class="alert alert-primary" role="alert">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-primary" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive investor-div">
            <table class="table table-striped table-bordered">
                <thead class="tablefonts">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>InvestorID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="tablefonts">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($row['investorid']); ?></td>
                                <td>
                                    <a href="investors.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Update</a>
                                    <a href="investors.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($_GET['id'])): ?>
            <?php
            // Fetch user details for the update form
            $userID = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT fullname, investorid, password, dashboardbalance, equity, cumulative_profit FROM users WHERE id = ?");
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $stmt->bind_result($fullname, $investorID, $password, $dashboardBalance, $equity, $cumulativeProfit);
            $stmt->fetch();
            $stmt->close();
            ?>
            <h2 class="mt-5 header-one">Update User Details</h2>
            <form method="POST" action="investors.php" class="investment-form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($userID); ?>">

                <div class="form-group">
                    <label for="fullname" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
                </div>

                <div class="form-group">
                    <label for="investorID" class="form-label">Investor ID</label>
                    <input type="text" class="form-control" id="investorID" name="investorID" value="<?php echo htmlspecialchars($investorID); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>
                </div>

                <div class="form-group">
                    <label for="dashboardBalance" class="form-label">Dashboard Balance</label>
                    <input type="number" class="form-control" id="dashboardBalance" name="dashboardBalance" value="<?php echo htmlspecialchars($dashboardBalance); ?>" required>
                </div>

                <div class="form-group">
                    <label for="equity" class="form-label">Equity</label>
                    <input type="number" class="form-control" id="equity" name="equity" value="<?php echo htmlspecialchars($equity); ?>" required>
                </div>

                <div class="form-group">
                    <label for="cumulative_profit" class="form-label">Cumulative Profit</label>
                    <input type="number" class="form-control" id="cumulative_profit" name="cumulative_profit" value="<?php echo htmlspecialchars($cumulativeProfit); ?>" required>
                </div>

                <button type="submit" name="update" class="btn btn-success">Update</button>
            </form>

        <?php endif; ?>
    </div>
    <a href="createuser.php"><button class="create-investor-btn">CREATE INVESTOR</button></a>
    <a href="withdrawalrequest.php"><button class="create-investor-btn">WITHDRAWAL REQUESTS</button></a>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Close the connection
$conn->close();
?>