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
$stmtFood = $conn->prepare("SELECT * FROM foodmarkers WHERE foodmarkers.id = $id");
//executes
$stmtFood->execute();
//close connection
$conn = null;
//fetch data
$foodMarkersPHP = $stmtFood->fetch();
//encode for use in javascript
$foodMarkersJS = json_encode($foodMarkersPHP);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- style sheet -->
    <link rel="stylesheet" type="text/css" href="../../styles/style.css" />

    <!-- bootstrap css -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Food Edit</title>
</head>

<body class="admin-body">
    <div class="container-fluid">
        <nav class="row">
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-sm-center text-md-center text-lg-left text-xl-left logged-in">Logged in as <?php echo " " . htmlspecialchars($_SESSION["username"]); ?></div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-center admin-title ">Edit Food Marker</div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-flex justify-content-xl-end justify-content-lg-end m-auto justify-content-md-center justify-content-sm-center justify-content-xs-center log-btns">
                <button type="button" class="btn btn-success"><a href="foodMarkers.php">Back</a></button>
                <button type="button" class="btn btn-warning"><a href="admin/reset-password.php">Reset Password</a></button>
                <button type="button" class="btn btn-danger"><a href="admin/logout.php">Sign Out</a></button>
            </div>
        </nav>
    </div>
    <div class="mapEditDiv" id="mapDiv">
        <p>Something Went Wrong!</p>
    </div>
    <main class="main">
        <button class="btn btn-info" type="button" id="resetLatLng">Reset Marker Position</button>
        <form id="foodForm" action="foodUpdate.php">
            <label for="buildingName">Change Building Name:</label><br>
            <input class="margin-bottom-5" type="text" id="buildingName" name="buildingName"><br>
            <div class="margin-bottom-5"><b>Drag and drop marker to change coordinates:</b></div>
            <label for="markerLat">Current Latitude Coordinates:</label><br>
            <input type="text" id="markerLat" name="markerLat" readonly><br>
            <label for="markerLng">Current Longitude Coordinates:</label><br>
            <input type="text" id="markerLng" name="markerLng" readonly><br>
            <input type="text" id="storeCount" name="storeCount" hidden><br>
            <input type="text" id="newStoreCount" name="newStoreCount" hidden><br>
            <input type="text" id="foodId" name="foodId" hidden><br>

            <div class="margin-top-5" id="stores">
                <p><b>Stores</b></p>
            </div>
            <div id="storeBtnDiv">

            </div>
            <div>
                <b>WARNING!! Any information you change will be saved on form submission, make sure it's correct.</b>
                <br><b>If you leave a field blank, it will be treated as a deleted item</b>
            </div>
            <button class="btn btn-success submit-button" type="submit">Submit to Database</button>
        </form>
    </main>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5Nf-Bw1vZ4ZtH0N-tbk7t6lDEvXVdALc"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
    (function() {
        function initialise() {
            var iconBase = '../../Images/';
            var png = '.png';
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get("id");
            const map = createMap();
            if (id === null) {
                location.replace("foodMarkers.php?iderror=true");
            }
            var foodId = document.getElementById("foodId").setAttribute("value", id);
            <?php
            echo "const foodMarkers = " . $foodMarkersJS . ";\n";
            ?>

            function createMap() {
                var mapOptions = {
                    center: new google.maps.LatLng(53.64303, -1.777420),
                    zoom: 16,
                    disableDefaultUI: false,
                    mapTypeControl: true,
                    gestureHandling: "greedy",
                    //map style JSON information
                    styles: [{
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "administrative",
                            "elementType": "geometry",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "administrative.land_parcel",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "administrative.land_parcel",
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "administrative.neighborhood",
                            "stylers": [{
                                    "visibility": "off"
                                },
                                {
                                    "lightness": "30"
                                }
                            ]
                        },
                        {
                            "featureType": "landscape.natural",
                            "elementType": "geometry.fill",
                            "stylers": [{
                                "color": "#f5f2f1"
                            }]
                        },
                        {
                            "featureType": "landscape.man_made",
                            "elementType": "geometry.fill",
                            "stylers": [{
                                "lightness": -10
                            }]
                        },
                        {
                            "featureType": "landscape",
                            "elementType": "geometry.fill",
                            "stylers": [{
                                "lightness": 30
                            }]
                        },
                        {
                            "featureType": "poi",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "geometry.fill",
                            "stylers": [{
                                "color": "#ffffff"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "geometry.stroke",
                            "stylers": [{
                                "color": "#cfcfcf"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels.icon",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels.text",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                    "color": "#002448"
                                },
                                {
                                    "weight": 0.5
                                }
                            ]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "geometry.fill",
                            "stylers": [{
                                "color": "#e7e6e4"
                            }]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "geometry.stroke",
                            "stylers": [{
                                "color": "#bcbcbc"
                            }]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "labels.text",
                            "stylers": [{
                                    "color": "#bcbcbc"
                                },
                                {
                                    "weight": 1
                                }
                            ]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#002448"
                            }]
                        },
                        {
                            "featureType": "road.local",
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "on"
                            }]
                        },
                        {
                            "featureType": "transit",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        }
                    ],
                }
                var map = new google.maps.Map(document.getElementById("mapDiv"), mapOptions);
                return map;
            }

            function createfoodMarker(map, foodMarkers) {
                //create a marker, grab the coordinates from the list of group markers, stick it on the map and apply the custom icon
                const coords = new google.maps.LatLng(foodMarkers.lat, foodMarkers.lng);
                if (foodMarkers.id == id) {
                    formInit(foodMarkers);
                }
                var foodMarker = new google.maps.Marker({
                    position: new google.maps.LatLng(parseFloat(foodMarkers.lat), parseFloat(foodMarkers.lng)),
                    map: map,
                    icon: iconBase + 'Food' + png,
                    draggable: true
                });
                markerCoords(foodMarker, map);

                map.setCenter(coords);
                foodMarkerHTMLSetup(foodMarkers, foodMarker);
            }


            function foodMarkerHTMLSetup(foodMarkers, foodMarker) {
                const storeDiv = document.getElementById("stores");
                const storeBtnDiv = document.getElementById("storeBtnDiv");
                const markerLat = document.getElementById("markerLat");
                const markerLng = document.getElementById("markerLng");
                const buildingName = document.getElementById("buildingName");
                const markerId = document.getElementById("markerId");
                const storeCount = document.getElementById("storeCount");
                const newStoreCounter = document.getElementById("newStoreCount");
                const addStoreBtn = document.createElement("button");
                let newStoreCount = null;
                markerLat.setAttribute("value", foodMarkers.lat);
                markerLng.setAttribute("value", foodMarkers.lng);
                buildingName.setAttribute("value", foodMarkers.location);
                //reset button to move marker to original position if moved
                $("#resetLatLng").click(function() {
                    var latlng = new google.maps.LatLng(foodMarkers.lat, foodMarkers.lng);
                    foodMarker.setPosition(latlng);
                    markerLat.setAttribute("value", foodMarkers.lat);
                    markerLng.setAttribute("value", foodMarkers.lng);
                });
                const stores = foodMarkers.text.split(';');
                for (i = 0; i < stores.length; i++){
                    stores[i].replace('<li>',' ');
                    stores[i].replace('</li>','');
                    const input = document.createElement("input");
                    const br = document.createElement("br");
                    const deleteStore = document.createElement("button");
                    deleteStore.setAttribute("class", "btn btn-info");
                    deleteStore.setAttribute("type", "button");
                    deleteStore.setAttribute("id", "dltStoreBtn_" + i);
                    deleteStore.innerHTML = "Delete Store";
                    storeCount.setAttribute("value", i);
                    input.setAttribute("type", "text");
                    input.setAttribute("class", "input");
                    input.setAttribute("id", "store_" + i);
                    input.setAttribute("name", "store_" + i);
                    input.setAttribute("value", stores[i]);
                    storeDiv.appendChild(input);
                    storeDiv.appendChild(deleteStore);
                    storeDiv.appendChild(br);
                    deleteStore.addEventListener("click", function() {
                        input.remove();
                        deleteStore.remove();
                    });
                }
                addStoreBtn.setAttribute("class", "btn btn-info editBtn");
                addStoreBtn.setAttribute("type", "button");
                addStoreBtn.setAttribute("id", "addExtraStore");
                addStoreBtn.innerHTML = "Add Store";
                addStoreBtn.innerHTML = "Add Room";
                storeBtnDiv.appendChild(addStoreBtn);
                $("#addExtraStore").click(function(){
                    if (newStoreCount == null) {
                        newStoreCount = 0;
                        newStoreCounter.setAttribute("value", newStoreCount);
                    } else {
                        newStoreCount++;
                        newStoreCounter.setAttribute("value", newStoreCount);
                    }
                    const input = document.createElement("input");
                    const br = document.createElement("br");
                    const dltNewStoreBtn = document.createElement("button");
                    dltNewStoreBtn.setAttribute("class", "btn btn-info");
                    dltNewStoreBtn.setAttribute("type", "button");
                    dltNewStoreBtn.setAttribute("id", "dltNewStoreBtn_" + newStoreCount);
                    dltNewStoreBtn.innerHTML = "Delete Store";
                    input.setAttribute("type", "text");
                    input.setAttribute("class", "input");
                    input.setAttribute("id", "newStore_"+ newStoreCount);
                    input.setAttribute("name", "newStore" + newStoreCount);
                    storeDiv.appendChild(input);
                    storeDiv.appendChild(dltNewStoreBtn);
                    storeDiv.appendChild(br);
                    dltNewStoreBtn.addEventListener("click", function(){
                        input.remove();
                        dltNewStoreBtn.remove();
                    })
                });

            }



            function markerCoords(markerobject, map) {
                const markerLat = document.getElementById("markerLat");
                const markerLng = document.getElementById("markerLng");
                google.maps.event.addListener(markerobject, 'dragend', function(evt) {
                    markerLat.setAttribute("value", evt.latLng.lat());
                    markerLng.setAttribute("value", evt.latLng.lng());

                });
            }

            function formInit(foodMarkers) {
                $("#buildingName").attr("value", foodMarkers.location);
            }

            function main(map, foodMarkers) {
                createfoodMarker(map, foodMarkers);
            }
            main(map, foodMarkers);


        }
        google.maps.event.addDomListener(window, "load", initialise);
    })();
</script>

</html>