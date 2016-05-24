'use strict';

/**
 * @ngdoc function
 * @name publicApp.controller:UserbookingsCtrl
 * @description
 * # UserbookingsCtrl
 * Controller of the publicApp
 */
angular.module('sportofittApp')
  .controller('UserbookingsCtrl', function (SweetAlert,bookingService,$scope,$rootScope) {
        var vm = this;

        vm.init = function(){
        bookingService.getUserBookings().then(function(response){
            vm.myBookings = response.data.data;
        });

        };

      vm.init();
      
      vm.cancelBooking = function(booking){
          SweetAlert.swal({
                title: "Are you sure?",
                text: "Are you sure to cancel your order?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No!",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function (isConfirm) {
                if (isConfirm) {
                   
                    SweetAlert.swal("Order cancelled", "Thank you for purchase at Sportofitt.\n\
     The total amount of Rs."+booking.booking_amount+" will be refunded and credited to your account in 7-10 business days");
                } else {
                    SweetAlert.swal("Not cancelled", "Order is safe :)", "error");
                }
            });
      };


  });
