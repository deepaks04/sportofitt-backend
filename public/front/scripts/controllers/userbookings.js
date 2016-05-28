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
          if(!booking.bookingDetails[0].is_editable){
              SweetAlert.swal("Can not cancel!", "Order passed date!", "error");
              return
          }

          SweetAlert.swal({
                title: "Are you sure?",
                text: "Are you sure to cancel your booking?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, cancel booking!",
                cancelButtonText: "No!",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function (isConfirm) {
                if (isConfirm) {
                    var options = {order_id:booking.oid};
                    bookingService.cancelBooking(options).then(function(response){
                        booking.bookingDetails[0].is_editable = false;
                        booking.booking_status = 3;
                        SweetAlert.swal("Order cancelled", response.data.message.success,'success');

                    }).catch(function(response){
                        SweetAlert.swal("Order not cancelled", response.data.message.error,'error');
                    });
                } else {
                    SweetAlert.swal("Not cancelled", "Order is safe :)", "error");
                }
            });
      };


  });
