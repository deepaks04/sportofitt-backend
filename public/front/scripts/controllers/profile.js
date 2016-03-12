'use strict';

/**
 * @ngdoc function
 * @name clipTwoApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the clipTwoApp
 */
angular.module('sportofittApp')
  .controller('ProfileCtrl', function (userService,toastr) {
    var vm = this;

    vm.userInfo = {};

    vm.cities = [{
        id : 1,
        name : 'Pune'
    }];
      vm.disableSubmit =false;

      userService.getAreas().then(function(areas){
          vm.areas = areas.data.area;
      });

      userService.getProfile().then(function(user){
         vm.userInfo = user.data.data;
          vm.userInfo.city_id = 1;
      });

      vm.saveUserProfile = function(userInfo){
          vm.disableSubmit = true;
          var saveProfile = userService.updateProfile(userInfo);
          saveProfile.success(function (response) {
              toastr.success(response.message.success);
              vm.disableSubmit =false;

          });
          saveProfile.error(function (data, status) {
              vm.disableSubmit = false;
              vm.errors = {};
              angular.forEach(data, function (errors, field) {

                  vm.errors[field] = errors.join(', ');
              });
          });
      }

      vm.changePassward = function(userPasswordInfo){
          vm.disableSubmit = true;
          vm.errors = {};
          var saveProfile = userService.updatePassword(userPasswordInfo);
          saveProfile.success(function (response) {
              toastr.success(response.message.success);
              vm.userPassword = {};
              vm.disableSubmit =false;

          });
          saveProfile.error(function (data, status) {
              vm.disableSubmit = false;

            if(status == 422) {
                vm.errors = {};
                angular.forEach(data, function (errors, field) {

                    vm.errors[field] = errors.join(', ');
                });
            }else{
                vm.errors['current_password'] = data.message.error;
                toastr.error(data.message.error);
            }
          });
      }

  });
