'use strict';

/**
 * @ngdoc service
 * @name sportofittApp.bookingService
 * @description
 * # bookingService
 * Service in the sportofittApp.
 */
angular.module('sportofittApp')
  .service('bookingService', function () {
    // AngularJS will instantiate a singleton by calling "new" on this function
    this.localBooking = [];

    this.getLocalBookings = function(){
      return this.localBooking;
    }

    this.saveLocalBooking = function(booking){
        if(!this.checkAlreadyInCart(booking)) {
            booking.qty = 1;
            this.localBooking.push(booking);
        }

        console.log(this.localBooking);
    }

      this.checkAlreadyInCart = function(booking){
          var alreadyHas = false;
          angular.forEach(this.localBooking,function(value,key){
             if (value.id == booking.id){
                 value.qty++;
                 alreadyHas = true;
             }
          });

          return alreadyHas;
      }
  });
