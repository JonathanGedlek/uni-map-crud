<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
require_once "../config.php";


$id = $_GET["id"];

$stmtdisabledDelete = $conn->prepare("DELETE FROM `foodmarkers` WHERE `id`= $id");

//execute
$stmtdisabledDelete->execute();
$conn = null;

header("Location:foodMarkers.php");
?>