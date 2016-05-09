'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:OrderconfirmationCtrl
 * @description
 * # OrderconfirmationCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
  .controller('OrderConfirmationCtrl', function (bookingService,$scope,$rootScope) {
    var vm =this;
    var LocalBookings;
      vm.LocalBookings = {};
      vm.init = function(){

          vm.LocalBookings = LocalBookings = bookingService.getLocalBookings();
         if(vm.LocalBookings)
         {
             if(vm.LocalBookings.package_type_id == 0) {
                 vm.LocalBookings.booking_amount = (vm.LocalBookings.is_peak)?vm.LocalBookings.peak_hour_price:vm.LocalBookings.off_peak_hour_price
                 vm.LocalBookings.discount = 0;
                 vm.LocalBookings.discounted_amount = 0;
             }else {
                 vm.LocalBookings.booking_amount = vm.LocalBookings.actual_price;
                 vm.LocalBookings.discount = vm.LocalBookings.discount;
                 vm.LocalBookings.discounted_amount = (vm.LocalBookings.actual_price*vm.LocalBookings.discount/100);
             }
         }

          vm.user = angular.copy($rootScope.user);

          console.log(vm.user);

          //    vm.LocalBookings = [];
          //angular.forEach(LocalBookings.keys(),function(value){
          //    var booking = LocalBookings.get(value);
          //   booking.discount_amount = (booking.actual_price*booking.discount/100);
          //    booking.discounted_amount = booking.actual_price-booking.discount_amount;
          //    vm.LocalBookings.push(booking);
          //});

          //calculateTotal();

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
