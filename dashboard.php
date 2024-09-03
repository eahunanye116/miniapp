<?php
session_start();
require 'dbconn.php'; // Adjust to your file path

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve the user's details from the database
$sql = "SELECT fullname, dashboardbalance, equity, cumulative_profit FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname, $dashboardbalance, $equity, $cumulative_profit);
$stmt->fetch();
$stmt->close();

// Retrieve the user's withdrawal requests from the database
$sqlWithdrawals = "SELECT amount, wallet_address, status FROM requestwithdrawal WHERE user_id=?";
$stmtWithdrawals = $conn->prepare($sqlWithdrawals);
$stmtWithdrawals->bind_param("i", $user_id);
$stmtWithdrawals->execute();
$stmtWithdrawals->bind_result($amount, $wallet_address, $status,);

// Store the withdrawal requests in an array
$withdrawal_requests = [];
while ($stmtWithdrawals->fetch()) {
    $withdrawal_requests[] = [
        'amount' => $amount,
        'wallet_address' => $wallet_address,
        'status' => $status,
    ];
}
$stmtWithdrawals->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investor Dashboard</title>
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

        .welcome-text {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .card-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .card {
            background-color: #23272A;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-icon {
            font-size: 2rem;
        }

        .card-text {
            text-align: right;
        }

        .card-text h4 {
            margin: 0;
            font-size: 1.25rem;
        }

        .card-text p {
            margin: 0;
            font-size: 0.875rem;
            color: white;
            text-align: center;
        }

        h4 {
            color: white;
            text-align: center;
        }

        .tradingview-widget-container-general {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 30em;
            margin-bottom: 1.7em;
            margin-top: 2em;
        }

        .tradingview-widget-container-general-1 {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 35em;
            margin-bottom: 1em;
        }

        h4 {
            font-family: "Roboto Condensed", sans-serif;
        }

        .copyright {
            text-align: center;
            font-family: "Roboto Condensed", sans-serif;
            color: #B9BBBE;
        }

        .nav-btn {
            border: none;
            margin-top: 1em;
            font-family: "Roboto Condensed", sans-serif;
            background-color: #7289DA;
            color: white;
            width: 8em;
        }

        .dashboard-nav {
            display: flex;
            gap: 10px;
        }

        .withdrawal-table {
            margin-top: 30px;
        }

        .withdrawal-table th,
        .withdrawal-table td {
            text-align: center;
            color: white;
        }

        .withdrawal-table thead {
            background-color: #7289DA;
        }

        .withdrawal-table tbody tr {
            background-color: #23272A;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">
                    AlgoTrade
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end dashboard-nav" id="navbarNav">
                    <div> <a class="nav-link active" aria-current="page" href="withdraw.php"><button class="nav-btn">Withdraw</button></a></div>

                    <div> <a class="nav-link active" aria-current="page" href="logout.php"><button class="nav-btn">Logout</button></a></div>
                </div>
            </div>
        </nav>
    </header>
    <div class="container mt-5">
        <div class="welcome-text">
            Welcome, <?php echo htmlspecialchars($fullname); ?>!
        </div>
        <div class="card-container">
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-arrow-down" style="color: #FFA500;"></i>
                </div>
                <div class="card-text">
                    <h4>$<?php echo number_format($dashboardbalance, 2); ?></h4>
                    <p>Deposit</p>
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-coins" style="color: #32CD32;"></i>
                </div>
                <div class="card-text">
                    <h4>$<?php echo number_format($equity, 2); ?></h4>
                    <p>Equity</p>
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-dollar-sign" style="color: #6A5ACD;"></i>
                </div>
                <div class="card-text">
                    <h4>$<?php echo number_format($cumulative_profit, 2); ?></h4>
                    <p>Cumulative Profit</p>
                </div>
            </div>
        </div>

        <!-- Withdrawal Requests Section -->
        <div class="withdrawal-table">
            <h4>Your Withdrawal Requests</h4>
            <?php if (count($withdrawal_requests) > 0): ?>
                <table class="table table-bordered table-dark">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Wallet Address</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($withdrawal_requests as $request): ?>
                            <tr>
                                <td>$<?php echo number_format($request['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($request['wallet_address']); ?></td>
                                <td><?php echo htmlspecialchars($request['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No withdrawal requests found.</p>
            <?php endif; ?>
        </div>
    </div>

     <!-- TradingView Widget BEGIN -->
     <div class="tradingview-widget-container-general">
        <h4>TECHNICAL CHART</h4>
        <!-- TradingView Widget BEGIN -->
        <div class="tradingview-widget-container" style="height:100%;width:100%">
            <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                {
                    "autosize": true,
                    "symbol": "BITSTAMP:BTCUSD",
                    "interval": "D",
                    "timezone": "Etc/UTC",
                    "theme": "dark",
                    "style": "1",
                    "locale": "en",
                    "allow_symbol_change": true,
                    "calendar": false,
                    "support_host": "https://www.tradingview.com"
                }
            </script>
        </div>
        <!-- TradingView Widget END -->
    </div>
    <!-- TradingView Widget END -->


    <!-- TradingView Widget BEGIN -->
    <div class="tradingview-widget-container-general-1">
        <h4>NEWS ANALYSIS</h4>
        <!-- TradingView Widget BEGIN -->
        <div class="tradingview-widget-container">
            <div class="tradingview-widget-container__widget"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-timeline.js" async>
                {
                    "feedMode": "all_symbols",
                    "isTransparent": false,
                    "displayMode": "regular",
                    "width": "100%",
                    "height": "100%",
                    "colorTheme": "dark",
                    "locale": "en"
                }
            </script>
        </div>
        <!-- TradingView Widget END -->
    </div>


    <p class="copyright">Copyright © 2024 Algobot, inc</p>


    <!-- Bootstrap JS and Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/js/all.min.js"></script>
</body>

</html>