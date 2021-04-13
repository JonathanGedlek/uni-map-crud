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
//Get params from URL
$buildingName = str_replace($strReplace, '', $_GET["buildingName"]);
$markerLat = str_replace($strReplace, '', $_GET["markerLat"]);
$markerLng = str_replace($strReplace, '', $_GET["markerLng"]);
$newStoreCount = str_replace($strReplace, '', $_GET["newStoreCount"]);
//make empty array for stores
$storeArr = [];

for ($i = 0; $i <= $newStoreCount; $i++) {
    if (isset($_GET["newStore" . $i])) {
        $currentStore = str_replace($strReplace, '', $_GET["newStore" . $i]);
        if ($currentStore != null) {
            if ($i != $newStoreCount) {
                $currentStore = $currentStore . ";";
            }
            array_push($storeArr, $currentStore);
        }
    }
}
//turns room codes into a string
$storeString = implode("", $storeArr);
//make sure ; sign is not at the end of the string
if (subStr($storeString, -1) === ";") {
    $storeString = subStr($storeString, 0, -1);
};
//prepare statement
$stmtFoodCreate = $conn->prepare("INSERT INTO `foodmarkers`
(`lat`, `lng`, `text`, `location`) 
VALUES (:markerLat,:markerLng,:storeString,:buildingName)");
$stmtFoodCreate->bindValue("markerLat", "$markerLat");
$stmtFoodCreate->bindValue("markerLng", "$markerLng");
$stmtFoodCreate->bindValue("storeString", "$storeString");
$stmtFoodCreate->bindValue("buildingName", "$buildingName");

$stmtFoodCreate->execute();

$conn = null;

header("Location: foodMarkers.php");
