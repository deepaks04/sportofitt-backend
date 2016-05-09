'use strict';

/**
 * @ngdoc service
 * @name sportofittApp.Auth
 * @description
 * # Auth
 * Service in the sportofittApp.
 */
angular.module('sportofittApp')
  .service('Auth', function ($rootScope,$cookies,$http,myConfig) {
    // AngularJS will instantiate a singleton by calling "new" on this function

    this.url = myConfig.backend + "user/";

    this.register = function(newUser){
      return $http.post(this.url+'sign-up',newUser);
    }
      this.confirmUser = function(token){
          return $http.get(this.url+'confirmation/'+token);
      }
      this.getAuthenticatedUser = function(){
          return $http.post(this.url+"authenticated-user").then(function(user){
              delete user.data.data.access_token;
              $cookies.putObject('loggedUser',user.data.data);
              $rootScope.user = $cookies.getObject("loggedUser");
              return user.data;
          });
      }
  });
