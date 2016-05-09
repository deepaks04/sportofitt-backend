'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:AuthCtrl
 * @description
 * # AuthCtrl
 * Controller of the sportofittApp
 */
(function (module) {

    var authCtrl = function AuthController(Auth, $auth, $rootScope, $state, toastr) {

        var vm = this;

        vm.isMainLogin = false;
        vm.login = function () {
            //var loginOptions = {
            //    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            //};
            //var credentials = $httpParamSerializerJQLike({
            //    email: vm.email,
            //    password: vm.password
            //});
            //
            //// Use Satellizer's $auth service to login
            //$auth.login(credentials, loginOptions).then(function (response) {
            //    //set token
            //    $auth.setToken(response.data.data.access_token);
            //
            //    Auth.getAuthenticatedUser().then(function(user){
            //        $rootScope.user = user.data;
            //        // If login is successful, redirect to the users state
            //        //toastr.success(errors.data.message.success);
            //        if (vm.isMainLogin) {
            //            $state.reload();
            //        } else {
            //            $state.go('app.home', {});
            //        }
            //        //
            //        $rootScope.isAuthenticated = $auth.isAuthenticated();
            //
            //    });
            //
            //}).catch(function (errors) {
            //    toastr.error(errors.data.message.error);
            //});
            Auth.login(vm.email,vm.password).then(function(){
                // If login is successful, redirect to the users state
                //toastr.success(errors.data.message.success);

                Auth.getAuthenticatedUser().then(function(user){
                      $rootScope.user = user.data;
                      $rootScope.isAuthenticated = $auth.isAuthenticated();

                    if (vm.isMainLogin) {
                        $state.reload();
                    } else {
                        $state.go('app.home', {});
                    }
                  });

            }).catch(function (errors) {
                    toastr.error(errors);
                });
        }

        //if($auth.isAuthenticated()){
        //    $state.go('app.home', {});
        //}

    }

    module.controller("AuthCtrl", authCtrl);

}(angular.module("sportofittApp")));