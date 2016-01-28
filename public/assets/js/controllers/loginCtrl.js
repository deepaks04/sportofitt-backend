'use strict';
/**
 * controllers used for the Login
 */

app.controller('signCtrl', [
    "$scope",
    "$state",
    "$timeout",
    "$rootScope",
    "SweetAlert",
    "Login",
    "SessionService",
    function ($scope, $state, $timeout, $rootScope, SweetAlert, Login,
            SessionService) {
        $scope.master = $scope.user;
        $scope.form = {
            submit: function (form) {
                var firstError = null;
                if (form.$invalid) {

                    var field = null, firstError = null;
                    for (field in form) {
                        if (firstError === null && !form[field].$valid) {
                            firstError = form[field].$name;
                        }

                        if (form[field].$pristine) {
                            form[field].$dirty = true;
                        }
                    }

                    angular.element('.ng-invalid[name=' + firstError + ']')
                            .focus();
                    // SweetAlert.swal("The form cannot be submitted because
                    // it contains validation errors!", "Errors are marked
                    // with a red, dashed border!", "error");


                } else {
                    var auth = Login.auth($scope.user);
                    auth.success(function (response) {
                        console.log(response);
                        $state.go(response.user.role + '.dashboard', {'name': response.user.extra.business_name});
                    });
                    auth.error(function (data, status) {
                        console.log(data);
                        SweetAlert.swal("Sign in unsuccessfull",
                                data.message, "error");
                        // return;
                    });
                }
            }
        };
    }]);

app.controller('registrationCtrl', [
    "$scope",
    "$state",
    "$timeout",
    "SweetAlert",
    "Login",
    function ($scope, $state, $timeout, SweetAlert, Login) {
        $scope.master = $scope.myModel;
        $scope.errors = {};

        $scope.form = {
            register: function (form) {
                $scope.disableSubmit = true;
                var auth = Login.register(form);
                console.log(form);
                auth.success(function (response) {
                    SweetAlert.swal("Good job!", response.message,
                            "success");
                    $state.go("login.signin");
                    console.log(response);
                });
                auth.error(function (data, status) {
                    $scope.disableSubmit = false;
                    $scope.errors = {};
                    angular.forEach(data, function (errors, field) {

                        $scope.errors[field] = errors.join(', ');
                    });
                });
            }
        };
    }]);

app.controller('forgetPasswordCtrl', [
    "$scope",
    "$state",
    "$timeout",
    "SweetAlert",
    "Login",
    function ($scope, $state, $timeout, SweetAlert, Login) {
        $scope.master = $scope.myModel;

        $scope.form = {
            forgotPassword: function (form) {
                $scope.disableSubmit = true;
                $scope.errors = {};
                var passwordResponse = Login.forgetPassword(form);
                console.log(form);
                passwordResponse.then(function (response) {
                    SweetAlert.swal("Password reset done!", response.data.message,
                            "success");
                    $state.go("login.signin");
                    console.log(response);
                });
                passwordResponse.catch(function (response, status) {

                    $scope.disableSubmit = false;
                    $scope.errors = {};
                    if (response.status === 422) {
                        angular.forEach(response.data, function (errors, field) {

                            $scope.errors[field] = errors.join(', ');
                        });
                    } else {
                        $scope.errors['email'] = response.data.message;
                    }
                });
            }
        };
    }]);


app.controller('resetPasswordCtrl', [
    "$scope",
    "$state",
    "$timeout",
    "SweetAlert",
    "Login",
    function ($scope, $state, $timeout, SweetAlert, Login) {
        //$scope.master = $scope.myModel = {
        //    'token' : $state.params.token
        //};

        Login.getResetPassword($state.params.token).then(function (response) {
            $scope.myModel = response.data.data;
            //console.log($scope.myModel.data);
        }).catch(function (response) {
            $scope.myModel = {};
        });
        $scope.errors = {};

        $scope.form = {
            resetPassword: function (form) {
                $scope.disableSubmit = true;
                $scope.errors = {};
                var passwordResponse = Login.resetPassword(form);
                //console.log(form);
                passwordResponse.then(function (response) {
                    SweetAlert.swal("Password change done!", response.data.message,
                            "success");
                    $state.go("login.signin");
                    console.log(response.data);
                });
                passwordResponse.catch(function (response, status) {
                    //console.log(data);
                    $scope.disableSubmit = false;
                    $scope.errors = {};
                    if (response.status === 422) {
                        angular.forEach(response.data, function (errors, field) {

                            $scope.errors[field] = errors.join(', ');
                        });
                    } else {
                        $scope.errors['email'] = response.data.message;
                    }
                });
            }
        };
    }]);


