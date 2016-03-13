'use strict';

/**
 * @ngdoc service
 * @name sportofittApp.searchService
 * @description
 * # searchService
 * Service in the sportofittApp.
 */
angular.module('sportofittApp')
  .service('searchService', function ($http,myConfig) {
    // AngularJS will instantiate a singleton by calling "new" on this function
    this.url = myConfig.backend + 'index/';


    this.search = function(){
      return $http.get(this.url + "search");
    };

  });
