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


//Get params from URL
$buildingName = $_GET["buildingName"];
$b_id = $_GET["buildingLetterId"];
$markerLat = $_GET["markerLat"];
$markerLng = $_GET["markerLng"];
$description = $_GET["description"];
$departments = $_GET["departments"];
$facilities = $_GET["facilities"];
$coordinates = $_GET["polygons"];
$imageLoc = $_GET["imageLoc"];


$stmtBuildingUpdate = $conn->prepare("INSERT INTO `buildings`(`b_id`, `name`, `lat`, `lng`, `description`, `departments`, `facilities`, `background`, `coordinates`) 
VALUES (:b_id,:buildingName,:lat,:lng,:buildingDesc,:departments,:facilities,:background,:coordinates)");

//binds to the prepared statement
$stmtBuildingUpdate->bindValue(":buildingName", "$buildingName");
$stmtBuildingUpdate->bindValue(":b_id", "$b_id");
$stmtBuildingUpdate->bindValue(":lat", "$markerLat");
$stmtBuildingUpdate->bindValue(":lng", "$markerLng");
$stmtBuildingUpdate->bindValue(":buildingDesc", "$description");
$stmtBuildingUpdate->bindValue(":departments", "$departments");
$stmtBuildingUpdate->bindValue(":facilities", "$facilities");
$stmtBuildingUpdate->bindValue(":background", "$imageLoc");
$stmtBuildingUpdate->bindValue(":coordinates", "$coordinates");
$stmtBuildingUpdate->execute();

// Perform a query, check for error
//close connection
$conn = null;
// //redirect user to edit page
// header("Location: buildings.php");
