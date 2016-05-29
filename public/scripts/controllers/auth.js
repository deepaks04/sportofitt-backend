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

        $rootScope.user = {};

        vm.disableSubmit = false;

        if($state.params.token){
            Auth.getResetPassword($state.params.token).then(function (response) {
                vm.resetModel = response.data.data;
                //console.log($scope.myModel.data);
            }).catch(function (response) {
                vm.resetModel = {};
            });
        }

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

            vm.disableSubmit = true;
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
                    toastr.error(errors.data.message.error);
                vm.disableSubmit = false;
                });
        }

        //if($auth.isAuthenticated()){
        //    $state.go('app.home', {});
        //}
        vm.requestNewPassword = function(form) {
            vm.disableSubmit = true;
            vm.errors = {};
            var passwordResponse = Auth.forgetPassword(form);
            passwordResponse.then(function (response) {
               toastr.success(response.data.message);
                $state.go("app.login");
            });
            passwordResponse.catch(function (response, status) {

                vm.disableSubmit = false;
                vm.errors = {};
                if (response.status === 422) {
                    angular.forEach(response.data, function (errors, field) {
                        vm.errors[field] = errors.join(', ');
                    });
                } else {
                    vm.errors['email'] = response.data.message;
                }
            });
        }

        vm.resetNewPassword = function (form) {
            $scope.disableSubmit = true;
            vm.errors = {};
            var passwordResponse = Auth.resetPassword(form);
            //console.log(form);
            passwordResponse.then(function (response) {
                toastr.success(response.data.message);
                $state.go("app.login");
            });
            passwordResponse.catch(function (response, status) {
                //console.log(data);
                $scope.disableSubmit = false;
                vm.errors = {};
                if (response.status === 422) {
                    angular.forEach(response.data, function (errors, field) {

                        vm.errors[field] = errors.join(', ');
                    });
                } else {
                    vm.errors['email'] = response.data.message;
                }
            });
        }
    }

    module.controller("AuthCtrl", authCtrl);

}(angular.module("sportofittApp")));