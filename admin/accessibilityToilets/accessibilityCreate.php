<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin/login.php");
    exit;
}
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

    <title>Accessibility Toilet Creation</title>
</head>

<body class="admin-body">
    <div class="container-fluid">
        <nav class="row">
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-sm-center text-md-center text-lg-left text-xl-left logged-in">Logged in as <?php echo " " . htmlspecialchars($_SESSION["username"]); ?></div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-center admin-title ">Create Accessibility Toilet</div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-flex justify-content-xl-end justify-content-lg-end m-auto justify-content-md-center justify-content-sm-center justify-content-xs-center log-btns">
                <button type="button" class="btn btn-success"><a href="accessibilityToilets.php">Back</a></button>
                <button type="button" class="btn btn-warning"><a href="admin/reset-password.php">Reset Password</a></button>
                <button type="button" class="btn btn-danger"><a href="admin/logout.php">Sign Out</a></button>
            </div>
        </nav>
    </div>
    <div class="mapEditDiv" id="mapDiv">
        <p>Something Went Wrong!</p>
    </div>
    <main class="main">
        <form id="neutralForm" action="accessibilityCreateSQL.php">
            <label for="buildingName">Building Name:</label><br>
            <input class="margin-bottom-5" type="text" id="buildingName" name="buildingName" required><br>
            <div class="margin-bottom-5"><b>Click to add marker. Drag and drop to change coordinates:</b></div>
            <label for="markerLat">Current Latitude Coordinates:</label><br>
            <input type="text" id="markerLat" name="markerLat" required><br>
            <label for="markerLng">Current Longitude Coordinates:</label><br>
            <input type="text" id="markerLng" name="markerLng" required>
            <input type="text" id="newRoomCount" name="newRoomCount" hidden>

            <div class="margin-top-5" id="rooms">
                <p><b>Rooms</b></p>
            </div>
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
            let markerSelected = false;

            //create the map and polygon
            const map = createMap();
            // create the map and return it to save it as a variable
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

            function createMarker(latLng) {
                var singleMarker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    draggable: true,
                    icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });
                return singleMarker;
            }

            function markerCoords(markerobject) {
                const markerLat = document.getElementById("markerLat");
                const markerLng = document.getElementById("markerLng");
                google.maps.event.addListener(markerobject, 'dragend', function(evt) {
                    markerLat.setAttribute("value", evt.latLng.lat());
                    markerLng.setAttribute("value", evt.latLng.lng());
                });
            }

            function addMarker() {
                google.maps.event.addDomListener(map, "click", function(event) {
                    if (markerSelected === false) {
                        const markerLat = document.getElementById("markerLat");
                        const markerLng = document.getElementById("markerLng");
                        let latLng = [{
                            "lat": event.latLng.lat(),
                            "lng": event.latLng.lng()
                        }];
                        var marker = createMarker(latLng[0]);
                        markerLat.setAttribute("value", event.latLng.lat());
                        markerLng.setAttribute("value", event.latLng.lng());
                        markerCoords(marker);
                        markerSelected = true;
                        return marker;
                    }
                });
            }

            function accessibilityToiletHTMLSetup() {
                const roomDiv = document.getElementById("rooms");
                const roomBtnDiv = document.getElementById("roomBtnDiv");
                const buildingName = document.getElementById("buildingName");
                const newRoomCounter = document.getElementById("newRoomCount");
                const addRoomBtn = document.createElement("button");
                let newRoomCount = null;
                newRoomCounter.setAttribute("value", newRoomCount);
                addRoomBtn.setAttribute("class", "btn btn-info editBtn");
                addRoomBtn.setAttribute("type", "button");
                addRoomBtn.setAttribute("id", "addExtraRoom");
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

            function main() {
                addMarker();
                accessibilityToiletHTMLSetup();
            }
            main();

        }
        google.maps.event.addDomListener(window, "load", initialise);
    })();
</script>

</html>