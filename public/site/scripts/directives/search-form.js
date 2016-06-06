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
        controller:'SearchCtrl',
        controllerAs :'vm'
    };
  }).controller('SearchCtrl',function($state){
    var vm = this;

});
