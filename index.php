<?php
// Include config file
require_once "admin/config.php";

//prepare statements
$stmtNeutral = $conn->prepare("SELECT * FROM neutraltoilets");
$stmtFood = $conn->prepare("SELECT * FROM foodmarkers");
$stmtDisabledEntrances = $conn->prepare("SELECT * FROM disabledentrances");
$stmtaccessibilitytoilets = $conn->prepare("SELECT * FROM accessibilitytoilets");
$stmtbuildings = $conn->prepare("SELECT * FROM buildings");

//executes
$stmtNeutral->execute();
$stmtFood->execute();
$stmtDisabledEntrances->execute();
$stmtaccessibilitytoilets->execute();
$stmtbuildings->execute();

//close connection
$conn = null;

//fetch data
$neutralToiletsPHP = $stmtNeutral->fetchAll();
$foodMarkersPHP = $stmtFood->fetchAll();
$disabledEntrancesPHP = $stmtDisabledEntrances->fetchAll();
$accessibilityToiletsPHP = $stmtaccessibilitytoilets->fetchAll();
$buildingsPHP = $stmtbuildings->fetchAll();

//encode for use in javascript
$neutralToiletsJS = json_encode($neutralToiletsPHP);
$foodMarkersJS = json_encode($foodMarkersPHP);
$disabledEntrancesJS = json_encode($disabledEntrancesPHP);
$accessibilityToiletsJS = json_encode($accessibilityToiletsPHP);
$buildingsJS = json_encode($buildingsPHP);


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- style sheet -->
  <link rel="stylesheet" type="text/css" href="styles/style.css" />
  <!-- bootstrap css -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <title>University Map</title>
</head>

<body class="index-body">
  <div id="legend-div">
  </div>
  <div class="mapDiv" id="mapDiv">
    <p>Something Went Wrong!</p>
  </div>
  <div class="accordion" id="legendAccordian">
    <div class="card">
      <div class="card-header" id="headingTwo">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#legendContainer" aria-expanded="true" aria-controls="legend">
          <div class="buildingFinderTitle">Map Filters</div>
        </button>
      </div>
      <div id="legendContainer" class="collapse" aria-labelledby="headingOne" data-parent="#legendAccordian">
        <form id='legend'>
          <label class="form-check-label" for="buildingOutlines">Buildings</label>
          <input type="checkbox" class="form-check-input" id="buildingOutlines" name="buildingOutlines" checked /><br>
          <label class="form-check-label" for="gps">GPS Marker</label>
          <input type="checkbox" class="form-check-input" id="gps" name="gps" /><br>
          <label class="form-check-label" for="routes">Accessible Routes</label>
          <input type="checkbox" class="form-check-input" id="routes" name="routes" /><br>
          <label class="form-check-label" for="disabledEntrances">Disabled Entrances</label>
          <input type="checkbox" class="form-check-input" id="disabledEntrances" name="disabledEntrances" /><br>
          <label class="form-check-label" for="food">Food and Drink</label>
          <input type="checkbox" class="form-check-input" id="food" name="food" /><br>
          <label class="form-check-label" for="neutralToilet">Gender Neutral Toilets</label>
          <input type="checkbox" class="form-check-input" id="neutralToilet" name="neutralToilet" /><br>
          <label class="form-check-label" for="accessibleToilet">Accessibility Toilets</label>
          <input type="checkbox" class="form-check-input" id="accessibleToilet" name="accessibleToilet" /><br>
          <label class="form-check-label" for="campusOutline">Campus Outline</label>
          <input type="checkbox" class="form-check-input" id="campusOutline" name="campusOutline" checked />
        </form>
      </div>
    </div>
  </div>


  <div class="accordion" id="buildingAccordian">
    <div class="card">
      <div class="card-header" id="headingOne">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#buttonListContainer" aria-expanded="true" aria-controls="buttonListContainer">
          <p class="buildingFinderTitle">Building Finder</p>
        </button>
      </div>
      <div id="buttonListContainer" class="collapse" aria-labelledby="headingOne" data-parent="#buildingAccordian">
        <div id="buildingList" class="card-body">
        </div>
      </div>
    </div>
  </div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5Nf-Bw1vZ4ZtH0N-tbk7t6lDEvXVdALc"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script>
  (function() {
    function initialise() {

      //globals
      var zoomListener;
      var infoLoc;
      var i;
      var iconBase = 'Images/';
      var png = '.png';
      var buildingIconBase = "/buildings/";
      //creation of map as a global var
      const map = createMap();
      //converting PHP arrays to JS
      <?php
      echo "const neutralToilets = " . $neutralToiletsJS . ";\n";
      echo "const foodMarkers = " . $foodMarkersJS . ";\n";
      echo "const disabledEntrances = " . $disabledEntrancesJS . ";\n";
      echo "const accessibilityToilets = " . $accessibilityToiletsJS . ";\n";
      echo "const buildings = " . $buildingsJS . ";\n";
      ?>




      //empty arrays for storing google map objects
      var createdAccessibilityToilets = [];
      var createdRoutes = [];
      var createdPolygons = [];
      var createdDisabledEntrances = [];
      var createdFoodMarkers = [];
      var createdNeutralToilets = [];

      //builds map and adds style
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


      //GPS marker code
      function gps(map) {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            infoLoc = new google.maps.InfoWindow();
            geoMarker = new google.maps.Marker({
              position: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
              map: map,
            });
            infoLoc.setContent('<p class = "geoText">You are here!</p>');
            infoLoc.open(map, geoMarker);

          }, function() {
            handleLocationError(true, infoWindow, map.getCenter());
          });
        } else {
          // in case Browser doesn't support Geolocation
          handleLocationError(false, infoWindow, map.getCenter());
        }
      }




      //FOOD MARKER CODE

      //creates a single group marker
      function createFoodMarker(foodMarkers, map, i) {
        var foodMarker = new google.maps.Marker({
          position: new google.maps.LatLng(foodMarkers[i].lat, foodMarkers[i].lng),
          map: map,
          icon: iconBase + "Food" + png
        });
        return foodMarker;
      }

      //adds an on click event to each marker to display the food in the 
      //corresponding building
      function createFoodInfo(foodMarker, foodMarkers, map, i) {
        var foodInfo = new google.maps.InfoWindow({});
        foodInfo.setMap(null);
        google.maps.event.addListener(foodMarker, 'click', function() {
          if (foodInfo.getMap() === null) {
            var foodText = ("<div class = 'foodWindow'><h3>" + foodMarkers[i].location + "</h3>");
            var stores = foodMarkers[i].text.split(";");
            for (i = 0; i < stores.length; i++) {
              var store = "<li>" + stores[i] + "</li>";
              foodText = foodText + store;
            }
            foodText = foodText + "</div>";
            foodInfo.setContent(foodText);
            foodInfo.open(map, foodMarker);
          } else {
            foodInfo.close();
          }
        });
      }

      //gets food marker array and loops through it creating markers
      function initialiseFoodMarkers(map, foodMarkers) {
        for (i = 0; i < foodMarkers.length; i++) {
          var foodMarker = createFoodMarker(foodMarkers, map, i);
          createFoodInfo(foodMarker, foodMarkers, map, i);
          createdFoodMarkers.push(foodMarker);
        }
      }


      //Disabled Toilet Creation
      function createDisabledToilet(map, accessibilityToilets) {
        //create a marker, grab the coordinates from the list of group markers, stick it on the map and apply the custom icon
        var disabledToilet = new google.maps.Marker({
          position: new google.maps.LatLng(accessibilityToilets[i].lat, accessibilityToilets[i].lng),
          map: map,
          icon: iconBase + 'disabledtoilet' + png
        });
        return disabledToilet;
      }
      //builds an info pane on a given marker
      function createDisabledToiletInfo(disabledToilet, accessibilityToilets, map, i) {
        var disabledToiletInfo = new google.maps.InfoWindow();
        //needs to be set as null on the map or first click of the marker doesn't open it
        disabledToiletInfo.setMap(null);
        const roomCode = accessibilityToilets[i].room_name.split(', ');
        google.maps.event.addListener(disabledToilet, 'click', function() {
          if (disabledToiletInfo.getMap() === null) {
            var toiletText = "<div class='toiletText'><h3>" + accessibilityToilets[i].building_name + " Accessibility Toilets</h3><br/><b>Room Codes</b><br>";
            for (j = 0; j < roomCode.length; j++) {
              toiletText += "<li>" + roomCode[j] + "</li>";
            };
            toiletText += "</div>";
            disabledToiletInfo.setContent(toiletText);
            disabledToiletInfo.open(map, disabledToilet);
          } else {
            disabledToiletInfo.close();
          }
        });
      }

      function initialiseDisabledToilets(map, accessibilityToilets) {
        for (i = 0; i < accessibilityToilets.length; i++) {
          var disabledToilet = createDisabledToilet(map, accessibilityToilets);
          createdAccessibilityToilets.push(disabledToilet);
          createDisabledToiletInfo(disabledToilet, accessibilityToilets, map, i);
        }
      }





      //Neutral Toilet Marker Creation

      function createNeutralToilet(map, neutralToilets) {
        //create a marker, grab the coordinates from the list of group markers, stick it on the map and apply the custom icon
        var neutralToilet = new google.maps.Marker({
          position: new google.maps.LatLng(neutralToilets[i][2], neutralToilets[i][3]),
          map: map,
          icon: iconBase + 'neutralbathroom' + png
        });
        return neutralToilet;
      }

      function createNeutralToiletInfo(neutralToilet, neutralToilets, map, i) {
        var neutralToiletInfo = new google.maps.InfoWindow({});
        //null sett
        neutralToiletInfo.setMap(null);
        const roomCodes = neutralToilets[i].room_code;
        const roomCodeArr = roomCodes.split(", ");
        google.maps.event.addListener(neutralToilet, 'click', function() {
          if (neutralToiletInfo.getMap() === null) {
            var toiletText = "<div class='toiletText'><h3>" + neutralToilets[i][1] + " Gender Neutral Toilets</h3><br/><b>Room Codes</b><br>";
            for (j = 0; j < roomCodeArr.length; j++) {
              toiletText += "<li>" + roomCodeArr[j] + "</li>";
            };
            toiletText += "</div>";
            neutralToiletInfo.setContent(toiletText);
            neutralToiletInfo.open(map, neutralToilet);
          } else {
            neutralToiletInfo.close();
          }
        });
      }

      function initialiseNeutralToilets(map, neutralToilets) {
        for (i = 0; i < neutralToilets.length; i++) {
          var neutralToilet = createNeutralToilet(map, neutralToilets);
          createdNeutralToilets.push(neutralToilet);
          createNeutralToiletInfo(neutralToilet, neutralToilets, map, i);
        }
      }



      //Disabled Entrance

      //creates a single disabled entrance
      function createDisabledMarker(map, disabledEntrances) {
        for (i = 0; i < disabledEntrances.length; i++) {
          var disabledMarker = new google.maps.Marker({
            position: new google.maps.LatLng(disabledEntrances[i].lat, disabledEntrances[i].lng),
            map: map,
            icon: iconBase + "disabledentrance" + disabledEntrances[i].type + png
          });
          createdDisabledEntrances.push(disabledMarker);
        }
      };




      // Route Creation
      //creates lines on the maps using google.maps.Polylines
      function createRoutes(map) {
        //iteration through an array that holds the lat and lang coordinates of a route
        fetch("json/routes.json").then(function(response) {
          return response.json();
        }).then(function(json) {
          var routes = json;
          for (i = 0; i < routes.length; i++) {
            var route = new google.maps.Polyline({
              path: routes[i],
              geodesic: true,
              strokeColor: '#FF0000',
              strokeOpacity: 0.5,
              strokeWeight: 3
            });
            //add the route to the map
            route.setMap(map);
            //add the route to an array, this will be iterated over to show and hide routes in the legend
            createdRoutes.push(route);
          }
        });
      };


      //creates polygons on the maps using google.maps.Polygons, currently not in use
      function createBuildingPolygons(map, buildings) {
        for (i = 0; i < buildings.length; i++) {
          var buildingCoords = buildings[i].coordinates.split(";");
          let buildingLatLng = [];
          for (var j = 0; j < buildingCoords.length; j++) {
            var buildingCoordSplit = buildingCoords[j].split(", ");
            buildingLatLng.push({
              "lat": parseFloat(buildingCoordSplit[0]),
              "lng": parseFloat(buildingCoordSplit[1])
            });
          }
          var buildingPolygon = new google.maps.Polygon({
            paths: buildingLatLng,
            strokeColor: '#062359',
            strokeOpacity: 0.5,
            strokeWeight: 2,
            fillColor: '#FFFFFF',
            fillOpacity: .7,
            zIndex: 100
          });

          createBuildingInfo(buildingPolygon, buildings, map, i);
          //adds current polygon to the map
          buildingPolygon.setMap(map);
          //add the polygon to an array, this will be iterated over to show and hide routes in the legend
          createdPolygons.push(buildingPolygon);
          createButtons(buildings, i);
          zoomCurrentMarker(map, i, buildings);
        }
      };

      //creates the list of buttons in the building finder
      function createButtons(buildings, i) {
        var button = document.createElement("button");
        button.setAttribute('class', 'button buttonC');
        var buttonP = document.createElement("p");
        buttonP.setAttribute('class', 'buttonP buttonP' + i);
        buttonP.innerHTML = buildings[i].name;
        button.id = "button" + i;
        document.getElementById('buildingList').appendChild(button);
        document.getElementById("button" + i).appendChild(buttonP);
      }

      function zoomCurrentMarker(map, i, buildings) {
        //event listener for the building finder buttons. When clicked on, will zoom the map to the selected building
        google.maps.event.addDomListener(document.getElementById('button' + i), 'click', (function() {

          map.setZoom(18);
          map.panTo(new google.maps.LatLng(buildings[i].lat, buildings[i].lng));

        }));
      }




      function createBuildingInfo(buildingPolygon, buildings, map, i) {
        //empty strings to initialise image and facilities field, also stops undefined errors if building doesn't have an image or facility info
        var facilityText = "";
        var img = "";
        var description = "";
        var buildingFacilities = buildings[i].facilities;
        var image = buildings[i].background;
        var buildingDescription = buildings[i].description;

        //make sure empty strings aren't added as content
        if (buildingFacilities == "") {
          buildingFacilities = null;
        }
        if (buildingDescription === "") {
          buildingDescription = null;
        }

        if (image == "") {
          image = null;
        }
        //create an infoWindow
        var buildingInfo = new google.maps.InfoWindow();


        //when clicked on, populate the local variables above with building information for the selected building marker and set the content to
        //that specific marker
        if (buildings[i].description != null || buildings[i].description != "") {
          description = buildings[i].description;
        }
        if (buildingFacilities != null) {
          var facilities = buildings[i].facilities.split("; ");
          for (j = 0; j < facilities.length; j++) {
            facilityText = facilityText + "<li>" + facilities[j] + "</li>";
          }
          facilityText = "<div class = 'facilities'><h4>Facilities</h4><ul>" + facilityText + "</ul></div>";
        }
        if(buildingDescription != null){
          description = "<div class='description'>" + description + "</div>";
        }
        if (image != null) {
          img = "<img height='100px' src='" + iconBase + buildingIconBase + buildings[i].background + "'>";
        }
        
        buildingInfo.setContent("<div class = 'infoWindow'><div><h3 class = 'locname'>" + buildings[i].name + "</div><div><div class = 'infoContentWrap'>" + img + facilityText + "</div></div></div></div>");
        const positionLat = parseFloat(buildings[i].lat);
        const positionLng = parseFloat(buildings[i].lng);
        buildingInfo.setPosition(new google.maps.LatLng(positionLat, positionLng));
        //Makes sure it starts as a null value
        buildingInfo.setMap(null);
        google.maps.event.addListener(buildingPolygon, 'click', function() {
          if (buildingInfo.getMap() === null) {
            buildingInfo.open(map);
          }
          //if the info panel for the building is already opened when clicked on, close it instead.
          else {
            buildingInfo.setMap(null);
          }
        });
      };


      function createCampusOutline(map) {
        fetch("json/campusOutline.json").then(function(response) {
          return response.json();
        }).then(function(json) {
          var campusOutline = json;
          var campusPolygon = new google.maps.Polygon({
            paths: campusOutline,
            strokeColor: '#062359',
            strokeOpacity: 0.5,
            strokeWeight: 2,
            fillColor: '#062359',
            fillOpacity: 0.05
          });
          campusPolygon.setMap(map);
        });
      }

      //code related to the filter system, simply creates a listener for the filter and either sets the element to the map or takes it off
      function legendFilter(map, neutralToilets, disabledEntrances, accessibilityToilets, buildings) {
        $('#gps').change(function() {
          //if its been unchecked, delete the gps marker off of the map
          if ($('#gps:checked').length == 0) {
            geoMarker.setMap(null);
            infoLoc.setMap(null);
          }
          //if its been checked, recall the function that creates the GPS icon
          else if ($('#gps:checked').length > 0) {
            gps(map);
          }
        });

        $('#neutralToilet').change(function() {
          //if the routes checkbox is unchecked
          if ($('#neutralToilet:checked').length == 0) {
            //loops through the array storing route objects and deletes them
            for (i = 0; i < createdNeutralToilets.length; i++) {
              createdNeutralToilets[i].setMap(null);
            }
          }
          //if the building checkbox is checked, re-enitialise the routes
          else if ($('#neutralToilet:checked').length > 0) {
            initialiseNeutralToilets(map, neutralToilets);
          }
        });

        $('#accessibleToilet').change(function() {
          //if the routes checkbox is unchecked
          if ($('#accessibleToilet:checked').length == 0) {
            //loops through the array storing route objects and deletes them
            for (i = 0; i < createdAccessibilityToilets.length; i++) {
              createdAccessibilityToilets[i].setMap(null);
            }
          }
          //if the building checkbox is checked, re-enitialise the routes
          else if ($('#accessibleToilet:checked').length > 0) {
            initialiseDisabledToilets(map, accessibilityToilets);
          }
        });

        //jquery event listener to check if routes tickbox has changed
        $('#routes').change(function() {
          //if the routes checkbox is unchecked
          if ($('#routes:checked').length == 0) {
            //loops through the array storing route objects and deletes them
            for (i = 0; i < createdRoutes.length; i++) {
              createdRoutes[i].setMap(null);
            }
          }
          //if the building checkbox is checked, re-enitialise the routes
          else if ($('#routes:checked').length > 0) {
            createRoutes(map);
          }
        });

        //jquery event listener to check if building tickbox has changed
        $('#buildingOutlines').change(function() {
          //if the routes checkbox is unchecked
          if ($('#buildingOutlines:checked').length == 0) {
            //loops through the array storing route objects and deletes them
            for (i = 0; i < createdPolygons.length; i++) {
              createdPolygons[i].setMap(null);
            }
          }
          //if the building checkbox is checked, re-enitialise the routes
          else if ($('#buildingOutlines:checked').length > 0) {
            createBuildingPolygons(map, buildings);
          }
        });

        $('#disabledEntrances').change(function() {
          //if the routes checkbox is unchecked
          if ($('#disabledEntrances:checked').length == 0) {
            //loops through the array storing route objects and deletes them
            for (i = 0; i < createdDisabledEntrances.length; i++) {
              createdDisabledEntrances[i].setMap(null);
            }
          }
          //if the building checkbox is checked, re-enitialise the routes
          else if ($('#disabledEntrances:checked').length > 0) {
            createDisabledMarker(map, disabledEntrances);
          }
        });

        $('#food').change(function() {
          //if the routes checkbox is unchecked
          if ($('#food:checked').length == 0) {
            //loops through the array storing route objects and deletes them
            for (i = 0; i < createdFoodMarkers.length; i++) {
              createdFoodMarkers[i].setMap(null);
            }
          }
          //if the building checkbox is checked, re-enitialise the routes
          else if ($('#food:checked').length > 0) {
            initialiseFoodMarkers(map, foodMarkers);
          }
        });
      }


      //Call of all functions that create items immediately displayed on the map at initialisation of page
      function main(neutralToilets, map, disabledEntrances, accessibilityToilets, buildings) {
        createCampusOutline(map);
        createBuildingPolygons(map, buildings);
        legendFilter(map, neutralToilets, disabledEntrances, accessibilityToilets, buildings);
      }
      main(neutralToilets, map, disabledEntrances, accessibilityToilets, buildings);

    }
    //Don't do anything till page is fully loaded
    google.maps.event.addDomListener(window, "load", initialise);
  })();
</script>

</html>