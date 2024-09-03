<?php
// Include the database configuration file
include 'dbconn.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullname = $_POST['fullname'];
    $investorid = $_POST['investorID'];
    $password = $_POST['password'];
    $dashboardbalance = $_POST['balance'];
    $equity = $_POST['equity'];
    $cumulative_profit = $_POST['cumulative_profit'];

    // Prepare and bind the SQL statement for creating a user
    $stmt = $conn->prepare("INSERT INTO users (fullname, investorid, password, dashboardbalance, equity, cumulative_profit) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdd", $fullname, $investorid, $password, $dashboardbalance, $equity, $cumulative_profit);

    // Execute the statement
    if ($stmt->execute()) {
        // Get the ID of the newly created user
        $user_id = $stmt->insert_id;

        // Prepare and bind the SQL statement to insert a row in the requestwithdrawal table
        $stmt_withdrawal = $conn->prepare("INSERT INTO requestwithdrawal (id, amount, crypto, wallet_address, status) VALUES (?, NULL, NULL, NULL, NULL)");
        $stmt_withdrawal->bind_param("i", $user_id);

        // Execute the statement
        if ($stmt_withdrawal->execute()) {
            // Redirect to the investors page
            header("Location: investors.php");
        } else {
            echo "Error inserting into requestwithdrawal: " . $stmt_withdrawal->error;
        }

        // Close the withdrawal statement
        $stmt_withdrawal->close();
    } else {
        echo "Error creating user: " . $stmt->error;
    }

    // Close the user statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investor Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
         <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .signup-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .signup-header {
            margin-bottom: 30px;
            text-align: center;
        }
        .form-control {
            margin-bottom: 20px;
        }
        .btn-custom {
            background-color: #343a40;
            color: #fff;
            border-radius: 25px;
        }
        .btn-custom:hover {
            background-color: #212529;
        }
    </style>
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-header">
            <h2>Investor Signup</h2>
            <p>Create your Investors account by filling in the details below</p>
        </div>
        <form method="POST" action="createuser.php">
            <div class="mb-3">
                <label for="fullname" class="form-label">Set Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter full name" required>
            </div>
            <div class="mb-3">
                <label for="investorID" class="form-label">Set Investor ID</label>
                <input type="text" class="form-control" id="investorID" name="investorID" placeholder="Create Investor ID" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Set Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Create password" required>
            </div>
            <div class="mb-3">
                <label for="balance" class="form-label">Set Dashboard Balance</label>
                <input type="number" class="form-control" id="balance" name="balance" placeholder="Enter a starting balance" required>
            </div>
            <div class="mb-3">
                <label for="equity" class="form-label">Set Equity</label>
                <input type="number" class="form-control" id="equity" name="equity" placeholder="Enter equity" required>
            </div>
            <div class="mb-3">
                <label for="cumulative_profit" class="form-label">Set Cumulative Profit</label>
                <input type="number" class="form-control" id="cumulative_profit" name="cumulative_profit" placeholder="Enter cumulative profit" required>
            </div>
            <button type="submit" class="btn btn-custom w-100">Create Investor</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
