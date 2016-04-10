'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:OrderconfirmationCtrl
 * @description
 * # OrderconfirmationCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
  .controller('OrderConfirmationCtrl', function (bookingService,$scope) {
    var vm =this;
    var LocalBookings;
      vm.init = function(){
          LocalBookings = bookingService.getLocalBookings();
            vm.LocalBookings = [];
          angular.forEach(LocalBookings.keys(),function(value){
              var booking = LocalBookings.get(value);
             booking.discount_amt = (booking.actual_price*booking.discount/100);
              booking.discounted_price = booking.actual_price-booking.discount_amt;
              vm.LocalBookings.push(booking);
          });

          calculateTotal();

      }
function calculateTotal(){
              vm.LocalBookings.Total = 0;
              vm.LocalBookings.Total_Discount = 0;
              angular.forEach(vm.LocalBookings,function(booking,key){
                  vm.LocalBookings.Total= vm.LocalBookings.Total + booking.discounted_price;
                  vm.LocalBookings.Total_Discount =vm.LocalBookings.Total_Discount + booking.discount_amt;
              });
      }
      vm.init();
  });
