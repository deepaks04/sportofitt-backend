'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:OrderconfirmationCtrl
 * @description
 * # OrderconfirmationCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
    .controller('OrderConfirmationCtrl', function (bookingService, $scope, $state, $rootScope, toastr) {
        var vm = this;
        var LocalBookings;
        vm.LocalBookings = {};
        vm.init = function () {

            vm.LocalBookings = LocalBookings = bookingService.getLocalBookings();
            if (vm.LocalBookings) {
                if (vm.LocalBookings.package_type_id == 0) {
                    vm.LocalBookings.booking_amount = (vm.LocalBookings.is_peak) ? vm.LocalBookings.peak_hour_price : vm.LocalBookings.off_peak_hour_price
                    vm.LocalBookings.discount = 0;
                    vm.LocalBookings.discounted_amount = 0;
                } else {
                    vm.LocalBookings.booking_amount = vm.LocalBookings.actual_price;
                    vm.LocalBookings.discount = vm.LocalBookings.discount;
                    vm.LocalBookings.discounted_amount = (vm.LocalBookings.actual_price * vm.LocalBookings.discount / 100);
                }
            }

            vm.user = angular.copy($rootScope.user);

            //    vm.LocalBookings = [];
            //angular.forEach(LocalBookings.keys(),function(value){
            //    var booking = LocalBookings.get(value);
            //   booking.discount_amount = (booking.actual_price*booking.discount/100);
            //    booking.discounted_amount = booking.actual_price-booking.discount_amount;
            //    vm.LocalBookings.push(booking);
            //});

            //calculateTotal();

        }
        function calculateTotal() {
            vm.LocalBookings.Total = 0;
            vm.LocalBookings.Total_Discount = 0;
            angular.forEach(vm.LocalBookings, function (booking, key) {
                vm.LocalBookings.Total = vm.LocalBookings.Total + booking.discounted_price;
                vm.LocalBookings.Total_Discount = vm.LocalBookings.Total_Discount + booking.discount_amt;
            });
        }

        vm.init();

        vm.checkout = function () {
            var booking = {
                booking_data: [{
                    "is_peak": vm.LocalBookings.is_peak,
                    "selectedDate": vm.LocalBookings.date,
                    "name": vm.LocalBookings.name,
                    "description": vm.LocalBookings.description,
                    "booking_amount": vm.LocalBookings.booking_amount,
                    "discount": vm.LocalBookings.discount,
                    "discounted_amount": vm.LocalBookings.discounted_amount,
                    "selectedSlot": vm.LocalBookings.selectedSlot,
                    "facilityId": vm.LocalBookings.id,
                    "package_type_id": vm.LocalBookings.package_type_id,
                    "qty": vm.LocalBookings.qty,

                }],
                "payment_mode": vm.LocalBookings.payment_mode,
                "order_total": vm.LocalBookings.booking_amount,

            };
            bookingService.checkout(booking).then(function (response) {
                toastr.success("Order placed successfully!");
                $state.go('app.mybookings');

            })
        }
    });
