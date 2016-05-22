'use strict';

/**
 * @ngdoc function
 * @name publicApp.controller:UserbookingsCtrl
 * @description
 * # UserbookingsCtrl
 * Controller of the publicApp
 */
angular.module('sportofittApp')
  .controller('UserbookingsCtrl', function (bookingService,$scope,$rootScope) {
        var vm = this;

        vm.init = function(){
        bookingService.getUserBookings().then(function(response){
            vm.myBookings = response.data.data;
        });

        };

      vm.init();


  });
