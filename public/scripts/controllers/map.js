'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:MapCtrl
 * @description
 * # MapCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
        .controller('MapCtrl', function (searchService, toastr, $filter, $stateParams) {

            var vm = this;
            vm.filter = $stateParams;
            vm.types = ['Venue', 'Coaching'];
            vm.visibleItemsArray = [];

            vm.search = function() {
                searchService.search().then(function (response) {
                    vm.searchData = response.data.data;
                        vm.setFilters(vm.filter);
                }).catch(function (errors) {
                    toastr.error(errors);
                });
            };

            vm.search();
            vm.setFilters = function (newfilter) {
                var filter = angular.copy(newfilter);
                filter.area_id= newfilter.area.id;
                angular.forEach(filter, function (value, key) {
                    if (!value || key == 'area') {
                        delete filter[key];
                    }
                });
                vm.visibleItemsArray = $filter('filter')(vm.searchData, filter, true);
            }
    });
