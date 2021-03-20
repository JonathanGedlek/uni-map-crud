(function () {
    function initialise() {


        var zoomListener;
        var infoLoc;
        var i;
        var iconBase = 'Images/';
        var png = '.png';
        var buildingIconBase = "/buildings/";


        //empty arrays for storing google map objects

        var createdAccessibilityToilets = [];
        var createdRoutes = [];
        var createdPolygons = [];
        var createdDisabledEntrances = [];
        var createdFoodMarkers = [];
        var createdNeutralToilets = [];

        function createMap(url) {
            fetch(url).then(function (response) {
                return response.json();
            }).then(function (json) {
                const mapStyle = json;
                var mapOptions = {
                    center: new google.maps.LatLng(53.64303, -1.777420),
                    zoom: 17,
                    disableDefaultUI: false,
                    mapTypeControl: true,
                    gestureHandling: "greedy",
                    //map style JSON information
                    styles: mapStyle,
                };
                var map = new google.maps.Map(document.getElementById("mapDiv"), mapOptions);
                createCampusOutline(map);
                createBuildingPolygons(map);
                legendFilter(map);
            });
        }


        function gps(map) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    infoLoc = new google.maps.InfoWindow();
                    geoMarker = new google.maps.Marker({
                        position: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
                        map: map,
                    });
                    infoLoc.setContent('<p class = "geoText">You are here!</p>');
                    infoLoc.open(map, geoMarker);

                }, function () {
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
            var foodInfo = new google.maps.InfoWindow({
            });
            google.maps.event.addListener(foodMarker, 'click', function () {
                if (foodInfo.getMap() === null) {
                    var foodText = ("<div class = 'foodWindow'><h3>" + foodMarkers[i].loc + "</h3>" + foodMarkers[i].text + "</div>");
                    foodInfo.setContent(foodText);
                    foodInfo.open(map, foodMarker);
                }
                else {
                    foodInfo.close();
                }
            });
        }

        //gets food marker array and loops through it creating markers
        function initialiseFoodMarkers(map) {
            fetch("json/foodMarkers.json").then(function (response) {
                return response.json();
            }).then(function (json) {
                var foodMarkers = json;
                for (i = 0; i < foodMarkers.length; i++) {
                    var foodMarker = createFoodMarker(foodMarkers, map, i);
                    createFoodInfo(foodMarker, foodMarkers, map, i);
                    createdFoodMarkers.push(foodMarker);
                }
            });
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


        function createDisabledToiletInfo(disabledToilet, accessibilityToilets, map, i) {
            var disabledToiletInfo = new google.maps.InfoWindow({
            });
            google.maps.event.addListener(disabledToilet, 'click', function () {
                if (disabledToiletInfo.getMap() === null) {
                    var toiletText = "<div class='toiletText'><h3>" + accessibilityToilets[i].building_name + " Accessibility Toilets</h3><br/>";
                    for (j = 0; j < accessibilityToilets[i].room_name.length; j++) {
                        toiletText += "<li>" + accessibilityToilets[i].room_name[j] + "</li>";
                    };
                    toiletText += "</div>";
                    disabledToiletInfo.setContent(toiletText);
                    disabledToiletInfo.open(map, disabledToilet);
                }
                else {
                    disabledToiletInfo.close();
                }
            });
        }

        function initialiseDisabledToilets(map) {
            fetch("json/accessibilityToilets.json").then(function (response) {
                return response.json();
            }).then(function (json) {
                var accessibilityToilets = json;
                for (i = 0; i < accessibilityToilets.length; i++) {
                    var disabledToilet = createDisabledToilet(map, accessibilityToilets);
                    createdAccessibilityToilets.push(disabledToilet);
                    createDisabledToiletInfo(disabledToilet, accessibilityToilets, map, i);
                }

            });
        }





        //Neutral Toilet Marker Creation

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
            var neutralToiletInfo = new google.maps.InfoWindow({
            });
            google.maps.event.addListener(neutralToilet, 'click', function () {
                if (neutralToiletInfo.getMap() === null) {
                    var toiletText = "<div class='toiletText'><h3>" + neutralToilets[i][0] + " Gender Neutral Toilets</h3><br/>";
                    for (j = 0; j < neutralToilets[i][3].length; j++) {
                        toiletText += "<li>" + neutralToilets[i][3][j] + "</li>";
                    };
                    toiletText += "</div>";
                    neutralToiletInfo.setContent(toiletText);
                    neutralToiletInfo.open(map, neutralToilet);
                }
                else {
                    neutralToiletInfo.close();
                }
            });
        }

        function initialiseNeutralToilets(map) {
            fetch("json/neutralToilets.json").then(function (response) {
                return response.json();
            }).then(function (json) {
                var neutralToilets = json;
                for (i = 0; i < neutralToilets.length; i++) {
                    var neutralToilet = createNeutralToilet(map, neutralToilets);
                    createdNeutralToilets.push(neutralToilet);
                    createNeutralToiletInfo(neutralToilet, neutralToilets, map, i);
                }

            });
        }



        //Disabled Entrance

        //creates a single disabled entrance
        function createDisabledMarker(map) {
            fetch("json/disabledEntrances.json").then(function (response) {
                return response.json();
            }).then(function (json) {
                var disabledEntrances = json;
                for (i = 0; i < disabledEntrances.length; i++) {

                    var disabledMarker = new google.maps.Marker({
                        position: new google.maps.LatLng(disabledEntrances[i].coordinates[0].lat, disabledEntrances[i].coordinates[1].lng),
                        map: map,
                        icon: iconBase + "disabledentrance" + disabledEntrances[i].type + png
                    });
                    createdDisabledEntrances.push(disabledMarker);
                }
            });
        };




        // Route Creation
        //creates lines on the maps using google.maps.Polylines
        function createRoutes(map) {
            //iteration through an array that holds the lat and lang coordinates of a route
            fetch("json/routes.json").then(function (response) {
                return response.json();
            }).then(function (json) {
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
        function createBuildingPolygons(map) {
            fetch("json/building-outlines.json").then(function (response) {
                return response.json();
            }).then(function (json) {
                var buildings = json;
                for (i = 0; i < buildings.length; i++) {
                    var buildingPolygon = new google.maps.Polygon({
                        paths: buildings[i].coordinates,
                        strokeColor: '#062359',
                        strokeOpacity: 0.5,
                        strokeWeight: 2,
                        fillColor: '#FFFFFF',
                        fillOpacity: .7
                    });
                    createBuildingInfo(buildingPolygon, buildings, map, i);
                    //adds current polygon to the map
                    buildingPolygon.setMap(map);
                    //add the polygon to an array, this will be iterated over to show and hide routes in the legend
                    createdPolygons.push(buildingPolygon);
                    createButtons(buildings, i);
                    zoomCurrentMarker(map, i, buildings);
                }
            });
        };

        function createButtons(buildings, i) {
            var button = document.createElement("button");
            button.setAttribute('class', 'button buttonC');
            var buttonP = document.createElement("p");
            buttonP.setAttribute('class', 'buttonP buttonP' + i);
            buttonP.innerHTML = buildings[i].b_name;
            button.id = "button" + i;
            document.getElementById('buildingList').appendChild(button);
            document.getElementById("button" + i).appendChild(buttonP);
        }

        function zoomCurrentMarker(map, i, buildings) {
            //event listener for the building finder buttons. When clicked on, will zoom the map to the selected building
            google.maps.event.addDomListener(document.getElementById('button' + i), 'click', (function () {

                map.setZoom(18);
                map.panTo(new google.maps.LatLng(buildings[i].b_lat, buildings[i].b_lng));

            }));
        }


        function createBuildingInfo(buildingPolygon, buildings, map, i) {
            //empty strings to initialise image and facilities field, also stops undefined errors if building doesn't have an image or facility info
            var img = "";
            var facilities = "";
            //create an infoWindow
            var buildingInfo = new google.maps.InfoWindow();
            //when clicked on, populate the local variables above with building information for the selected building marker and set the content to
            //that specific marker
            if (buildings[i].b_facilities != null) {
                facilities = "<div class = 'facilities'><h4>Facilities</h4>" + buildings[i].b_facilities + "</div>";
            }
            if (buildings[i].b_background != null) {
                img = "<img height='100px' src='" + iconBase + buildingIconBase + buildings[i].b_background + "'>";
            }
            buildingInfo.setContent("<div class = 'infoWindow'><div><h3 class = 'locname'>" + buildings[i].b_name + "</div><div><div class = 'infoContentWrap'>" + img + facilities + "</div></div></div></div>");
            buildingInfo.setPosition(new google.maps.LatLng(buildings[i].b_lat, buildings[i].b_lng));
            //Makes sure it starts as a null value
            buildingInfo.setMap(null);
            google.maps.event.addListener(buildingPolygon, 'click', function () {
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
            fetch("json/campusOutline.json").then(function (response) {
                return response.json();
            }).then(function (json) {
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


        function legendFilter(map) {
            $('#gps').change(function () {
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

            $('#neutralToilet').change(function () {
                //if the routes checkbox is unchecked
                if ($('#neutralToilet:checked').length == 0) {
                    //loops through the array storing route objects and deletes them
                    for (i = 0; i < createdNeutralToilets.length; i++) {
                        createdNeutralToilets[i].setMap(null);
                    }
                }
                //if the building checkbox is checked, re-enitialise the routes
                else if ($('#neutralToilet:checked').length > 0) {
                    initialiseNeutralToilets(map);
                }
            });

            $('#accessibleToilet').change(function () {
                //if the routes checkbox is unchecked
                if ($('#accessibleToilet:checked').length == 0) {
                    //loops through the array storing route objects and deletes them
                    for (i = 0; i < createdAccessibilityToilets.length; i++) {
                        createdAccessibilityToilets[i].setMap(null);
                    }
                }
                //if the building checkbox is checked, re-enitialise the routes
                else if ($('#accessibleToilet:checked').length > 0) {
                    initialiseDisabledToilets(map);
                }
            });

            //jquery event listener to check if routes tickbox has changed
            $('#routes').change(function () {
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
            $('#buildingOutlines').change(function () {
                //if the routes checkbox is unchecked
                if ($('#buildingOutlines:checked').length == 0) {
                    //loops through the array storing route objects and deletes them
                    for (i = 0; i < createdPolygons.length; i++) {
                        createdPolygons[i].setMap(null);
                    }
                }
                //if the building checkbox is checked, re-enitialise the routes
                else if ($('#buildingOutlines:checked').length > 0) {
                    createBuildingPolygons(map);
                }
            });

            $('#disabledEntrances').change(function () {
                //if the routes checkbox is unchecked
                if ($('#disabledEntrances:checked').length == 0) {
                    //loops through the array storing route objects and deletes them
                    for (i = 0; i < createdDisabledEntrances.length; i++) {
                        createdDisabledEntrances[i].setMap(null);
                    }
                }
                //if the building checkbox is checked, re-enitialise the routes
                else if ($('#disabledEntrances:checked').length > 0) {
                    createDisabledMarker(map);
                }
            });

            $('#food').change(function () {
                //if the routes checkbox is unchecked
                if ($('#food:checked').length == 0) {
                    //loops through the array storing route objects and deletes them
                    for (i = 0; i < createdFoodMarkers.length; i++) {
                        createdFoodMarkers[i].setMap(null);
                    }
                }
                //if the building checkbox is checked, re-enitialise the routes
                else if ($('#food:checked').length > 0) {
                    initialiseFoodMarkers(map);
                }
            });
        }










        function main() {
            createMap("json/map-styles.json");
        }
        main();

    }
    google.maps.event.addDomListener(window, "load", initialise);
})();

