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

      this.getVendorById = function(vendorId){
          return $http.get(this.url + "vendor/show/" + vendorId);
      }
      this.getFacilityById = function(facilityId){
          return $http.get(this.url + "facility/show/" + facilityId);
      }

      this.getFacilityAvailableSlotsById = function(filter){
          filter.date = new Date(filter.date).getTime();
          return $http.post(this.url + "facility/available-slots" , filter);
      }
      this.getCategories = function(){
          return $http.get(myConfig.backend + "user/get-sub-category");
      }

      this.getArea = function(){
          return $http.get(myConfig.backend + "user/areas");
      }
  });
