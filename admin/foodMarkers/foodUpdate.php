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
$foodId = str_replace($strReplace, '', $_GET["foodId"]);
$markerLat = str_replace($strReplace, '', $_GET["markerLat"]);
$markerLng = str_replace($strReplace, '', $_GET["markerLng"]);
$storeCount = str_replace($strReplace, '', $_GET["storeCount"]);
$newStoreCount = str_replace($strReplace, '', $_GET["newStoreCount"]);
//make empty array for stores
$storeArr = [];
//push old and new stores to arrays, ignoring empty strings
for ($i = 0; $i <= $storeCount; $i++) {
    if (isset($_GET["store_" . $i])) {
        $currentStore = str_replace($strReplace, '', $_GET["store_" . $i]);
        $j = $i+1;
        if ($currentStore != null) {
            if ($storeCount != $i || $newStoreCount != null) {
                $currentStore = $currentStore . ";";
            }
            array_push($storeArr, $currentStore);
        }
    }
}
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
if(subStr($storeString, -1)=== ";"){
$storeString = subStr($storeString, 0, -1);
};
//prepare statement
$stmtFoodUpdate = $conn->prepare("UPDATE `foodmarkers` SET `lat` = :markerLat,`lng` = :markerLng,`text` = :storeText,`location`= :buildingName WHERE id = :foodId");
//bind values to statement
$stmtFoodUpdate->bindValue(":markerLat", "$markerLat");
$stmtFoodUpdate->bindValue(":markerLng", "$markerLng");
$stmtFoodUpdate->bindValue(":storeText", "$storeString");
$stmtFoodUpdate->bindValue(":buildingName", "$buildingName");
$stmtFoodUpdate->bindValue(":foodId", "$foodId");
//execute the update query
$stmtFoodUpdate->execute();
//close connection
$conn = null;
//redirect user to edit page
header("Location: foodEdit.php?id=" . $foodId);
