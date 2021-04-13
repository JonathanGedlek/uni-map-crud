<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/uni-map-crud/styles/style.css">
</head>

<body class="admin-body">
    <nav class="fixed-top d-flex justify-content-end">
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
    </nav>
    <h1 class="my-5 text-center">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Here is your admin Controls.</h1>
    <section class="d-flex justify-content-center">
        <div class="admin-container d-flex flex-column">
            <div class="admin-btn-div">
                <a class="admin-btn" href="buildings/buildings.php">
                    <div class="admin-btn-text text-center">Buildings</div>
                </a>
            </div>
            <div class="admin-btn-div">
                <a class="admin-btn" href="disabledEntrances/disabledEntrances.php">
                    <div class="admin-btn-text text-center">Disabled Entrances</div>
                </a>
            </div>
            <div class="admin-btn-div">
                <a class="admin-btn" href="foodMarkers/foodMarkers.php">
                    <div class="admin-btn-text text-center">Food Markers</div>
                </a>
            </div>
            <div class="admin-btn-div">
                <a class="admin-btn" href="neutralToilets/neutralToilets.php">
                    <div class="admin-btn-text text-center">Neutral Toilets</div>
                </a>
            </div>
            <div class="admin-btn-div">
                <a class="admin-btn" href="accessibilityToilets/accessibilityToilets.php">
                    <div class="admin-btn-text text-center">Accessibility Toilets</div>
                </a>
            </div>
        </div>
    </section>
</body>

</html>