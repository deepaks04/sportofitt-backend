'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:VendorinfoCtrl
 * @description
 * # VendorinfoCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
  .controller('VendorInfoCtrl', function ($stateParams,$state,searchService,toastr,$scope,bookingService) {


      var vm = this;
      vm.vendorId = $stateParams.vendorId;
      var draggableMarker = false;

      var mapStyles = [ {"featureType":"road","elementType":"labels","stylers":[{"visibility":"simplified"},{"lightness":20}]},{"featureType":"administrative.land_parcel","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"all","stylers":[{"saturation":-100},{"visibility":"on"},{"lightness":10}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":50}]},{"featureType":"water","elementType":"all","stylers":[{"hue":"#a1cdfc"},{"saturation":30},{"lightness":49}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"hue":"#f49935"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"hue":"#fad959"}]}, {featureType:'road.highway',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:60},{visibility:'on'}]}, {featureType:'landscape.natural',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-71},{lightness:-18},{visibility:'on'}]},  {featureType:'poi',elementType:'all',stylers:[{hue:'#d9d5cd'},{saturation:-70},{lightness:20},{visibility:'on'}]} ];

      vm.init = function(){
          searchService.getVendorById(vm.vendorId).then(function(response){
             vm.vendor = response.data.data.vendor;
              simpleMap(vm.vendor.latitude, vm.vendor.longitude, draggableMarker);
              }).catch(function(response){
              toastr.error(response);
          })
      }

      vm.days = {1:'Monday',2:'Thusday',3:'Wednesday',4:'Thursday',5:'Friday',6:'Saturday',7:'Sunday'};


      function simpleMap(_latitude, _longitude, draggableMarker){
          var mapCenter = new google.maps.LatLng(_latitude, _longitude);
          var mapOptions = {
              zoom: 14,
              center: mapCenter,
              disableDefaultUI: true,
              scrollwheel: false,
              styles: mapStyles,
              panControl: false,
              zoomControl: false,
              draggable: true
          };
          var mapElement = document.getElementById('map-simple');
          var map = new google.maps.Map(mapElement, mapOptions);

          // Google map marker content -----------------------------------------------------------------------------------

          var markerContent = document.createElement('DIV');
          markerContent.innerHTML =
              '<div class="map-marker">' +
              '<div class="icon"></div>' +
              '</div>';

          // Create marker on the map ------------------------------------------------------------------------------------

          var marker = new RichMarker({
              //position: mapCenter,
              position: new google.maps.LatLng( _latitude, _longitude ),
              map: map,
              draggable: draggableMarker,
              content: markerContent,
              flat: true
          });

          marker.content.className = 'marker-loaded';
      }

      vm.init();
  });
