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
$stmtBuildings = $conn->prepare("SELECT * FROM buildings WHERE buildings.id = $id");
//executes
$stmtBuildings->execute();
//close connection
$conn = null;
//fetch data
$buildingsPHP = $stmtBuildings->fetch();
//encode for use in javascript
$buildingsJS = json_encode($buildingsPHP);
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

    <title>Building Edit</title>
</head>

<body class="admin-body">
    <div class="container-fluid">
        <nav class="row">
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-sm-center text-md-center text-lg-left text-xl-left logged-in">Logged in as <?php echo " " . htmlspecialchars($_SESSION["username"]); ?></div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-center admin-title ">Edit Building</div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-flex justify-content-xl-end justify-content-lg-end m-auto justify-content-md-center justify-content-sm-center justify-content-xs-center log-btns">
                <button type="button" class="btn btn-success"><a href="buildings.php">Back</a></button>
                <button type="button" class="btn btn-warning"><a href="admin/reset-password.php">Reset Password</a></button>
                <button type="button" class="btn btn-danger"><a href="admin/logout.php">Sign Out</a></button>
            </div>
        </nav>
    </div>
    <b>Drag Markers to change building polygon, click map to add a new marker. <br>
        To rebuild the polygon hit Recreate Polygon button and build from scratch. <br>Blue Marker indicates the first marker <br>Green Marker indicates the last marker, these will be joined at the end<br>
        Yellow Marker indicates position of where info window will pop up</b>
    <div class="mapEditDiv" id="mapDiv">
        <p>Something Went Wrong!</p>
    </div>
    <main class="main">
        <button class="btn btn-info" type="button" id="resetLatLng">Reset Info Marker Position</button>
        <button class="btn btn-info" type="button" id="recreatePolygon">Recreate Polygon</button>
        <button class="btn btn-info" type="button" id="resetPolygon">Reset Changes to Polygon</button>
        <form id="neutralForm" action="buildingsUpdate.php">
            <label for="buildingName">Change Building Name:</label><br>
            <input class="margin-bottom-5" type="text" id="buildingName" name="buildingName"><br>
            <label for="buildingLetterId">Change Building Letter ID:</label><br>
            <input class="margin-bottom-5" type="text" id="buildingLetterId" name="buildingLetterId"><br>
            <div class="margin-bottom-5"><b>Drag and drop marker to change coordinates, this should be at the buildings centre, the info pane will open at this spot.</b></div>
            <label for="markerLat">Current Latitude Coordinates:</label><br>
            <input type="text" id="markerLat" name="markerLat" readonly><br>
            <label for="markerLng">Current Longitude Coordinates:</label><br>
            <input type="text" id="markerLng" name="markerLng" readonly><br>
            <label for="description">Change description of building (You can add in extra HTML here for styling)</label><br>
            <input type="text" id="description" name="description" class="descriptionBox"><br>
            <b>For security reasons, image upload cannot be used. If you want to add an image to the building, upload it manually to the image folder then enter the file name here, i.e. studentcentral.jpg</b><br>
            <b>please only upload jpg or png files, and think about file size before uploading the image.</b>
            <input type="text" id="imageLoc" name="imageLoc"><br>
            <label for="departments"><b>Add departments here, seperate each with a comma and a space</b></label><br>
            <input type="text" id="departments" name="departments" class="descriptionBox"><br>
            <label for="facilities"><b>Add facilities here, seperate each with a ; and a space</b></label><br>
            <input type="text" id="facilities" name="facilities" class="descriptionBox"><br>
            <input type="text" id="buildingId" name="buildingId" hidden><br>
            <input type="text" id="polygons" name="polygons" hidden><br>
            <div>
                <b>WARNING!! Any information you change will be saved on form submission, make sure it's correct.</b>
                <br><b>If you leave a field blank, it will be treated as a deleted item</b>
                <br><b>Bare in mind, data such as Building Letter ID and departments is gathered but are not currently displayed on the map</b>
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
            //get params and save id
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get("id");
            //if user has gone to page without an id, return to previous with error message display
            if (id === null) {
                location.replace("buildings.php?iderror=true");
            }
            var buildingId = document.getElementById("buildingId").setAttribute("value", id);

            //translate the PHP JSON object into javascript
            <?php
            echo "const buildings = " . $buildingsJS . ";\n";
            ?>
            //empty arrays for later manipulation
            let buildingLatLng = [];
            let markers = [];
            let originalPolyCoords = [];

            //create the map and polygon
            const map = createMap();
            let buildingPolygon = createBuildingPolygons(map, buildings);

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

            //initialise building name
            function formInit(buildings) {
                $("#buildingName").attr("value", buildings.name);
            }

            //creates polygons on the maps using google.maps.Polygons, currently not in use
            function createBuildingPolygons(map, buildings) {
                // initialise global arrays with coordinates
                var buildingCoords = buildings.coordinates.split(";");
                for (var j = 0; j < buildingCoords.length; j++) {
                    var buildingCoordSplit = buildingCoords[j].split(", ");
                    //main array to interact with
                    buildingLatLng.push({
                        "lat": parseFloat(buildingCoordSplit[0]),
                        "lng": parseFloat(buildingCoordSplit[1])
                    });
                    //secondary array so user can reset original coordinates if they mess up
                    originalPolyCoords.push({
                        "lat": parseFloat(buildingCoordSplit[0]),
                        "lng": parseFloat(buildingCoordSplit[1])
                    });
                }
                //create the polygon
                var buildingPolygon = new google.maps.Polygon({
                    paths: buildingLatLng,
                    strokeColor: '#062359',
                    strokeOpacity: 0.5,
                    strokeWeight: 2,
                    fillColor: '#FFFFFF',
                    fillOpacity: .7,
                    zIndex: 100
                });
                buildingPolygon.setMap(map);
                //
                google.maps.event.addListener(map, 'click', function(event) {
                    let latLng = [{
                        "lat": event.latLng.lat(),
                        "lng": event.latLng.lng()
                    }];
                    var marker = createMarker(latLng[0]);
                    markers.push(marker);
                    changeCoords(marker, map, buildingPolygon, markers);
                    buildingLatLng.push(latLng[0]);
                    for (i = 0; i < markers.length; i++) {
                        if (i === 0 || markers.length === 1) {
                            markers[i].setIcon("http://maps.google.com/mapfiles/ms/icons/blue-dot.png");
                        } else if (i === markers.length - 1) {
                            markers[i].setIcon("http://maps.google.com/mapfiles/ms/icons/green-dot.png");
                        } else {
                            markers[i].setIcon("http://maps.google.com/mapfiles/ms/icons/red-dot.png");
                        }
                    }
                    buildingPolygon.setPaths(buildingLatLng);
                    setPolygon();
                });
                return buildingPolygon;
            };

            function createMarker(latLng) {
                var singleMarker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    draggable: true,
                    icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });
                return singleMarker;
            }

            function createMarkers() {
                for (i = 0; i < buildingLatLng.length; i++) {
                    var singleMarker = createMarker(buildingLatLng[i]);
                    var index = i;
                    if (i === 0) {
                        singleMarker.setIcon("http://maps.google.com/mapfiles/ms/icons/blue-dot.png");
                    }
                    if (i === buildingLatLng.length - 1) {
                        singleMarker.setIcon("http://maps.google.com/mapfiles/ms/icons/green-dot.png");
                    }
                    markers.push(singleMarker);
                    changeCoords(singleMarker, map, buildingPolygon, markers);
                    singleMarker.setMap(map);
                }
            }

            //Gets marker coordinates and sets it to DOM
            function changeCoords(markerobject, map, buildingPolygon, markers) {
                google.maps.event.addListener(markerobject, 'dragend', function(evt) {
                    for (j = 0; j < markers.length; j++) {
                        var marker = markers[j];
                        buildingLatLng[j].lat = marker.getPosition().lat();
                        buildingLatLng[j].lng = marker.getPosition().lng();
                    }
                    setPolygon();
                    buildingPolygon.setPaths(buildingLatLng);
                });
            }



            function redrawPolygon() {
                buildingPolygon.setPaths(buildingLatLng);
            }


            //Gets marker coordinates and sets it to DOM
            function markerCoords(markerobject) {
                const markerLat = document.getElementById("markerLat");
                const markerLng = document.getElementById("markerLng");
                google.maps.event.addListener(markerobject, 'dragend', function(evt) {
                    markerLat.setAttribute("value", evt.latLng.lat());
                    markerLng.setAttribute("value", evt.latLng.lng());
                });
            }

            function recreateBtn() {
                var recreateBtn = document.getElementById("recreatePolygon");
                recreateBtn.addEventListener("click", function() {
                    buildingLatLng = [];
                    for (i = 0; i < markers.length; i++) {
                        markers[i].setMap(null);
                    }
                    markers = [];
                    redrawPolygon();
                    setPolygon();
                });
            }

            function resetBtn() {
                var resetBtn = document.getElementById("resetPolygon");
                resetBtn.addEventListener("click", function() {
                    buildingLatLng = originalPolyCoords;
                    for (i = 0; i < markers.length; i++) {
                        markers[i].setMap(null);
                    }
                    markers = [];
                    for (i = 0; i < buildingLatLng.length; i++) {
                        var marker = createMarker(buildingLatLng[i]);
                        markers.push(marker);
                    }
                    for (i = 0; i < markers.length; i++) {
                        if (i === 0 || markers.length === 1) {
                            markers[i].setIcon("http://maps.google.com/mapfiles/ms/icons/blue-dot.png");
                        } else if (i === markers.length - 1) {
                            markers[i].setIcon("http://maps.google.com/mapfiles/ms/icons/green-dot.png");
                        } else {
                            markers[i].setIcon("http://maps.google.com/mapfiles/ms/icons/red-dot.png");
                        }
                    }
                    setPolygon();
                    redrawPolygon();
                });
            }

            function setPolygon() {
                let polygons = document.getElementById("polygons");
                let buildingCoords = "";
                for (i = 0; i < buildingLatLng.length; i++) {
                    var lastArr = buildingLatLng.length - 1;
                    if (i === lastArr) {
                        buildingCoords = buildingCoords + buildingLatLng[i].lat + ", " + buildingLatLng[i].lng;
                    } else {
                        buildingCoords = buildingCoords + buildingLatLng[i].lat + ", " + buildingLatLng[i].lng + "; ";
                    }
                }
                polygons.setAttribute("value", buildingCoords);
            }

            function buildingHTMLSetup() {
                let latLng = new google.maps.LatLng(buildings.lat, buildings.lng);
                let marker = createMarker(latLng);
                markerCoords(marker);
                marker.setIcon("http://maps.google.com/mapfiles/ms/icons/yellow-dot.png");
                let textId = document.getElementById("buildingLetterId");
                textId.setAttribute("value", buildings.b_id);
                let markerLat = document.getElementById("markerLat");
                let markerLng = document.getElementById("markerLng");
                markerLat.setAttribute("value", buildings.lat);
                markerLng.setAttribute("value", buildings.lng);
                let description = document.getElementById("description");
                description.setAttribute("value",buildings.description);
                let imageURL = document.getElementById("imageLoc");
                imageURL.setAttribute("value", buildings.background);
                let departments = document.getElementById("departments");
                departments.setAttribute("value", buildings.departments);
                let facilities = document.getElementById("facilities");
                facilities.setAttribute("value", buildings.facilities);
                setPolygon();
            }

            function main(buildings, markers) {
                formInit(buildings);
                createMarkers();
                recreateBtn();
                resetBtn();
                buildingHTMLSetup();
            }
            main(buildings, markers);
        }

        google.maps.event.addDomListener(window, "load", initialise);
    })();
</script>

</html>