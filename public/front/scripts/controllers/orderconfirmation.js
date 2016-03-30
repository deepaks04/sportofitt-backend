'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:OrderconfirmationCtrl
 * @description
 * # OrderconfirmationCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
  .controller('OrderConfirmationCtrl', function (bookingService) {
    var vm =this;

      vm.init = function(){
          vm.LocalBookings = bookingService.getLocalBookings();
      }

      vm.init();
  });
