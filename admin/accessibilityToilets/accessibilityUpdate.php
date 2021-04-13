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
$buildingId = $_GET["buildingId"];
$markerLat = $_GET["markerLat"];
$markerLng = $_GET["markerLng"];
$roomCount = $_GET["roomCount"];
$newRoomCount = $_GET["newRoomCount"];



//make empty array for rooms
$roomArr = [];
//push old and new rooms to arrays, ignoring empty strings
for ($i = 0; $i <= $roomCount; $i++) {
    if (isset($_GET["room_" . $i])) {
        $currentRoom = $_GET["room_" . $i];
        if ($currentRoom != null) {
            array_push($roomArr, $currentRoom);
        }
    }
}
for ($i = 0; $i <= $newRoomCount; $i++) {
    if (isset($_GET["newRoom" . $i])) {
        $currentRoom = $_GET["newRoom" . $i];
        if ($currentRoom != null) {
            array_push($roomArr, $currentRoom);
        }
    }
}
//turns room codes into a string
$roomString = implode(", ", $roomArr);

//prepare statement
$stmtAccessibilityToiletUpdate = $conn->prepare("UPDATE `accessibilitytoilets` SET `lat`= :markerLat,`lng`= :markerLng,`room_name`=:roomString,`building_name`=:buildingName WHERE `id` = $buildingId");
//binds to the prepared statement
$stmtAccessibilityToiletUpdate->bindValue(":buildingName","$buildingName");
$stmtAccessibilityToiletUpdate->bindValue(":markerLat","$markerLat");
$stmtAccessibilityToiletUpdate->bindValue(":markerLng","$markerLng");
$stmtAccessibilityToiletUpdate->bindValue(":roomString","$roomString");
//executes the update query
$stmtAccessibilityToiletUpdate->execute();
// Perform a query, check for error
//close connection
$conn = null;
// //redirect user to edit page
header("Location: accessibilityEdit.php?id=".$buildingId);
?>
