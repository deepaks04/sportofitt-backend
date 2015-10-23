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
    function($scope, $state, $timeout, $rootScope, SweetAlert, Login,
        SessionService) {

      $scope.master = $scope.user;
      $scope.form = {

        submit : function(form) {
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
            return;

          } else {

            var auth = Login.auth($scope.user);
            auth.success(function(response) {
              $state.go(response.user.role + '.dashboard')
            });
            auth.error(function(data, status) {
              console.log(data);
              SweetAlert.swal("Sign in unsuccessfull",
                  data.message, "error");
              // return;
            })

          }

        }
      };

    } ]);

app.controller('registrationCtrl', [
    "$scope",
    "$state",
    "$timeout",
    "SweetAlert",
    "Login",
    function($scope, $state, $timeout, SweetAlert, Login) {
      $scope.master = $scope.myModel;
      $scope.errors = {};
      $scope.form = {

        register : function(form) {
          var auth = Login.register(form);
          console.log(form);
          auth.success(function(response) {
            SweetAlert.swal("Good job!", response.message,
                "success");
            $state.go("login.signin");
            console.log(response);
          });
          auth.error(function(data, status) {
            $scope.errors = {};
            angular.forEach(data, function(errors, field) {

              $scope.errors[field] = errors.join(', ');
            });
          })
        }
      };
    } ]);
