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
          toastr.success(response.data.message.success);
          $state.go('app.login',{});
      }).catch(function(response){
          toastr.error(response.data.message.error);
          $state.go('app.login',{});
      })
  });
