'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:ConfirmationCtrl
 * @description
 * # ConfirmationCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
  .controller('ConfirmationCtrl', function ($state,$stateParams,toastr,Auth) {
   //   toastr.info($stateParams)
      Auth.confirmUser($stateParams.token).then(function(response){
          toastr.success(response.message.success);
          $state.go('login',{});
      }).catch(function(response){
          toastr.error(response.message.error);
          $state.go('login',{});
      })
  });
