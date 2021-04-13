<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin/login.php");
    exit;
}

//database access
require_once "../config.php";

$id = $_GET["id"];

//prepare statements
$stmtNeutralDelete = $conn->prepare("DELETE FROM `neutraltoilets` WHERE `id`=$id");
//executes
$stmtNeutralDelete->execute();
//close connection
$conn = null;
//redirect user to edit page
header("Location: neutralToilets.php");
?>