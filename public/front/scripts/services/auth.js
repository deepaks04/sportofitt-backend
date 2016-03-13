'use strict';

/**
 * @ngdoc service
 * @name sportofittApp.Auth
 * @description
 * # Auth
 * Service in the sportofittApp.
 */
angular.module('sportofittApp')
  .service('Auth', function ($http,myConfig) {
    // AngularJS will instantiate a singleton by calling "new" on this function

    this.url = myConfig.backend + "user/";

    this.register = function(newUser){
      return $http.post(this.url+'sign-up',newUser);
    }
  });
