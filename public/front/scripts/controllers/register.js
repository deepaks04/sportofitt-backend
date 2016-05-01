'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:RegisterCtrl
 * @description
 * # RegisterCtrl
 * Controller of the sportofittApp
 */

(function(module) {

    var registerCtrl = function RegisterController($auth, $state,Auth,toastr) {

        var vm = this;
        vm.user = {};
        vm.errors = {};
        vm.disableSubmit =false;
        vm.form = {
            register: function (form) {
                vm.disableSubmit = true;
                var auth = Auth.register(form);
                auth.success(function (response) {
                    toastr.success(response.message.success);
                    $state.go("app.login");
                });
                auth.error(function (data, status) {
                    vm.disableSubmit = false;
                    vm.errors = {};
                    angular.forEach(data, function (errors, field) {

                        vm.errors[field] = errors.join(', ');
                    });
                });
            }
        };
    }

    module.controller("RegisterCtrl", registerCtrl);

}(angular.module("sportofittApp")));