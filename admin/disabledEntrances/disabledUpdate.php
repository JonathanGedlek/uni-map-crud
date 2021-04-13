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

//string of characters to escape from user input
$strReplace = array(';', '[', ']', '$', '{', '}');
//Get Params from URL
$markerLat = str_replace($strReplace, '', $_GET["markerLat"]);
$markerLng = str_replace($strReplace, '', $_GET["markerLng"]);
$floors = str_replace($strReplace, '', $_GET["floors"]);
$entranceId = str_replace($strReplace, '', $_GET["entranceId"]);

//prepare statement
$stmtEntranceUpdate = $conn->prepare("UPDATE `disabledentrances` SET `lat`= :markerLat,`lng`= :markerLng,`type`= :floors WHERE disabledentrances.id = :entranceId");
//bind values to prepared statement
$stmtEntranceUpdate->bindValue(":markerLat", "$markerLat");
$stmtEntranceUpdate->bindValue(":markerLng", "$markerLng");
$stmtEntranceUpdate->bindValue(":floors", "$floors");
$stmtEntranceUpdate->bindValue(":entranceId", "$entranceId");

$stmtEntranceUpdate->execute();
//close connection after execute
$conn = null;
//redirect user back to edit page
header("Location: disabledEdit.php?id=" . $entranceId);
?>