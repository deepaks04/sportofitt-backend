'use strict';

/**
 * @ngdoc directive
 * @name publicApp.directive:searchForm
 * @description
 * # searchForm
 */
angular.module('sportofittApp')
    .directive('searchForm', function () {
        return {
            templateUrl: 'front/views/layouts/search-form.html',
            restrict: 'E',
            scope:{},
            controller : 'searchFormCtrl',
            controllerAs : 'vm'
        };
    }).controller('searchFormCtrl',function($state,searchService, toastr,$stateParams){

    var vm = this;
    vm.types = ['Venue', 'Coaching'];

    vm.filter = $stateParams || {
        category : "",
        type : "",
        area:""
    };


    // Load JSON data and create Google Maps

    searchService.getCategories().then(function (response) {
        vm.subCategories = response.data.category;
    }).catch(function (errors) {
        toastr.error(errors);
    });

    searchService.getArea().then(function (response) {
        vm.areas = response.data.area;
        //vm.filter.area = vm.areas[0];
    }).catch(function (errors) {
        toastr.error(errors);
    });

    vm.getData = function(formData){
        $state.go("app.listings",{category:formData.category,area:formData.area,type:formData.type});
       };
});
