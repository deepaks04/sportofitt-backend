'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:MapCtrl
 * @description
 * # MapCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
        .controller('HomeCtrl', function (searchService, toastr, $filter, $state) {
            var vm = this;
            vm.categoryFilter = "";
            searchService.getCategories().then(function (response) {
                vm.subCategories = response.data.category;
            }).catch(function (errors) {
                toastr.error(errors);
            });
            
            vm.setCategory = function () {
                $state.go('app.listings',{category:vm.categoryFilter.name});
            };
            
        })
        .filter('propsFilter', function() {
  return function(items, props) {
    var out = [];

    if (angular.isArray(items)) {
      var keys = Object.keys(props);
        
      items.forEach(function(item) {
        var itemMatches = false;

        for (var i = 0; i < keys.length; i++) {
          var prop = keys[i];
          var text = props[prop].toLowerCase();
          if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
            itemMatches = true;
            break;
          }
        }

        if (itemMatches) {
          out.push(item);
        }
      });
    } else {
      // Let the output be the input untouched
      out = items;
    }

    return out;
  };
});;