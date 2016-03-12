'use strict';

/**
 * @ngdoc service
 * @name sportofittApp.user
 * @description
 * # user
 * Service in the sportofittApp.
 */
angular.module('sportofittApp')
  .service('userService', function ($http) {
    // AngularJS will instantiate a singleton by calling "new" on this function

    this.getProfile = function(){
      return $http.get('/api/v1/user/dashboard');
    }

    this.getAreas = function() {
      return $http({
        method: 'GET',
        url: '/api/v1/user/areas',
        cache: true
      });
    }

      this.updateProfile = function(userProfile){
          return $http.post('/api/v1/user/update-profile',userProfile);
      }

      this.updatePassword = function(userPassword){
          return $http.post('/api/v1/user/change-password',userPassword);
      }
  });
