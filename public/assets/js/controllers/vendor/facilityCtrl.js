'use strict';
/**
 * controllers used for the facility
 */
app.controller('facilityAddCtrl', ["$scope", "$state", "$log", "facilityService", "SweetAlert", function ($scope, $state, $log, facilityService, SweetAlert) {

        facilityService.getRootCategory()
                .then(getRootCategorySuccess);

        facilityService.getDuration()
                .then(getDurationSuccess);

        $scope.types = {0: "Peak Time", 1: "Off time"};

        $scope.master = $scope.facility = {};
        $scope.form = {
            submit: function (form) {
                var firstError = null;

                var addFacility = facilityService.addFacility($scope.facility);

                angular.forEach($scope.facility, function (value, field) {
                    $scope.Form[field].$dirty = false;
                });
                addFacility.then(function (response) {
                    SweetAlert.swal(response.message, "success");
                    $state.go("vendor.facility.list");

                });
                addFacility.catch(function (data, status) {
                    angular.forEach(data.data, function (errors, field) {
                        $scope.Form[field].$dirty = true;
                        $scope.Form[field].$error = errors;
                    });
                    SweetAlert.swal(data.data.message, data.data.statusText, "error");
                    return false;
                })

            },
            reset: function (form) {

                $scope.facility = angular.copy($scope.master);
                form.$setPristine(true);

            }
        };

        function getRootCategorySuccess(categoryData) {
            $scope.categoryData = categoryData.category;
        }

        function getDurationSuccess(durations) {
            $scope.durations = durations.duration;
        }
    }]);

//List of facility
app.controller('facilityListCtrl', ["$scope", "$filter", "$modal", "$log", "ngTableParams", "facilityService", function ($scope, $filter, $modal,
            $log, ngTableParams, facilityService) {

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

        $scope.blockUnblockFacility = function (facilityId, status) {

            var data = {'is_active': status ? 1 : 0}

            var statusBlocking = facilityService.blockUnblockFacility(facilityId, data);

            statusBlocking.then(function (response) {
                alert(response);
            });

            statusBlocking.catch(function (response) {
                alert(response);
            });
        }

        $scope.open = function (size, facility, tabActive) {

            var modalInstance = $modal.open({
                templateUrl: 'sessionPackageModal.html',
                controller: 'SessionModalInstanceCtrl',
                size: size,
                resolve: {
                    selectedFacility: function () {
                        return facility;
                    },
                    tab: function () {
                        return tabActive;
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



app.controller('SessionModalInstanceCtrl', ["$scope", "$modalInstance", "$filter", "selectedFacility", "tab", "facilityService",
    function ($scope, $modalInstance, $filter, selectedFacility, tab, facilityService) {

        $scope.facility = selectedFacility;
        $scope.tab = tab;
        facilityService.getDuration()
                .then(getDurationSuccess);

        function getDurationSuccess(durations) {
            $scope.durations = durations.duration;
        }

        facilityService.getDays()
                .then(getDaysSuccess);

        function getDaysSuccess(days) {
            $scope.days = days.data;
        }
        $scope.openingHours = [];
        $scope.sessions = [];
        $scope.packages = [];

        $scope.slots = {1: 1, 2: 2, 3: 3, 4: 4, 999: 999};
        $scope.types = {0: "Peak Time", 1: "Off time"};

        $scope.percentageArray = {
            10: 10,
            20: 20,
            30: 30,
            40: 40,
            50: 50,
            60: 60,
            70: 70,
            80: 80,
            90: 90,
            100: 100
        };

        $scope.months = ["1 Month", "3 Months", "6 Months"];

        $scope.showStatus = function () {
            var selected = [];
            angular.forEach($scope.statuses, function (s) {
                if ($scope.session.status.indexOf(s.value) >= 0) {
                    selected.push(s.text);
                }
            });
            return selected.length ? selected.join(', ') : 'Not set';
        };

        $scope.showDiscount = function (session) {
            var selected = [];
            if (session.discount) {
                selected = $filter('filter')($scope.discounts, {value: session.discount});
            }
            return selected.length ? selected[0].text : 'Not set';
        };


        $scope.ok = function () {
            $modalInstance.close($scope.selected.item);
        };

        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };

        $scope.getSessions = function () {
            $scope.tab = 'sessions';
            if ($scope.sessions.length) {
                return $scope.sessions;
            }
            ;

            facilityService.getSessionsByFacilityId($scope.facility.id).then(function (sessions) {
                var sessions = (sessions.data === "") ? [] : sessions.data;
                parseSessions(sessions);
            });
        };

        function parseSessions(sessions) {
            angular.forEach(sessions, function (session, keys) {
                session.peak = parseInt(session.peak);
                session.off_peak = parseInt(session.off_peak);
                this.push(session);
            }, $scope.sessions);
        }

        $scope.getOpeningHours = function () {
            $scope.tab = 'opening_hours';
            
            if ($scope.openingHours.length) {
                return $scope.openingHours;
            }

            facilityService.getOpeningTimesByFacilityId($scope.facility.id)
                    .then(function (openingHours) {
                        var openingHours = (openingHours.data === "") ? [] : openingHours.data;
                        parseOpeningHours(openingHours);
                    });
        };

        function parseOpeningHours(openingHours) {
            angular.forEach(openingHours, function (openingHour, keys) {

                openingHour.day = parseInt(openingHour.day);

                openingHour.is_peak = parseInt(openingHour.is_peak);

                openingHour.start = getHoursToDate(openingHour.start);

                openingHour.end = getHoursToDate(openingHour.end);

                this.push(openingHour);
            }, $scope.openingHours);

        }

        function getHoursToDate(openingHours) {
            var timeArray = openingHours.split(":");

            var dt = new Date();

            dt.setHours(timeArray[0]);

            dt.setMinutes(timeArray[1]);

            dt.setSeconds(timeArray[2]);

            return dt;
        }

        $scope.getPackages = function () {
            $scope.tab = 'packages';
            if ($scope.packages.length) {
                console.log($scope.packages);
                return $scope.packages;
            }
            ;

            facilityService.getPackagesByFacilityId($scope.facility.id)
                    .then(function (packages) {
                        var packages = (packages.data === "") ? [] : packages.data;
                        parsePackages(packages);
                    }).catch(function (data) {
                console.log(data.data);
            });
        };

        function parsePackages(packages) {
            angular.forEach(packages, function (pack, keys) {
                pack.is_peak = parseInt(pack.is_peak);
                pack.month = parseInt(pack.month);
                this.push(pack);
            }, $scope.packages);
        }

        $scope.saveHours = function (data, id,rowform) {
            // $scope.user not updated yet

            angular.extend(data, {id: id, available_facility_id: $scope.facility.id});

            var saveHours = facilityService.saveOpeningTime(data).then(function (responce) {
                console.log(responce.data);
            }).catch(function (responce) {
                rowform.$invalid= true;
                console.log(responce.data);
            });
        };
        
        $scope.setTab = function(tab){
          $scope.tab = tab;  
        };

        $scope.showDay = function (time) {

            var selected = [];
            if (time.day) {
                selected = $filter('filter')($scope.days, {id: time.day});
            }
            return selected.length ? selected[0].name : 'Not set';
        }

        $scope.saveSession = function (data, id, rowform) {
            // $scope.user not updated yet
            angular.extend(data, {id: id, available_facility_id: $scope.facility.id});
            var saveSession = facilityService.saveSession(data).then(function (data) {
                console.log(data);
            }).catch(function (response) {
                angular.forEach(response.data, function (errorData, field) {
                    data.$setError(field, errorData);
                });
            });
            return saveSession;
        };

        $scope.savePackage = function (data, id) {
            // $scope.user not updated yet
            angular.extend(data, {id: id, available_facility_id: $scope.facility.id});

            console.log(data);
            var savePackage = facilityService.savePackage(data);

            savePackage.then(function (data) {
//                $scope.showSessionEdit = false;
                console.log(data);
            });
            savePackage.catch(function (data, status) {
                return data.data;
            });
        };

        // remove Opening Hours
        $scope.removeOpeningHours = function (index) {
            $scope.openingHours.splice(index, 1);
        };

        // remove Session
        $scope.removeSession = function (index) {
            $scope.sessions.splice(index, 1);
        };

        // remove Package
        $scope.removePackage = function (index) {
            $scope.packages.splice(index, 1);
        };

        // add Opening Hours
        $scope.addHours = function () {
            var dt = new Date();
            $scope.inserted = {
                id: '',
                day: 0,
                start: dt,
                end: dt,
                is_peak: 1
            };
            $scope.openingHours.push($scope.inserted);
        };

        // add Sessions
        $scope.addSession = function () {
            $scope.sessionInserted = {
                id: '',
//				name:"",
                peak: 1,
                off_peak: 1,
                price: "",
                discount: 0
            };
            $scope.sessions.push($scope.sessionInserted);
            $scope.showSessionEdit = true;
        };

        // add Sessions
        $scope.addPackage = function () {
            $scope.packageInserted = {
                id: '',
                name: "",
                month: 1,
                actual_price: "",
                discount: 0,
                description: ""
            };
            $scope.packages.push($scope.packageInserted);
        };


        // edit facility

        $scope.form = {
            submit: function (form) {
                var firstError = null;

                var addFacility = facilityService.updateFacility($scope.facility);
                addFacility.then(function (response) {
                    SweetAlert.swal(response.message, "success");
                    $state.go("vendor.facility.list");

                    console.log(response);
                });
                addFacility.catch(function (data, status) {
                    angular.forEach(data.data, function (errors, field) {
                        $scope.Form[field].$dirty = true;
                        $scope.Form[field].$error = errors;
                        console.log($scope.Form[field]);
                    });

                    SweetAlert.swal(data.data.message, data.data.statusText, "error");
                    return false;
                })

            },
            reset: function (form) {

                $scope.facility = angular.copy($scope.master);
                form.$setPristine(true);

            }
        };

        function getRootCategorySuccess(categoryData) {
            $scope.categoryData = categoryData.category;
        }

        function getDurationSuccess(durations) {
            $scope.durations = durations.duration;
        }

        if ($scope.tab === 'opening_hours') {
            $scope.getOpeningHours();
        } else if ($scope.tab === 'sessions') {
            $scope.getSessions();
        } else if ($scope.tab === 'packages') {
            $scope.getPackages();
        }
        ;
    }]);
app.controller('facilitySessionCtrl', ["$scope", "$modalInstance"], function ($scope, $modalInstance) {
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

app.controller('facilityBookingCtrl', ["$scope", "$state", "$aside", "moment", "facilityService", "SweetAlert", function ($scope, $state, $aside, moment, facilityService, SweetAlert) {
        $scope.facilityId = $state.params.facilityId;


        $scope.facilityData = {};
        facilityService.getAllFacilities()
                .then(getAllFacilitySuccess);

        function getAllFacilitySuccess(facilityData) {
            $scope.facilityData = facilityData.facility;
        }
        function getFacilitySuccess(facilityData) {
            $scope.facility = facilityData.facility;
        }

        var vm = this;
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        var startDate = y + "-" + (m + 1);

        $scope.events = [];

        if ($scope.facilityId) {
            facilityService.getFacilityById($scope.facilityId)
                    .then(getFacilitySuccess);
            getBlockedSessionByFacilityId(startDate);
        } else {
            getBlockedSession(startDate);
        }

        function getBlockedSessionByFacilityId(startDate) {
            facilityService.getBlockedSessionsByFacilityId($scope.facilityId, startDate)
                    .then(function (events) {
                        parseEvents(events.data);
                    });
        }

        function  getBlockedSession(startDate) {
            facilityService.getBlockedSessions(startDate).then(function (events) {

                parseEvents(events.data);
            });
        }

        function parseEvents(events) {
            angular.forEach(events, function (event, keys) {
                var thisStartT = event.startsAt.substr(0, 10) + "T" + event.startsAt.substr(11, 8);
                event.startsAt = new Date(thisStartT);

                var thisEndT = event.endsAt.substr(0, 10) + "T" + event.endsAt.substr(11, 8);
                event.endsAt = new Date(thisEndT);
                this.push(event);
            }, $scope.events);
        }
        $scope.calendarView = 'week';
        $scope.calendarTitle = 'Name';
        $scope.calendarDay = new Date();

        $scope.getEvents = function (calendarDay, calendarView) {
            if (calendarView === "month") {
                var startDate = calendarDay.getFullYear() + "-" + (calendarDay.getMonth() + 1);

                if ($scope.facilityId) {

                    getBlockedSessionByFacilityId(startDate);
                } else {
                    getBlockedSession(startDate);
                }
            }
        }

        function showModal(action, event) {
            var modalInstance = $aside.open({
                templateUrl: 'calendarEvent.html',
                placement: 'right',
                size: 'sm',
                backdrop: true,
                resolve: {
                    selectedFacility: function () {
                        return $scope.facility;
                    },
                    facilityData: function () {
                        return $scope.facilityData;
                    }
                },
                controller: function ($scope, $modalInstance, selectedFacility, facilityData, facilityService) {
                    $scope.facilityData = facilityData;
                    $scope.selectedFacility = selectedFacility;
                    $scope.$modalInstance = $modalInstance;
                    $scope.action = action;
                    $scope.event = event;

                    $scope.toggleMin = function () {
                        $scope.minDate = $scope.minDate ? null : new Date();
                    };
                    $scope.toggleMin();

                    $scope.addEvent = function () {
                        //$modalInstance.dismiss('cancel');
                        facilityService.blockSession($scope.event).then(function (response) {
                            $modalInstance.close($scope.event, 'add');
                            $modalInstance.dismiss('cancel');

                        }).catch(function (response) {
                            $scope.errors = {};
                            angular.forEach(response.data, function (errors, field) {

                                $scope.errors[field] = (angular.isArray(errors)) ? errors.join(', ') : errors;
                            });
                        });

                    };
                    $scope.cancel = function () {
                        $modalInstance.dismiss('cancel');
                    };
                    $scope.deleteEvent = function () {
                        $modalInstance.close($scope.event, $scope.event);
                    };

                }
            });
            modalInstance.result.then(function (selectedEvent, action) {

                $scope.addEvent(selectedEvent);

            });
        }


        $scope.eventClicked = function (event) {
            var event = {title: "Blocked",
                startsAt: new Date(),
                available_facility_id: $scope.facilityId};
            showModal('Clicked', event);
        };
        $scope.addEvent = function (event) {
            $scope.events.push(event);

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
