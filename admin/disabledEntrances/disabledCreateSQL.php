<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
require_once "../config.php";

$markerLat = $_GET["markerLat"];
$markerLng = $_GET["markerLng"];
$floors = $_GET["floors"];

$stmtDisabledCreate = $conn->prepare("INSERT INTO `disabledentrances`(`lat`, `lng`, `type`) 
VALUES (:markerLat,:markerLng,:floors)");

$stmtDisabledCreate->bindValue(":markerLat", $markerLat);
$stmtDisabledCreate->bindValue(":markerLng", $markerLng);
$stmtDisabledCreate->bindValue(":floors", $floors);
$stmtDisabledCreate->execute();

$conn = null;

header("Location: disabledEntrances.php");


?>