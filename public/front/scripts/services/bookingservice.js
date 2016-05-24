'use strict';

/**
 * @ngdoc service
 * @name sportofittApp.bookingService
 * @description
 * # bookingService
 * Service in the sportofittApp.
 */
angular.module('sportofittApp')
    .service('bookingService', function (localStorageService, $http, myConfig) {
        // AngularJS will instantiate a singleton by calling "new" on this function

        this.getLocalBookings = function () {
            return localStorageService.get("booking");
        }

        this.saveLocalBooking = function (booking) {
            if (!this.checkAlreadyInCart(booking)) {
                booking.qty = 1;
                localStorageService.set("booking", booking);
            }
        };

        this.checkAlreadyInCart = function (booking) {
            var alreadyHas = false;
            angular.forEach(localStorageService.keys(), function (value, key) {
                var getItem = localStorageService.get(value);
                if (getItem == booking) {
                    getItem.qty++;
                    alreadyHas = true;
                }
            });

            return alreadyHas;
        };

        this.checkout = function (facilityDetails) {
            return $http.post(myConfig.backend + 'facility/book', facilityDetails).then(function () {
                localStorageService.set("booking", null);
            });
        };

        this.getUserBookings = function () {
            return $http.get(myConfig.backend + 'user/mybookings');
        };
    });
