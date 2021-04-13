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
$stmtDisabledEntrances = $conn->prepare("SELECT * FROM disabledentrances WHERE disabledentrances.id = $id");
//executes
$stmtDisabledEntrances->execute();
//close connection
$conn = null;
//fetch data
$disabledEntrancesPHP = $stmtDisabledEntrances->fetch();
//encode for use in javascript
$disabledEntrancesJS = json_encode($disabledEntrancesPHP);
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

    <title>Disabled Entrance Edit</title>
</head>

<body class="admin-body">
    <div class="container-fluid">
        <nav class="row">
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-sm-center text-md-center text-lg-left text-xl-left logged-in">Logged in as <?php echo " " . htmlspecialchars($_SESSION["username"]); ?></div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-center admin-title ">Edit Disabled Entrance</div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-flex justify-content-xl-end justify-content-lg-end m-auto justify-content-md-center justify-content-sm-center justify-content-xs-center log-btns">
                <button type="button" class="btn btn-success"><a href="disabledEntrances.php">Back</a></button>
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
        <form id="entranceForm" action="disabledUpdate.php">

            <div class="margin-bottom-5"><b>Drag and drop marker to change coordinates:</b></div>
            <label for="markerLat">Current Latitude Coordinates:</label><br>
            <input type="text" id="markerLat" name="markerLat" readonly><br>
            <label for="markerLng">Current Longitude Coordinates:</label><br>
            <input type="text" id="markerLng" name="markerLng" readonly><br>
            <input type="text" id="entranceId" name="entranceId" hidden><br>
            <label for="floors">Enter floor number that the entrance is on<br></label>
            <div><b>WARNING!! Make sure an image is uploaded with the floor number showing, with the correct naming conventions, i.e. disabledEntrance0.png or marker will not show on map</b></div>
            <input type="text" id="floors" name="floors"><br>
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
                location.replace("disabledEntrances.php?iderror=true");
            }
            var entranceId = document.getElementById("entranceId").setAttribute("value", id);
            <?php
            echo "const disabledEntrances = " . $disabledEntrancesJS . ";\n";
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

            function createDisabledMarker(map, disabledEntrances) {
                //create a marker, grab the coordinates from the list of group markers, stick it on the map and apply the custom icon
                const coords = new google.maps.LatLng(disabledEntrances.lat, disabledEntrances.lng);
                var disabledEntrance = new google.maps.Marker({
                    position: new google.maps.LatLng(parseFloat(disabledEntrances.lat), parseFloat(disabledEntrances.lng)),
                    map: map,
                    icon: iconBase + "disabledentrance" + disabledEntrances.type + png,
                    draggable: true
                });
                markerCoords(disabledEntrance, map);

                map.setCenter(coords);
                disabledMarkerHTMLSetup(disabledEntrances, disabledEntrance);
            }


            function disabledMarkerHTMLSetup(disabledEntrances, disabledEntrance) {
                const markerLat = document.getElementById("markerLat");
                const markerLng = document.getElementById("markerLng");
                const entranceId = document.getElementById("entranceId");
                const  floors = document.getElementById("floors");
                markerLat.setAttribute("value", disabledEntrances.lat);
                markerLng.setAttribute("value", disabledEntrances.lng);
                floors.setAttribute("value", disabledEntrances.type);
                //reset button to move marker to original position if moved
                $("#resetLatLng").click(function() {
                    var latlng = new google.maps.LatLng(disabledEntrances.lat, disabledEntrances.lng);
                    disabledEntrance.setPosition(latlng);
                    markerLat.setAttribute("value", disabledEntrances.lat);
                    markerLng.setAttribute("value", disabledEntrances.lng);
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


            function main(map, disabledEntrances) {
                createDisabledMarker(map, disabledEntrances);
            }
            main(map, disabledEntrances);


        }
        google.maps.event.addDomListener(window, "load", initialise);
    })();
</script>

</html>