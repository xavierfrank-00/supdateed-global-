<?php
include 'apiMain.php';
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Session Timeout Logic (Auto Logout)
if (isset($_SESSION["LAST_ACTIVITY"])) {
    $inactive_time = time() - $_SESSION["LAST_ACTIVITY"];
    if ($inactive_time > $_SESSION["EXPIRE_TIME"]) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=true");
        exit();
    }
}

// Update LAST_ACTIVITY time on user activity
$_SESSION["LAST_ACTIVITY"] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Session</title>
    <script>
        var logoutTime = <?php echo $_SESSION["EXPIRE_TIME"] * 1000; ?>; // Convert seconds to milliseconds
        var warningTime = logoutTime - 5000; // Show popup 5 seconds before timeout
        var countdownTime = 5; // Countdown starts from 5 seconds

        setTimeout(function () {
            document.getElementById("sessionPopup").style.display = "block";

            var countdownElement = document.getElementById("countdown");
            var countdownInterval = setInterval(function () {
                countdownElement.innerHTML = countdownTime;
                countdownTime--;

                if (countdownTime < 0) {
                    clearInterval(countdownInterval);
                    window.location.href = "login.php"; // Auto logout when countdown reaches 0
                }
            }, 1000);
        }, warningTime);

        // Auto Logout when session expires
        setTimeout(function () {
            window.location.href = "login.php";
        }, logoutTime);

        // Continue Session
        function continueSession() {
            document.getElementById("sessionPopup").style.display = "none";
            fetch("extend_session.php"); // Call PHP to extend session
            countdownTime = 5; // Reset countdown
        }
    </script>
    <style>
        /* Popup Styling */
        #sessionPopup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background: white;
            border: 2px solid black;
            box-shadow: 0px 0px 10px gray;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>
    <a href="login.php">Logout</a>

    <!-- Popup -->
    <div id="sessionPopup">
        <p>Your session will expire in <span id="countdown">15</span> seconds!</p>
        <button onclick="window.location.href='login.php'">Logout</button>
        <button onclick="continueSession()">Continue</button>
    </div>
</body>
</html>
