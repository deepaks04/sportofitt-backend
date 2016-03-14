'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:MapCtrl
 * @description
 * # MapCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
  .controller('MapCtrl', function (searchService,toastr) {
      var _latitude = 51.541216;
      var _longitude = -0.095678;
//    var jsonPath = 'assets/json/items.json.txt';


      // Load JSON data and create Google Maps

      searchService.search().then(function(response){
          createHomepageGoogleMap(_latitude,_longitude,response.data);
      }).catch(function(errors){
          toastr.error(errors);
      })


      function createHomepageGoogleMap(_latitude,_longitude,json) {
          $.get("assets/external/_infobox.js", function () {
              gMap();
          });
          function gMap() {
              if (navigator.geolocation) {
                  navigator.geolocation.getCurrentPosition(success);
              } else {
                  console.log('Geo Location is not supported');
              }
              var mapCenter = new google.maps.LatLng(_latitude, _longitude);
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
                      position: google.maps.ControlPosition.RIGHT_TOP
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

                  if (json.data[i].color) var color = json.data[i].color;
                  else color = '';

                  var markerContent = document.createElement('DIV');
                  if (json.data[i].featured == 1) {
                      markerContent.innerHTML =
                          '<div class="map-marker featured' + color + '">' +
                          '<div class="icon">' +
                          '<img src="' + json.data[i].type_icon + '">' +
                          '</div>' +
                          '</div>';
                  }
                  else {
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
                      }
                      else {
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

                  // Check if images are cached, so will not be loaded again

                  $.each(json.data, function (a) {
                      if (map.getBounds().contains(new google.maps.LatLng(json.data[a].latitude, json.data[a].longitude))) {
                          is_cached(json.data[a].gallery[0], a);
                      }
                  });

                  // Call Rating function ----------------------------------------------------------------------------------------

                  //rating('.results .item');

                  var $singleItem = $('.results .item');
                  $singleItem.hover(
                      function () {
                          newMarkers[$(this).attr('id') - 1].content.className = 'marker-active marker-loaded';
                      },
                      function () {
                          newMarkers[$(this).attr('id') - 1].content.className = 'marker-loaded';
                      }
                  );
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
                  }
                  else {
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

              //// Autocomplete address ----------------------------------------------------------------------------------------
              //
              //var input = $('#location');
              //var autocomplete = new google.maps.places.Autocomplete(input, {
              //    types: ["geocode"]
              //});
              //autocomplete.bindTo('bounds', map);
              //google.maps.event.addListener(autocomplete, 'place_changed', function () {
              //    var place = autocomplete.getPlace();
              //    if (!place.geometry) {
              //        return;
              //    }
              //    if (place.geometry.viewport) {
              //        map.fitBounds(place.geometry.viewport);
              //        map.setZoom(14);
              //    } else {
              //        map.setCenter(place.geometry.location);
              //        map.setZoom(14);
              //    }
              //
              //    //marker.setPosition(place.geometry.location);
              //    //marker.setVisible(true);
              //
              //    var address = '';
              //    if (place.address_components) {
              //        address = [
              //            (place.address_components[0] && place.address_components[0].short_name || ''),
              //            (place.address_components[1] && place.address_components[1].short_name || ''),
              //            (place.address_components[2] && place.address_components[2].short_name || '')
              //        ].join(' ');
              //    }
              //});
          }
      }
  });
