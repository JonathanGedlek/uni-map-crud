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
$stmtNeutral = $conn->prepare("SELECT * FROM neutraltoilets WHERE neutraltoilets.id = $id");
//executes
$stmtNeutral->execute();
//close connection
$conn = null;
//fetch data
$neutralToiletsPHP = $stmtNeutral->fetch();
//encode for use in javascript
$neutralToiletsJS = json_encode($neutralToiletsPHP);
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

    <title>Neutral Edit</title>
</head>

<body class="admin-body">
    <div class="container-fluid">
        <nav class="row">
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-sm-center text-md-center text-lg-left text-xl-left logged-in">Logged in as <?php echo " " . htmlspecialchars($_SESSION["username"]); ?></div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-center admin-title ">Edit Toilet</div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-flex justify-content-xl-end justify-content-lg-end m-auto justify-content-md-center justify-content-sm-center justify-content-xs-center log-btns">
                <button type="button" class="btn btn-success"><a href="neutralToilets.php">Back</a></button>
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
        <form id="neutralForm" action="neutralUpdate.php">
            <label for="buildingName">Change Building Name:</label><br>
            <input class="margin-bottom-5" type="text" id="buildingName" name="buildingName"><br>
            <div class="margin-bottom-5"><b>Drag and drop marker to change coordinates:</b></div>
            <label for="markerLat">Current Latitude Coordinates:</label><br>
            <input type="text" id="markerLat" name="markerLat" readonly><br>
            <label for="markerLng">Current Longitude Coordinates:</label><br>
            <input type="text" id="markerLng" name="markerLng" readonly><br>
            <input type="text" id="roomCount" name="roomCount" hidden><br>
            <input type="text" id="newRoomCount" name="newRoomCount" hidden><br>
            <input type="text" id="buildingId" name="buildingId" hidden><br>

            <div class="margin-top-5" id="rooms">
                <p><b>Rooms</b></p>
            </div>
            <button class="btn btn-info" type="button"><a href="neutralCreate.php">Create New Marker</a></button>
            <div id="roomBtnDiv">

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
                location.replace("neutralToilets.php?iderror=true");
            }
            var buildingId = document.getElementById("buildingId").setAttribute("value", id);
            <?php
            echo "const neutralToilets = " . $neutralToiletsJS . ";\n";
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


            function createNeutralToilet(map, neutralToilets) {
                //create a marker, grab the coordinates from the list of group markers, stick it on the map and apply the custom icon
                const coords = new google.maps.LatLng(neutralToilets.lat, neutralToilets.lng);
                if (neutralToilets.id == id) {
                    formInit(neutralToilets);
                }
                var neutralToilet = new google.maps.Marker({
                    position: new google.maps.LatLng(parseFloat(neutralToilets.lat), parseFloat(neutralToilets.lng)),
                    map: map,
                    icon: iconBase + 'neutralbathroom' + png,
                    draggable: true
                });
                markerCoords(neutralToilet, map);

                map.setCenter(coords);
                neutralToiletHTMLSetup(neutralToilets, neutralToilet);
            }

            //initialisation and setting of all dom elements that are generated from or require information from the database
            function neutralToiletHTMLSetup(neutralToilets, neutralToilet) {
                const roomDiv = document.getElementById("rooms");
                const roomBtnDiv = document.getElementById("roomBtnDiv");
                const markerLat = document.getElementById("markerLat");
                const markerLng = document.getElementById("markerLng");
                const buildingName = document.getElementById("buildingName");
                const roomCount = document.getElementById("roomCount");
                const newRoomCounter = document.getElementById("newRoomCount");
                const addRoomBtn = document.createElement("button");
                let newRoomCount = null;
                newRoomCounter.setAttribute("value", newRoomCount);
                markerLat.setAttribute("value", neutralToilets.lat);
                markerLng.setAttribute("value", neutralToilets.lng);
                buildingName.setAttribute("value", neutralToilets.building_name);
                //reset button to move marker to original position if moved
                $("#resetLatLng").click(function() {
                    var latlng = new google.maps.LatLng(neutralToilets.lat, neutralToilets.lng);
                    neutralToilet.setPosition(latlng);
                    markerLat.setAttribute("value", neutralToilets.lat);
                    markerLng.setAttribute("value", neutralToilets.lng);
                });
                const roomCodes = neutralToilets.room_code.split(', ');
                for (i = 0; i < roomCodes.length; i++) {
                    const input = document.createElement("input");
                    const br = document.createElement("br");
                    const deleteRoom = document.createElement("button");
                    deleteRoom.setAttribute("class", "btn btn-info");
                    deleteRoom.setAttribute("type", "button");
                    deleteRoom.setAttribute("id", "dltRoomBtn_" + i);
                    deleteRoom.innerHTML = "Delete Room";
                    roomCount.setAttribute("value", i);
                    input.setAttribute("type", "text");
                    input.setAttribute("class", "input");
                    input.setAttribute("id", "room_" + i);
                    input.setAttribute("name", "room_" + i);
                    input.setAttribute("value", roomCodes[i]);
                    roomDiv.appendChild(input);
                    roomDiv.appendChild(deleteRoom);
                    roomDiv.appendChild(br);
                    deleteRoom.addEventListener("click", function() {
                        input.remove();
                        deleteRoom.remove();
                    });
                }
                addRoomBtn.setAttribute("class", "btn btn-info editBtn");
                addRoomBtn.setAttribute("type", "button");
                addRoomBtn.setAttribute("id", "addExtraRoom");
                addRoomBtn.innerHTML = "Add Room";
                addRoomBtn.innerHTML = "Add Room";
                roomBtnDiv.appendChild(addRoomBtn);
                $("#addExtraRoom").click(function() {
                    if (newRoomCount == null) {
                        newRoomCount = 0;
                        newRoomCounter.setAttribute("value", newRoomCount);
                    } else {
                        newRoomCount++;
                        newRoomCounter.setAttribute("value", newRoomCount);
                    }
                    const input = document.createElement("input");
                    const br = document.createElement("br");
                    const dltNewRoomBtn = document.createElement("button");
                    dltNewRoomBtn.setAttribute("class", "btn btn-info");
                    dltNewRoomBtn.setAttribute("type", "button");
                    dltNewRoomBtn.setAttribute("id", "dltNewRoomBtn_" + newRoomCount);
                    dltNewRoomBtn.innerHTML = "Delete Room";
                    input.setAttribute("type", "text");
                    input.setAttribute("class", "input");
                    input.setAttribute("id", "newRoom_" + newRoomCount);
                    input.setAttribute("name", "newRoom" + newRoomCount);
                    roomDiv.appendChild(input);
                    roomDiv.appendChild(dltNewRoomBtn);
                    roomDiv.appendChild(br);
                    dltNewRoomBtn.addEventListener("click", function() {
                        input.remove();
                        dltNewRoomBtn.remove();
                    })
                });
            }

            //Gets marker coordinates and sets it to DOM
            function markerCoords(markerobject, map) {
                const markerLat = document.getElementById("markerLat");
                const markerLng = document.getElementById("markerLng");
                google.maps.event.addListener(markerobject, 'dragend', function(evt) {
                    markerLat.setAttribute("value", evt.latLng.lat());
                    markerLng.setAttribute("value", evt.latLng.lng());

                });
            }

            function formInit(neutralToilets) {
                $("#buildingName").attr("value", neutralToilets.building_name);
            }


            function main(map, neutralToilets) {
                createNeutralToilet(map, neutralToilets)
            }
            main(map, neutralToilets);


        }
        google.maps.event.addDomListener(window, "load", initialise);
    })();
</script>

</html>