'use strict';
/**
  * controllers used for the facility
*/
app.controller('facilityAddCtrl', ["$scope","$state","facilityService","SweetAlert", function ($scope,$state,facilityService,SweetAlert) {

    facilityService.getRootCategory()
        .then(getRootCategorySuccess);

        $scope.subCategory = "";

        $scope.master = $scope.facility;
        $scope.form = {

            submit: function (form) {
                var firstError = null;
                if (form.$invalid) {

                    var field = null, firstError = null;
                    for (field in form) {
                        if (field[0] != '$') {
                            if (firstError === null && !form[field].$valid) {
                                firstError = form[field].$name;
                            }

                            if (form[field].$pristine) {
                                form[field].$dirty = true;
                            }
                        }
                    }

                    angular.element('.ng-invalid[name=' + firstError + ']').focus();
                    SweetAlert.swal("The form cannot be submitted because it contains validation errors!", "Errors are marked with a red, dashed border!", "error");
                    return;

                } else {

                  var addFacility =facilityService.addFacility($scope.facility);
      addFacility.then(function(response){
      SweetAlert.swal(response.data.message, "success");
      $state.go("vendor.facility.list");

      console.log(response);
      });
      addFacility.catch(function(data,status){
      console.log(data);

      SweetAlert.swal(data.data.message,data.data.statusText, "error");
      return;
      })

                }

            },
            reset: function (form) {

                $scope.facility = angular.copy($scope.master);
                form.$setPristine(true);

            }
        };

    function getRootCategorySuccess(categoryData) {
        $scope.categoryData = categoryData.category;
    }
}]);

//List of facility
app.controller('facilityListCtrl', ["$scope", "$filter", "ngTableParams","facilityService", function ($scope, $filter, ngTableParams,facilityService) {
    
    $scope.facilityData = {};
    facilityService.getAllFacilities()
        .then(getFacilitySuccess);

function getFacilitySuccess(facilityData) {
        $scope.facilityData = facilityData.facility;
    }

    $scope.tableParams = new ngTableParams({
        page: 1, // show first page
        count: 5, // count per page
        sorting: {
            name: 'asc' // initial sorting
        }
    }, {
        total: $scope.facilityData.length, // length of data
        getData: function ($defer, params) {
            // use build-in angular filter
            var orderedData = params.sorting() ? $filter('orderBy')($scope.facilityData, params.orderBy()) : $scope.facilityData;
            $defer.resolve(orderedData);
        }
    });
}]);

app.controller('facilityEditCtrl', ["$scope","$state","facilityService","SweetAlert", function ($scope,$state,facilityService,SweetAlert) {

    facilityService.getRootCategory()
        .then(getRootCategorySuccess);

facilityService.getFacilityById($state.params.facilityId)
        .then(getFacilitySuccess);
        $scope.subCategory = "";
        $scope.selectedCategory = {};

        $scope.master = $scope.facility;
        $scope.form = {

            submit: function (form) {
                var firstError = null;
                if (form.$invalid) {

                    var field = null, firstError = null;
                    for (field in form) {
                        if (field[0] != '$') {
                            if (firstError === null && !form[field].$valid) {
                                firstError = form[field].$name;
                            }

                            if (form[field].$pristine) {
                                form[field].$dirty = true;
                            }
                        }
                    }

                    angular.element('.ng-invalid[name=' + firstError + ']').focus();
                    SweetAlert.swal("The form cannot be submitted because it contains validation errors!", "Errors are marked with a red, dashed border!", "error");
                    return;

                } else {

                  var addFacility =facilityService.addFacility($scope.facility);
      addFacility.then(function(response){
      SweetAlert.swal(response.data.message, "success");
      $state.go("vendor.facility.list");

      console.log(response);
      });
      addFacility.catch(function(data,status){
      console.log(data);

      SweetAlert.swal(data.data.message,data.data.statusText, "error");
      return;
      })

                }

            },
            reset: function (form) {

                $scope.facility = angular.copy($scope.master);
                form.$setPristine(true);

            }
        };

    function getRootCategorySuccess(categoryData) {
        $scope.categoryData = categoryData.category;
    }

    function getFacilitySuccess(facilityData) {
        $scope.facility = facilityData.facility;
    }
}]);