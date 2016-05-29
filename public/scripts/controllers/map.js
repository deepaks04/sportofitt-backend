'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:MapCtrl
 * @description
 * # MapCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
    .controller('MapCtrl', function (searchService, toastr, $filter) {
        var mapStyles = [{"featureType": "road", "elementType": "labels", "stylers": [{"visibility": "simplified"}, {"lightness": 20}]}, {"featureType": "administrative.land_parcel", "elementType": "all", "stylers": [{"visibility": "off"}]}, {"featureType": "landscape.man_made", "elementType": "all", "stylers": [{"visibility": "on"}]}, {"featureType": "transit", "elementType": "all", "stylers": [{"saturation": -100}, {"visibility": "on"}, {"lightness": 10}]}, {"featureType": "road.local", "elementType": "all", "stylers": [{"visibility": "on"}]}, {"featureType": "road.local", "elementType": "all", "stylers": [{"visibility": "on"}]}, {"featureType": "road.highway", "elementType": "labels", "stylers": [{"visibility": "simplified"}]}, {"featureType": "poi", "elementType": "labels", "stylers": [{"visibility": "off"}]}, {"featureType": "road.arterial", "elementType": "labels", "stylers": [{"visibility": "on"}, {"lightness": 50}]}, {"featureType": "water", "elementType": "all", "stylers": [{"hue": "#a1cdfc"}, {"saturation": 30}, {"lightness": 49}]}, {"featureType": "road.highway", "elementType": "geometry", "stylers": [{"hue": "#f49935"}]}, {"featureType": "road.arterial", "elementType": "geometry", "stylers": [{"hue": "#fad959"}]}, {featureType: 'road.highway', elementType: 'all', stylers: [{hue: '#dddbd7'}, {saturation: -92}, {lightness: 60}, {visibility: 'on'}]}, {featureType: 'landscape.natural', elementType: 'all', stylers: [{hue: '#c8c6c3'}, {saturation: -71}, {lightness: -18}, {visibility: 'on'}]}, {featureType: 'poi', elementType: 'all', stylers: [{hue: '#d9d5cd'}, {saturation: -70}, {lightness: 20}, {visibility: 'on'}]}];
        var vm = this;
        vm.latitude = 18.5074;
        vm.longitude = 73.8077;

        vm.filter = {area_id:14};
        vm.types = ['Venue', 'Coaching'];


        // Load JSON data and create Google Maps

        searchService.getCategories().then(function (response) {
            vm.subCategories = response.data.category;
        }).catch(function (errors) {
            toastr.error(errors);
        })

        searchService.getArea().then(function (response) {
            vm.areas = response.data.area;
            vm.filter.area = vm.areas[13];
        }).catch(function (errors) {
            toastr.error(errors);
        })

        searchService.search().then(function (response) {
            vm.masterData = response.data;
            vm.mapData = angular.copy(vm.masterData);
            vm.setFilters(vm.filter);
            //createHomepageGoogleMap(vm.latitude, vm.longitude, vm.mapData);
        }).catch(function (errors) {
            toastr.error(errors);
        })

        vm.setLangLat = function(area){
            if(area) {
                vm.latitude = area.latitude;
                vm.longitude = area.longitude;
                vm.filter.area_id = area.id;
            }else{

                vm.latitude = 18.5073551;
                vm.longitude = 73.7871018;
                vm.filter.area_id = 14;
            }
        };

        vm.setFilters = function (newfilter) {
            var filter = angular.copy(newfilter);
            angular.forEach(filter, function (value, key) {
                if (!value || key == 'area') {
                    delete filter[key];
                }
            });
            vm.mapData['data'] = $filter('filter')(vm.masterData.data, filter, true);
            createHomepageGoogleMap(vm.latitude, vm.longitude, vm.mapData);
        }

        function createHomepageGoogleMap(_latitude, _longitude, json) {
            gMap();
            function gMap() {
                //if (navigator.geolocation) {
                //    navigator.geolocation.getCurrentPosition(success);
                //} else {
                //    console.log('Geo Location is not supported');
                //}
                var mapCenter = new google.maps.LatLng(vm.latitude, vm.longitude);
                var mapOptions = {
                    zoom: 14,
                    center: mapCenter,
                    disableDefaultUI: false,
                    scrollwheel: false,
                    styles: mapStyles,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                        position: google.maps.ControlPosition.BOTTOM_CENTER
                    },
                    panControl: false,
                    zoomControl: true,
                    zoomControlOptions: {
                        style: google.maps.ZoomControlStyle.LARGE,
                        position: google.maps.ControlPosition.RIGHT_CENTER
                    }
                };
                var mapElement = document.getElementById('map');
                var map = new google.maps.Map(mapElement, mapOptions);
                var newMarkers = [];
                var markerClicked = 0;
                var activeMarker = false;
                var lastClicked = false;

                for (var i = 0; i < json.data.length; i++) {

                    // Google map marker content -----------------------------------------------------------------------------------

                    if (json.data[i].color)
                        var color = json.data[i].color;
                    else
                        color = '';

                    var markerContent = document.createElement('DIV');
                    if (json.data[i].featured == 1) {
                        markerContent.innerHTML =
                            '<div class="map-marker featured' + color + '">' +
                            '<div class="icon">' +
                            '<img src="' + json.data[i].type_icon + '">' +
                            '</div>' +
                            '</div>';
                    } else {
                        markerContent.innerHTML =
                            '<div class="map-marker ' + json.data[i].color + '">' +
                            '<div class="icon">' +
                            '<img src="' + json.data[i].type_icon + '">' +
                            '</div>' +
                            '</div>';
                    }

                    // Create marker on the map ------------------------------------------------------------------------------------

                    var marker = new RichMarker({
                        position: new google.maps.LatLng(json.data[i].latitude, json.data[i].longitude),
                        map: map,
                        draggable: false,
                        content: markerContent,
                        flat: true
                    });

                    newMarkers.push(marker);

                    // Create infobox for marker -----------------------------------------------------------------------------------

                    var infoboxContent = document.createElement("div");
                    var infoboxOptions = {
                        content: infoboxContent,
                        disableAutoPan: false,
                        pixelOffset: new google.maps.Size(-18, -42),
                        zIndex: null,
                        alignBottom: true,
                        boxClass: "infobox",
                        enableEventPropagation: true,
                        closeBoxMargin: "0px 0px -30px 0px",
                        closeBoxURL: "assets/img/close.png",
                        infoBoxClearance: new google.maps.Size(1, 1)
                    };
                    // Infobox HTML element ----------------------------------------------------------------------------------------

                    var category = json.data[i].category;
                    infoboxContent.innerHTML = drawInfobox(category, infoboxContent, json, i);

                    // Create new markers ------------------------------------------------------------------------------------------

                    newMarkers[i].infobox = new InfoBox(infoboxOptions);

                    // Show infobox after click ------------------------------------------------------------------------------------

                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {
                            google.maps.event.addListener(map, 'click', function (event) {
                                lastClicked = newMarkers[i];
                            });
                            activeMarker = newMarkers[i];
                            if (activeMarker != lastClicked) {
                                for (var h = 0; h < newMarkers.length; h++) {
                                    newMarkers[h].content.className = 'marker-loaded';
                                    newMarkers[h].infobox.close();
                                }
                                newMarkers[i].infobox.open(map, this);
                                newMarkers[i].infobox.setOptions({boxClass: 'fade-in-marker'});
                                newMarkers[i].content.className = 'marker-active marker-loaded';
                                markerClicked = 1;
                            }
                        }
                    })(marker, i));

                    // Fade infobox after close is clicked -------------------------------------------------------------------------

                    google.maps.event.addListener(newMarkers[i].infobox, 'closeclick', (function (marker, i) {
                        return function () {
                            activeMarker = 0;
                            newMarkers[i].content.className = 'marker-loaded';
                            newMarkers[i].infobox.setOptions({boxClass: 'fade-out-marker'});
                        }
                    })(marker, i));
                }

                // Close infobox after click on map --------------------------------------------------------------------------------

                google.maps.event.addListener(map, 'click', function (event) {
                    if (activeMarker != false && lastClicked != false) {
                        if (markerClicked == 1) {
                            activeMarker.infobox.open(map);
                            activeMarker.infobox.setOptions({boxClass: 'fade-in-marker'});
                            activeMarker.content.className = 'marker-active marker-loaded';
                        } else {
                            markerClicked = 0;
                            activeMarker.infobox.setOptions({boxClass: 'fade-out-marker'});
                            activeMarker.content.className = 'marker-loaded';
                            setTimeout(function () {
                                activeMarker.infobox.close();
                            }, 350);
                        }
                        markerClicked = 0;
                    }
                    if (activeMarker != false) {
                        google.maps.event.addListener(activeMarker, 'click', function (event) {
                            markerClicked = 1;
                        });
                    }
                    markerClicked = 0;
                });

                // Create marker clusterer -----------------------------------------------------------------------------------------

                var clusterStyles = [
                    {
                        url: 'assets/img/cluster.png',
                        height: 34,
                        width: 34
                    }
                ];

                var markerCluster = new MarkerClusterer(map, newMarkers, {styles: clusterStyles, maxZoom: 19});
                markerCluster.onClick = function (clickedClusterIcon, sameLatitude, sameLongitude) {
                    return multiChoice(sameLatitude, sameLongitude, json);
                };

                // Dynamic loading markers and data from JSON ----------------------------------------------------------------------

                google.maps.event.addListener(map, 'idle', function () {
                    var visibleArray = [];
                    for (var i = 0; i < json.data.length; i++) {
                        if (map.getBounds().contains(newMarkers[i].getPosition())) {
                            visibleArray.push(newMarkers[i]);
                            $.each(visibleArray, function (i) {
                                setTimeout(function () {
                                    if (map.getBounds().contains(visibleArray[i].getPosition())) {
                                        if (!visibleArray[i].content.className) {
                                            visibleArray[i].setMap(map);
                                            visibleArray[i].content.className += 'bounce-animation marker-loaded';
                                            markerCluster.repaint();
                                        }
                                    }
                                }, i * 50);
                            });
                        } else {
                            newMarkers[i].content.className = '';
                            newMarkers[i].setMap(null);
                        }
                    }

                    var visibleItemsArray = [];
                    $.each(json.data, function (a) {
                        if (map.getBounds().contains(new google.maps.LatLng(json.data[a].latitude, json.data[a].longitude))) {
                            var category = json.data[a].category;
                            pushItemsToArray(json, a, category, visibleItemsArray);
                        }
                    });

                    // Create list of items in Results sidebar ---------------------------------------------------------------------

                    $('.items-list .results').html(visibleItemsArray);

                    if( $('.items-list').length > 0 ){
                        $(".items-list").mCustomScrollbar({
                            mouseWheel:{ scrollAmount: 350 }
                        });
                    }

                    // Check if images are cached, so will not be loaded again

                    $.each(json.data, function (a) {
                        if (map.getBounds().contains(new google.maps.LatLng(json.data[a].latitude, json.data[a].longitude))) {
                            is_cached(json.data[a].gallery[0], a);
                        }
                    });

                    // Call Rating function ----------------------------------------------------------------------------------------

                    //rating('.results .item');

                    //var $singleItem = $('.results .item');
                    //$singleItem.hover(
                    //        function () {
                    //            newMarkers[$(this).attr('id') - 1].content.className = 'marker-active marker-loaded';
                    //        },
                    //        function () {
                    //            newMarkers[$(this).attr('id') - 1].content.className = 'marker-loaded';
                    //        }
                    //);
                });

                redrawMap('google', map);

                function is_cached(src, a) {
                    var image = new Image();
                    var loadedImage = $('.results li #' + json.data[a].id + ' .image');
                    image.src = src;
                    if (image.complete) {
                        $(".results").each(function () {
                            loadedImage.removeClass('loading');
                            loadedImage.addClass('loaded');
                        });
                    } else {
                        $(".results").each(function () {
                            $('.results li #' + json.data[a].id + ' .image').addClass('loading');
                        });
                        $(image).load(function () {
                            loadedImage.removeClass('loading');
                            loadedImage.addClass('loaded');
                        });
                    }
                }

                // Geolocation of user -----------------------------------------------------------------------------------------

                $('.geolocation').on("click", function () {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(success);
                    } else {
                        console.log('Geo Location is not supported');
                    }
                });

                function success(position) {
                    var locationCenter = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    if(vm.latitude && vm.longitude){
                        locationCenter = new google.maps.LatLng(vm.latitude,vm.longitude);
                    }
                    map.setCenter(locationCenter);
                    map.setZoom(14);

                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        "latLng": locationCenter
                    }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var lat = results[0].geometry.location.lat(),
                                lng = results[0].geometry.location.lng(),
                                placeName = results[0].address_components[0].long_name,
                                latlng = new google.maps.LatLng(lat, lng);

                            $("#location").val(results[0].formatted_address);
                        }
                    });

                }
            }
        }

        // Push items to array and create <li> element in Results sidebar ------------------------------------------------------

        function pushItemsToArray(json, a, category, visibleItemsArray) {
            var itemPrice;

            visibleItemsArray.push(
                '<li>' +
                '<div class="item" id="' + json.data[a].id + '">' +
                '<a href="#/venue/'+json.data[a].facilityId +'" class="image">' +
                '<div class="inner">' +
                '<div class="item-specific">' +
                drawItemSpecific(category, json, a) +
                '</div>' +
                '<img src="' + json.data[a].gallery[0] + '" alt="">' +
                '</div>' +
                '</a>' +
                '<div class="wrapper">' +
                '<a href="#/venue/'+json.data[a].facilityId +'" id="' + json.data[a].facilityId + '"><h3>' + json.data[a].title + '</h3></a>' +
                '<figure>' + json.data[a].location + '</figure>' +
                drawPrice(json.data[a].peak_hour_price) +
                ' <span class="type">' + json.data[a].category + '</span>' +

                '<div class="info">' +
                '<span class="type">' + json.data[a].type + '</span>' +
                ' <span class="type">' + $filter('filter')(vm.areas,{id:json.data[a].area_id})[0].name + '</span>' +
                '</div>' +
                '<div class="rating" data-rating="' + json.data[a].rating + '"></div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</li>'
            );

            function drawPrice(price) {
                if (price) {
                    itemPrice = '<div class="price"><i class="fa fa-inr"></i> ' + price + '</div>';
                    return itemPrice;
                } else {
                    return '';
                }
            }

        }
        // Redraw map after item list is closed --------------------------------------------------------------------------------

        function redrawMap(mapProvider, map) {
            $('.map .toggle-navigation').click(function () {
                $('.map-canvas').toggleClass('results-collapsed');
                $('.map-canvas .map').one("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function () {
                    if (mapProvider == 'osm') {
                        map.invalidateSize();
                    } else if (mapProvider == 'google') {
                        google.maps.event.trigger(map, 'resize');
                    }
                });
            });
        }

// Create modal if more items are on the same location (example: one building with floors) -----------------------------

        var multipleItems = [];
        function multiChoice(sameLatitude, sameLongitude, json) {
            //if (clickedCluster.getMarkers().length > 1){
            multipleItems = [];
            $.each(json.data, function (a) {
                if (parseFloat(json.data[a].latitude).toFixed(6) == sameLatitude && parseFloat(json.data[a].longitude).toFixed(6) == sameLongitude) {
                    pushItemsToArray(json, a, json.data[a].category, multipleItems);
                }
            });


            $('body').append('<div class="modal-window multichoice fade_in"></div>');
            $('.modal-window').load('assets/external/_modal-multichoice.html', function () {
                $('.modal-window .modal-wrapper .items').html(multipleItems);
                rating('.modal-window');
            });

            //}
        }


// Rating --------------------------------------------------------------------------------------------------------------

        function rating(element) {
            var ratingElement =
                    '<span class="stars">' +
                    '<i class="fa fa-star s1" data-score="1"></i>' +
                    '<i class="fa fa-star s2" data-score="2"></i>' +
                    '<i class="fa fa-star s3" data-score="3"></i>' +
                    '<i class="fa fa-star s4" data-score="4"></i>' +
                    '<i class="fa fa-star s5" data-score="5"></i>' +
                    '</span>'
                ;
            if (!element) {
                element = '';
            }
            $.each($(element + ' .rating'), function (i) {
                $(this).append(ratingElement);
                if ($(this).hasClass('active')) {
                    $(this).append('<input readonly hidden="" name="score_' + $(this).attr('data-name') + '" id="score_' + $(this).attr('data-name') + '">');
                }
                var rating = $(this).attr('data-rating');
                for (var e = 0; e < rating; e++) {
                    var rate = e + 1;
                    $(this).children('.stars').children('.s' + rate).addClass('active');
                }
            });

            var ratingActive = $('.rating.active i');
            ratingActive.on('hover', function () {
                    for (var i = 0; i < $(this).attr('data-score'); i++) {
                        var a = i + 1;
                        $(this).parent().children('.s' + a).addClass('hover');
                    }
                },
                function () {
                    for (var i = 0; i < $(this).attr('data-score'); i++) {
                        var a = i + 1;
                        $(this).parent().children('.s' + a).removeClass('hover');
                    }
                });
            ratingActive.on('click', function () {
                $(this).parent().parent().children('input').val($(this).attr('data-score'));
                $(this).parent().children('.fa').removeClass('active');
                for (var i = 0; i < $(this).attr('data-score'); i++) {
                    var a = i + 1;
                    $(this).parent().children('.s' + a).addClass('active');
                }
                return false;
            });
        }
        // Specific data for each item -----------------------------------------------------------------------------------------

        function drawItemSpecific(category, json, a) {
            var itemSpecific = '';
            if (category) {
                if (category == 'real_estate') {
                    if (json.data[a].item_specific) {
                        if (json.data[a].item_specific.bedrooms) {
                            itemSpecific += '<span title="Bedrooms"><img src="assets/img/bedrooms.png">' + json.data[a].item_specific.bedrooms + '</span>';
                        }
                        if (json.data[a].item_specific.bathrooms) {
                            itemSpecific += '<span title="Bathrooms"><img src="assets/img/bathrooms.png">' + json.data[a].item_specific.bathrooms + '</span>';
                        }
                        if (json.data[a].item_specific.area) {
                            itemSpecific += '<span title="Area"><img src="assets/img/area.png">' + json.data[a].item_specific.area + '<sup>2</sup></span>';
                        }
                        if (json.data[a].item_specific.garages) {
                            itemSpecific += '<span title="Garages"><img src="assets/img/garages.png">' + json.data[a].item_specific.garages + '</span>';
                        }
                        return itemSpecific;
                    }
                } else if (category == 'bar_restaurant') {
                    if (json.data[a].item_specific) {
                        if (json.data[a].item_specific.menu) {
                            itemSpecific += '<span>Menu from: ' + json.data[a].item_specific.menu + '</span>';
                        }
                        return itemSpecific;
                    }
                    return itemSpecific;
                }
            } else {
                return '';
            }
            return '';
        }

        function drawInfobox(category, infoboxContent, json, i) {

            if (json.data[i].color) {
                var color = json.data[i].color
            } else {
                color = ''
            }
            if (json.data[i].price) {
                var price = '<div class="price">' + json.data[i].price + '</div>'
            } else {
                price = ''
            }
            if (json.data[i].id) {
                var id = json.data[i].id;
                var url = "#/venue/" + json.data[i].facilityId
            } else {
                id = '';
                url = ""
            }
            //if(json.data[i].url)            { var url = json.data[i].id }
            //    else                        { url = '' }
            if (json.data[i].category) {
                var type = json.data[i].category
            } else {
                type = ''
            }
            if (json.data[i].title) {
                var title = json.data[i].title
            } else {
                title = ''
            }
            if (json.data[i].location) {
                var location = json.data[i].location
            } else {
                location = ''
            }
            if (json.data[i].gallery.length) {
                var gallery = json.data[i].gallery[0]
            } else {
                gallery = '/assets/img/default-item.jpg'
            }

            var ibContent = '';
            ibContent =
                '<div class="infobox ' + color + '">' +
                '<div class="inner">' +
                '<div class="image">' +
                '<div class="item-specific">' + drawItemSpecific(category, json, i) + '</div>' +
                '<div class="overlay">' +
                '<div class="wrapper">' +
                    //'<a href="#" class="quick-view" data-toggle="modal" data-target="#modal" id="' + id + '">Quick View</a>' +
                    //'<hr>' +
                '<a href="' + url + '" class="detail">Go to Detail</a>' +
                '</div>' +
                '</div>' +
                '<a href="' + url + '" class="description">' +
                '<div class="meta">' +
                price +
                '<h2>' + title + '</h2>' +
                '<figure>' + location + '</figure>' +
                '<i class="fa fa-angle-right"></i>' +
                '</div>' +
                '</a>' +
                '<img src="' + gallery + '">' +
                '</div>' +
                '</div>' +
                '</div>';

            return ibContent;
        }
    });
