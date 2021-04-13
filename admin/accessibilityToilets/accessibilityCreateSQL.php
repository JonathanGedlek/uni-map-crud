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
$markerLat = $_GET["markerLat"];
$markerLng = $_GET["markerLng"];
$newRoomCount = $_GET["newRoomCount"];

//make empty array for rooms
$roomArr = [];

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

$stmtAccessibilityToiletCreate = $conn->prepare("INSERT INTO `accessibilitytoilets` (`lat`, `lng`, `room_name`, `building_name`) 
VALUES (:markerLat,:markerLng,:roomString,:buildingName)");

$stmtAccessibilityToiletCreate->bindValue(":markerLat", "$markerLat");
$stmtAccessibilityToiletCreate->bindValue(":markerLng", "$markerLng");
$stmtAccessibilityToiletCreate->bindValue(":roomString", "$roomString");
$stmtAccessibilityToiletCreate->bindValue(":buildingName", "$buildingName");
$stmtAccessibilityToiletCreate->execute();


phpinfo();

$conn = null;

// header("Location: accessibilityToilets.php");

?>