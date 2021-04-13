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

//prepare statements
$stmtNeutral = $conn->prepare("SELECT * FROM neutraltoilets");
//executes
$stmtNeutral->execute();
//fetch data
$neutralToiletsPHP = $stmtNeutral->fetchAll();
//encode for use in javascript
$neutralToiletsJS = json_encode($neutralToiletsPHP);

$conn = null;
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

    <title>Neutral Toilets</title>
</head>

<body class="admin-body">
    <div class="container-fluid">
        <nav class="row">
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-sm-center text-md-center text-lg-left text-xl-left logged-in">Logged in as <?php echo " " . htmlspecialchars($_SESSION["username"]); ?></div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 text-center admin-title ">Neutral Toilets</div>
            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-flex justify-content-xl-end justify-content-lg-end m-auto justify-content-md-center justify-content-sm-center justify-content-xs-center log-btns">
                <button type="button" class="btn btn-success"><a href="../admin.php"> Back</a></button>
                <button type="button" class="btn btn-warning"><a href="../reset-password.php">Reset Password</a></button>
                <button type="button" class="btn btn-danger"><a href="../logout.php">Sign Out</a></button>
            </div>
        </nav>
    </div>
    <div class="mapAdminDiv" id="mapDiv">
        <p>Something Went Wrong!</p>
    </div>
    <button class="btn btn-info" type="button"><a href="neutralCreate.php">Create New Marker</a></button>
    <div id="error">
    </div>
</body>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5Nf-Bw1vZ4ZtH0N-tbk7t6lDEvXVdALc"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
    (function() {
        function initialise() {
            //globals
            var iconBase = '../../Images/';
            var png = '.png';
            const map = createMap();
            var createdNeutralToilets = [];
            const urlParams = new URLSearchParams(window.location.search);
            const idError = urlParams.get("iderror");

            //error message to display if rerouted from other pages without url params
            if (idError === "true") {
                const idErrorMsg = document.getElementById("error");
                const pTag = document.createElement("p");
                pTag.innerHTML = "You cannot update the database without choosing a marker.";
                idErrorMsg.appendChild(pTag);
            }

            //translates the JSON array from PHP to JS
            <?php
            echo "const neutralToilets = " . $neutralToiletsJS . ";\n";
            ?>

            var createdNeutralToilets = [];


            function createMap() {
                var mapOptions = {
                    center: new google.maps.LatLng(53.64303, -1.777420),
                    zoom: 17,
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
                };
                var map = new google.maps.Map(document.getElementById("mapDiv"), mapOptions);
                return map;
            }

            function createNeutralToilet(map, neutralToilets) {
                //create a marker, grab the coordinates from the list of group markers, stick it on the map and apply the custom icon
                var neutralToilet = new google.maps.Marker({
                    position: new google.maps.LatLng(neutralToilets[i].lat, neutralToilets[i].lng),
                    map: map,
                    icon: iconBase + 'neutralbathroom' + png
                });
                return neutralToilet;
            }

            function createNeutralToiletInfo(neutralToilet, neutralToilets, map, i) {
                var neutralToiletInfo = new google.maps.InfoWindow();
                var toiletText = '<div class="text"><h4>' + neutralToilets[i].building_name + ' Gender Neutral Toilets</h4><br><a href="neutralEdit.php?id=' + neutralToilets[i].id + '">Edit</a><br><br><a onclick="return confirm(`Are you sure you want to delete this marker?`)" id="deleteBtn'+i+'"href="neutralDelete.php?id=' + neutralToilets[i].id + '">Delete</a>';
                toiletText += "</div>";
                neutralToiletInfo.setContent(toiletText);
                neutralToiletInfo.setMap(null);
                google.maps.event.addListener(neutralToilet, 'click', function() {
                    if (neutralToiletInfo.getMap() === null) {
                        neutralToiletInfo.open(map, neutralToilet);
                    } else {
                        neutralToiletInfo.close();
                    }
                });
            }

            function initialiseNeutralToilets(map, neutralToilets) {
                for (i = 0; i < neutralToilets.length; i++) {
                    var neutralToilet = createNeutralToilet(map, neutralToilets, i);
                    createdNeutralToilets.push(neutralToilet);
                    createNeutralToiletInfo(neutralToilet, neutralToilets, map, i);
                }
            }

            function main(map, neutralToilets) {
                initialiseNeutralToilets(map, neutralToilets);
            }
            main(map, neutralToilets);
        }
        google.maps.event.addDomListener(window, "load", initialise);
    })();
</script>


</html>