<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin/login.php");
    exit;
}
require_once "../config.php";



//Get URL

$id = $_GET["id"];

//prepared statement
$stmtBuildingUpdate = $conn->prepare("DELETE FROM `buildings` WHERE id = $id");

//execute
$stmtBuildingUpdate->execute();
//close connection
$conn = null;
//redirect user to edit page
header("Location: buildings.php");