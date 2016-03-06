'use strict';

/**
 * @ngdoc service
 * @name sportofittApp.Auth
 * @description
 * # Auth
 * Service in the sportofittApp.
 */
angular.module('sportofittApp')
  .service('Auth', function ($http) {
    // AngularJS will instantiate a singleton by calling "new" on this function

    this.url = "../api/v1/user/";

    this.register = function(newUser){
      return $http.post(this.url+'sign-up',newUser,{
        headers : {
          'api-key' : 'djxOEhxWw2QbFYkhBC7Gtrtwzsl1WVDK'
        }
      });
    }
  });
