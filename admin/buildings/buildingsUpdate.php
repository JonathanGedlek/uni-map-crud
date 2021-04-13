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
$id = $_GET["buildingId"];
$b_id = $_GET["buildingLetterId"];
$markerLat = $_GET["markerLat"];
$markerLng = $_GET["markerLng"];
$description = $_GET["description"];
$departments = $_GET["departments"];
$facilities = $_GET["facilities"];
$coordinates = $_GET["polygons"];
$imageLoc = $_GET["imageLoc"];


$stmtBuildingUpdate = $conn->prepare("UPDATE `buildings` SET `b_id`=:b_id,`name`=:buildingName,`lat`=:lat,`lng`=:lng,`description`=:buildingDesc, `departments`=:departments, `facilities`=:facilities,`background`=:background,`coordinates`=:coordinates WHERE `id` = $id");

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

//close connection
$conn = null;
//redirect user to edit page
header("Location: buildingsEdit.php?id=" . $id);
?>
