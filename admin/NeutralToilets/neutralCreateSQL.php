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

//string of characters to escape from user input
$strReplace = array(';', '[', ']', '$', '{', '}');

//Get params from URL
$buildingName = str_replace($strReplace, '', $_GET["buildingName"]);
$markerLat = str_replace($strReplace, '', $_GET["markerLat"]);
$markerLng = str_replace($strReplace, '', $_GET["markerLng"]);
$newRoomCount = str_replace($strReplace, '', $_GET["newRoomCount"]);

//make empty array for rooms
$roomArr = [];
//push old and new rooms to arrays, ignoring empty strings
for ($i = 0; $i <= $newRoomCount; $i++) {
    if (isset($_GET["newRoom" . $i])) {
        $currentRoom = str_replace($strReplace, '', $_GET["newRoom" . $i]);
        if ($currentRoom != null) {
            array_push($roomArr, $currentRoom);
        }
    }
}


//turns room codes into a string
$roomString = implode(", ", $roomArr);
//prepare statement
$stmtNeutralUpdate = $conn->prepare("INSERT INTO `neutraltoilets`
(`building_name`, `lat`, `lng`, `room_code`) 
VALUES 
(:buildingName,:markerLat,:markerLng,:roomString)");
//binds to the prepared statement
$stmtNeutralUpdate->bindValue(":buildingName","$buildingName");
$stmtNeutralUpdate->bindValue(":markerLat","$markerLat");
$stmtNeutralUpdate->bindValue(":markerLng","$markerLng");
$stmtNeutralUpdate->bindValue(":roomString","$roomString");
//executes the update query
$stmtNeutralUpdate->execute();
//close connection
$conn = null;
//redirect user to edit page
header("Location: neutralToilets.php");
?>