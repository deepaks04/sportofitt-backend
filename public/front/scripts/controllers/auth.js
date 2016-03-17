'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:AuthCtrl
 * @description
 * # AuthCtrl
 * Controller of the sportofittApp
 */
(function(module) {

    var authCtrl = function AuthController($auth, $state,$httpParamSerializerJQLike,toastr) {

        var vm = this;

        vm.login = function() {
            var loginOptions = {
                headers: { 'Content-Type': 'application/x-www-form-urlencoded'}
            };
            var credentials = $httpParamSerializerJQLike({
                email: vm.email,
                password: vm.password
            });

            // Use Satellizer's $auth service to login
            $auth.login(credentials,loginOptions).then(function(response) {
                //set token
                $auth.setToken(response.data.data.access_token);
                // If login is successful, redirect to the users state
                //toastr.success(errors.data.message.success);

                $state.go('app.home', {});
            }).catch(function(errors){
                toastr.error(errors.data.message.error);
            });
        }

        if($auth.isAuthenticated()){
            $state.go('app.home', {});
        }

    }

    module.controller("AuthCtrl", authCtrl);

}(angular.module("sportofittApp")));