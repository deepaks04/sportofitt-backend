'use strict';

/**
 * @ngdoc service
 * @name sportofittApp.user
 * @description
 * # user
 * Service in the sportofittApp.
 */
angular.module('sportofittApp')
  .service('userService', function ($http,myConfig) {
    // AngularJS will instantiate a singleton by calling "new" on this function

    this.getProfile = function(){
      return $http.get(myConfig.backend + 'user/dashboard');
    }

    this.getAreas = function() {
      return $http({
        method: 'GET',
        url: myConfig.backend+'user/areas',
        cache: true
      });
    }

      this.updateProfile = function(userProfile){
          return $http.post(myConfig.backend+'user/update-profile',userProfile);
      }

      this.updatePassword = function(userPassword){
          return $http.post(myConfig.backend+'user/change-password',userPassword);
      }
  });
