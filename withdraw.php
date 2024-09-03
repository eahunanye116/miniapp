<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Crypto Withdrawal Request</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap");

        body {
            background-color: #2C2F33;
            color: #ffffff;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            background-color: #23272A;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.5);
        }

        .form-label {
            color: #B9BBBE;
        }

        .tooltip-inner {
            background-color: #7289DA;
        }

        .btn-primary {
            background-color: #7289DA;
            border-color: #7289DA;
        }

        .btn-primary:hover {
            background-color: #5A6DBF;
            border-color: #5A6DBF;
        }

        .progress {
            height: 20px;
            border-radius: 5px;
        }

        .progress-bar {
            background-color: #7289DA;
        }

        .input-group-text {
            background-color: #7289DA;
            color: #fff;
            border: none;
        }

        h4 {
            font-family: "Roboto Condensed", sans-serif;
            text-align: center;
            padding-top: 1em;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center">Request Withdrawal</h2>

        <?php
        // Start the session
        session_start();

        // Include database connection file
        include 'dbconn.php'; // Make sure to update the path if necessary

        // Check if the user is logged in (i.e., session ID is set)
        if (!isset($_SESSION['user_id'])) {
            echo '<div class="alert alert-danger" role="alert">Error: You must be logged in to submit a withdrawal request.</div>';
            exit();
        }

        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $amount = $_POST['amount'];
            $crypto = $_POST['crypto'];
            $wallet = $_POST['wallet'];
            $status = "Pending"; // Default status for a new request
            $userId = $_SESSION['user_id']; // Get the user ID from the session

            // Retrieve the user's dashboard balance from the database
            $sql = "SELECT dashboardbalance FROM users WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->bind_result($dashboardbalance);
            $stmt->fetch();
            $stmt->close();

            // Check if the withdrawal amount is equal to the dashboard balance
            if ($amount <= $dashboardbalance) {
                // Prepare and bind the withdrawal request
                $stmt = $conn->prepare("INSERT INTO requestwithdrawal (amount, crypto, wallet_address, status, user_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("dsssi", $amount, $crypto, $wallet, $status, $userId);

                // Execute the query
                if ($stmt->execute()) {
                    echo '<div class="alert alert-success" role="alert">Withdrawal request submitted successfully!</div>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Error: Could not submit withdrawal request.</div>';
                }

                // Close the statement
                $stmt->close();
            } else {
                echo '<div class="alert alert-danger" role="alert">Error: The withdrawal amount must be equal or less than your dashboard balance.</div>';
            }

            // Close the connection
            $conn->close();
        }
        ?>


        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Withdrawal Amount -->
            <div class="mb-4">
                <label for="amount" class="form-label">Withdrawal Amount (USD)</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" min="0" step="0.01" required>
                    <button type="button" class="btn btn-outline-light" id="tooltipAmount" data-bs-toggle="tooltip" data-bs-placement="right" title="Enter the amount you wish to withdraw in USD.">
                        ?
                    </button>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar" id="amountProgress" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <!-- Cryptocurrency Type -->
            <div class="mb-4">
                <label for="crypto" class="form-label">Select Cryptocurrency</label>
                <select class="form-select" id="crypto" name="crypto" required>
                    <option value="USDT">Tether (USDT)</option>
                    <!-- Add other cryptocurrency options as needed -->
                </select>
            </div>
            <!-- Wallet Address -->
            <div class="mb-4">
                <label for="wallet" class="form-label">Wallet Address</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="wallet" name="wallet" placeholder="Enter wallet address" required>
                    <button type="button" class="btn btn-outline-light" id="tooltipWallet" data-bs-toggle="tooltip" data-bs-placement="right" title="Paste your crypto wallet address here. Ensure it's correct!">
                        ?
                    </button>
                </div>
            </div>
            <!-- Submit Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Submit Withdrawal Request</button>
            </div>
        </form>
        <h4>The maximum daily withdrawal limit is $10,000</h4>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tooltip initialization
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        // Amount Progress Bar
        const amountInput = document.getElementById('amount');
        const amountProgress = document.getElementById('amountProgress');

        amountInput.addEventListener('input', function() {
            let value = parseFloat(amountInput.value) || 0;
            let max = 10000; // Assume max is $10,000 for example
            let percent = (value / max) * 100;

            amountProgress.style.width = percent + '%';
            amountProgress.setAttribute('aria-valuenow', percent);
        });
    </script>
</body>

</html>