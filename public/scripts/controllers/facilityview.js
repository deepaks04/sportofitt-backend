'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:FacilityviewCtrl
 * @description
 * # FacilityviewCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
    .controller('FacilityViewCtrl', function ($stateParams, $state, searchService, toastr, $scope, bookingService, $filter) {
        var vm = this;
        vm.facilityId = $stateParams.facilityId;

        vm.filter = {slot: 'peak'};

        vm.days = {1: 'Monday', 2: 'Thuesday', 3: 'Wednesday', 4: 'Thursday', 5: 'Friday', 6: 'Saturday', 7: 'Sunday'};
        var draggableMarker = false;

        var mapStyles = [{"featureType": "road", "elementType": "labels", "stylers": [{"visibility": "simplified"}, {"lightness": 20}]}, {"featureType": "administrative.land_parcel", "elementType": "all", "stylers": [{"visibility": "off"}]}, {"featureType": "landscape.man_made", "elementType": "all", "stylers": [{"visibility": "on"}]}, {"featureType": "transit", "elementType": "all", "stylers": [{"saturation": -100}, {"visibility": "on"}, {"lightness": 10}]}, {"featureType": "road.local", "elementType": "all", "stylers": [{"visibility": "on"}]}, {"featureType": "road.local", "elementType": "all", "stylers": [{"visibility": "on"}]}, {"featureType": "road.highway", "elementType": "labels", "stylers": [{"visibility": "simplified"}]}, {"featureType": "poi", "elementType": "labels", "stylers": [{"visibility": "off"}]}, {"featureType": "road.arterial", "elementType": "labels", "stylers": [{"visibility": "on"}, {"lightness": 50}]}, {"featureType": "water", "elementType": "all", "stylers": [{"hue": "#a1cdfc"}, {"saturation": 30}, {"lightness": 49}]}, {"featureType": "road.highway", "elementType": "geometry", "stylers": [{"hue": "#f49935"}]}, {"featureType": "road.arterial", "elementType": "geometry", "stylers": [{"hue": "#fad959"}]}, {featureType: 'road.highway', elementType: 'all', stylers: [{hue: '#dddbd7'}, {saturation: -92}, {lightness: 60}, {visibility: 'on'}]}, {featureType: 'landscape.natural', elementType: 'all', stylers: [{hue: '#c8c6c3'}, {saturation: -71}, {lightness: -18}, {visibility: 'on'}]}, {featureType: 'poi', elementType: 'all', stylers: [{hue: '#d9d5cd'}, {saturation: -70}, {lightness: 20}, {visibility: 'on'}]}];

        vm.init = function () {
            searchService.getFacilityById(vm.facilityId).then(function (response) {
                vm.facility = response.data.data;
                angular.forEach(vm.facility.packages, function(value, key){
                    value.discounted_amount = value.actual_price - (value.actual_price*value.discount/100);
                });
                if(vm.facility) {
                    itemDetailMap(vm.facility.vendor.longitude, vm.facility.vendor.latitude);
                }

                vm.filter = angular.copy(vm.facility);
                vm.filter.is_peak = true,
                    vm.filter.date= $filter('date')(new Date(), 'EEEE, MMMM d, yyyy');

                vm.getAvailableSlots();
            }).catch(function (response) {
                toastr.error(response);
            });

            vm.today();

            vm.dateOptions = {
                //dateDisabled: disabled,
                formatYear: 'yy',
                maxDate: new Date(2020, 5, 22),
                minDate: new Date(),
                startingDay: 1
            };

            vm.minDate = new Date();

        }


        vm.getAvailableSlots = function () {
            var options = {
                facility_id : vm.filter.id,
                is_peak : vm.filter.is_peak,
                date : vm.filter.date
            }
            searchService.getFacilityAvailableSlotsById(options).then(function (response) {
                vm.facilitySlots = response.data.data;
                vm.filterAvailableSlot();
            }).catch(function (response) {
                toastr.error(response);
            })
        }

        vm.datepickerOpened = false;

        vm.openDatePicker = function () {
            vm.datepickerOpened = !vm.datepickerOpened;
        }

        vm.setSlotFilter = function (slot) {
            vm.filter.is_peak = slot;
            //vm.filterAvailableSlot();
            vm.resetSlots();
        }

        vm.resetSlots = function(){
            vm.facilitySlots = [];
        }

        vm.filterAvailableSlot = function () {
            var day = new Date(vm.filter.date).getDay();
            vm.selectedDay = (day != 0) ? day : 7;
            vm.filter.selectedSlot = vm.facilitySlots[0];

        }

        vm.today = function () {
            vm.filter.date = $filter('date')(new Date(), 'EEEE, MMMM d, yyyy');
        };

        function getDayClass(data) {
            var date = data.date,
                mode = data.mode;
            if (mode === 'day') {
                var dayToCheck = new Date(date).setHours(0, 0, 0, 0);

                for (var i = 0; i < $scope.events.length; i++) {
                    var currentDay = new Date($scope.events[i].date).setHours(0, 0, 0, 0);

                    if (dayToCheck === currentDay) {
                        return $scope.events[i].status;
                    }
                }
            }

            return '';
        }

        function itemDetailMap( _longitude, _latitude){
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
            var icon = '<img src="icon">';


            // Google map marker content -----------------------------------------------------------------------------------

            var markerContent = document.createElement('DIV');
            markerContent.innerHTML =
                '<div class="map-marker">' +
                '<div class="icon">' +
                icon +
                '</div>' +
                '</div>';

            // Create marker on the map ------------------------------------------------------------------------------------

            var marker = new RichMarker({
                position: mapCenter,
                map: map,
                draggable: false,
                content: markerContent,
                flat: true
            });

            marker.content.className = 'marker-loaded';
        }

        vm.addLocalBooking = function (booking, singleSession) {
            var newBooking = angular.copy(booking);
              if(singleSession) {
                  newBooking.package_type_id = 0;
                      //newBooking.slotforView = $filter('filter')( vm.facilitySlots,newBooking.selectedSlot)[0];
              }else{
                  newBooking.package_id = booking.id,
                      newBooking.id = vm.facility.id,
                  newBooking.vendor = vm.facility.vendor;
                  newBooking.date = new Date();
              }
            bookingService.saveLocalBooking(newBooking);
            $state.go('app.orderconformation', {});
        }

        vm.init();
    });
