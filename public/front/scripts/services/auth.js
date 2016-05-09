'use strict';

/**
 * @ngdoc service
 * @name sportofittApp.Auth
 * @description
 * # Auth
 * Service in the sportofittApp.
 */
angular.module('sportofittApp')
  .service('Auth', function ($rootScope,$auth,$cookies,$http,myConfig,$httpParamSerializerJQLike) {
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
              return user.data;
          });
      }

      this.login = function(email,password){
          var loginOptions = {
              headers: {'Content-Type': 'application/x-www-form-urlencoded'}
          };
          var credentials = $httpParamSerializerJQLike({
              email: email,
              password: password
          });

          // Use Satellizer's $auth service to login
          return $auth.login(credentials, loginOptions).then(function (response) {
              //set token
              $auth.setToken(response.data.data.access_token);


          }).catch(function (errors) {
              console.log(errors);
                  return errors.data.message.error;
              });;
      }
  });
