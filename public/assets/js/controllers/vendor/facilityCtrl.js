'use strict';
/**
  * controllers used for the facility
*/
app.controller('facilityAddCtrl', ["$scope","$state","$log","facilityService","SweetAlert", function ($scope,$state,$log,facilityService,SweetAlert) {

    facilityService.getRootCategory()
        .then(getRootCategorySuccess);

        $scope.selectedCategory = {"":"Select"};
    $scope.slots = {1:1,2:2,3:3,4:4};
    $scope.types = {0:"Peak Time",1:"Off time"};

        $scope.master = $scope.facility = {};
    // Time Picker
    $scope.today = function () {
        var dt = new Date();
        dt.setHours( 0 );
        dt.setMinutes( 15 );
        $scope.facility.duration = dt;
    };
    $scope.today();

    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.hstep = 1;
    $scope.mstep = 15;



    $scope.changed = function () {
        var hours = $scope.facility.duration.getHours();

        var mins = $scope.facility.duration.getMinutes();
        if(hours > 3){
            alert('max time 3 hours');
            var dt = new Date();
            dt.setHours( 3 );
            dt.setMinutes( 0 );
            $scope.facility.duration = dt;

        }
        if(hours <= 0 && mins < 15){
            alert('min time 15 minutes');
            $scope.today();
        }
        $log.log('Time changed to: ' + hours + mins);
    };

    $scope.clear = function () {
        $scope.facility.duration = null;
    };
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
      SweetAlert.swal(response.message, "success");
      $state.go("vendor.facility.list");

      console.log(response);
      });
      addFacility.catch(function(data,status){
      console.log($scope.Form['name']);
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
app.controller('facilityListCtrl', ["$scope", "$filter", "$modal","$log","ngTableParams","facilityService", function ($scope, $filter,$modal,
                                                                                                                      $log,ngTableParams,facilityService) {
    
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

    $scope.open = function (size,facility) {

        var modalInstance = $modal.open({
            templateUrl: 'sessionPackageModal.html',
            controller: 'SessionModalInstanceCtrl',
            size: size,
            resolve: {
                selectedFacility: function () {
                    return facility;
                }
            }
        });


        modalInstance.result.then(function (selectedItem) {
            $scope.selected = selectedItem;
        }, function () {
            $log.info('Modal dismissed at: ' + new Date());
        });
    };
}]);



app.controller('SessionModalInstanceCtrl', ["$scope", "$modalInstance", "selectedFacility", function ($scope, $modalInstance, selectedFacility) {

    $scope.selected = selectedFacility;

    $scope.master = $scope.facility = {};


    $scope.types = {0:"Peak Time",1:"Off time"};

    $scope.discounts = {10:10,20:20,30:30};

    $scope.months = ["1 Month","3 Months","6 Months"];
    // Time Picker
    $scope.today = function () {
        var dt = new Date();
        dt.setHours( 0 );
        dt.setMinutes( 15 );
        $scope.facility.start = dt;

        $scope.facility.end = dt;
    };
    $scope.today();

    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.hstep = 1;
    $scope.mstep = 15;


$scope.ismeridian=true;
    $scope.changed = function () {
        $log.log('Time changed to: ' + $scope.facility.start);
        $log.log('Time changed to: ' + $scope.facility.end);
    };

    $scope.clear = function () {
        $scope.facility.start = null;
        $scope.facility.end = null;

    };
    console.log(selectedFacility);
    $scope.ok = function () {
        $modalInstance.close($scope.selected.item);
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
}]);
app.controller('facilitySessionCtrl',["$scope","$modalInstance"],function($scope,$modalInstance){
    $scope.items = items;
    $scope.selected = {
        item: $scope.items[0]
    };

    $scope.ok = function () {
        $modalInstance.close($scope.selected.item);
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});

app.controller('facilityBookingCtrl', ["$scope","$state", "$aside", "moment","facilityService","SweetAlert", function ($scope,$state, $aside, moment,facilityService,SweetAlert) {
    $scope.facilityId = $state.params.facilityId;



    $scope.facilityData = {};
    facilityService.getAllFacilities()
        .then(getAllFacilitySuccess);

    function getAllFacilitySuccess(facilityData) {
        $scope.facilityData = facilityData.facility;
        console.log($scope.facilityData);
    }

    facilityService.getFacilityById($scope.facilityId)
        .then(getFacilitySuccess);

    function getFacilitySuccess(facilityData) {
        $scope.facility = facilityData.facility;
        console.log($scope.facility);
    }

    var vm = this;
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    $scope.events = [
        {
            title: 'Birthday Party',
            type: 'home',
            startsAt: new Date(y, m, 5, 19, 0),
            endsAt: new Date(y, m, 5, 22, 30)
        },
        {
            title: 'AngularJS Seminar',
            type: 'off-site-work',
            startsAt: new Date(y, m, 8, 10, 30),
            endsAt: new Date(y, m, 9, 18, 30)
        },
        {
            title: 'Event 1',
            type: 'job',
            startsAt: new Date(y, m, d - 5),
            endsAt: new Date(y, m, d - 2)
        },
        {
            title: 'Event 2',
            type: 'cancelled',
            startsAt: new Date(y, m, d - 3, 16, 0),
            endsAt: new Date(y, m, d - 3, 18, 0)
        },
        {
            title: 'This is a really long event title',
            type: 'to-do',
            startsAt: new Date(y, m, d + 1, 19, 0),
            endsAt: new Date(y, m, d + 1, 22, 30)
        },
    ];

    $scope.calendarView = 'week';
    $scope.calendarTitle = 'Name';
    $scope.calendarDay = new Date();

    function showModal(action, event) {
        var modalInstance = $aside.open({
            templateUrl: 'calendarEvent.html',
            placement: 'right',
            size: 'sm',
            backdrop: true,
            controller: function ($scope, $modalInstance) {
                $scope.$modalInstance = $modalInstance;
                $scope.action = action;
                $scope.event = event;
                $scope.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };
                $scope.deleteEvent = function () {
                    $modalInstance.close($scope.event, $scope.event);
                };

            }
        });
        modalInstance.result.then(function (selectedEvent, action) {

            $scope.eventDeleted(selectedEvent);

        });
    }


    $scope.eventClicked = function (event) {
        showModal('Clicked', event);
    };
    $scope.addEvent = function () {
        $scope.events.push({
            title: "New",
            startsAt: new Date(y, m, d, 10, 0),
            endsAt: new Date(y, m, d, 11, 0),
            type: 'to-do'
        });
        $scope.eventEdited($scope.events[$scope.events.length - 1]);
    };

    $scope.eventEdited = function (event) {
        showModal('Edited', event);
    };

    $scope.eventDeleted = function (event) {

        SweetAlert.swal({
            title: "Are you sure?",
            text: "Your will not be able to recover this event!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel plx!",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                $scope.events.splice(event.$id, 1);
                SweetAlert.swal("Deleted!", "Event has been deleted.", "success");
            } else {
                SweetAlert.swal("Cancelled", "Event is safe :)", "error");
            }
        });
    };


    $scope.toggle = function ($event, field, event) {
        $event.preventDefault();
        $event.stopPropagation();

        event[field] = !event[field];
    };

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