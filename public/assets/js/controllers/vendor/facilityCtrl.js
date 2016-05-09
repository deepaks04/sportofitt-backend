'use strict';
/**
 * controllers used for the facility
 */
app.controller('facilityAddCtrl', ["$scope", "$state", "$log", "facilityService", "SweetAlert","FileUploader",
    function ($scope, $state, $log, facilityService, SweetAlert,FileUploader) {

        facilityService.getRootCategory()
                .then(getRootCategorySuccess);

        facilityService.getDuration()
                .then(getDurationSuccess);

        $scope.types = {0: "Peak Time", 1: "Off time"};

        $scope.master = $scope.facility = {};
        $scope.form = {
            submit: function (form) {
                var field = null, firstError = null;
                for (field in form) {
                    if (field[0] != '$') {
                        if (firstError === null && !form[field].$valid) {
                            firstError = form[field].$name;
                        }

                        form[field].$dirty = true;
                        form[field].$invalid = false;
                        form[field].$valid = true;
                    }
                }

                var addFacility = facilityService.addFacility($scope.facility);

                addFacility.then(function (response) {
                    SweetAlert.swal("Created!", response.data.message, "success");
                    $state.go("vendor.facility.list");

                });
                addFacility.catch(function (data) {
                    if (data.status === 422) {
                        angular.forEach(data.data, function (errors, field) {
                            form[field].$dirty = true;
                            form[field].$invalid = true;
                            form[field].$valid = false;
                            form[field].$error = errors;
                        });
                    } else {
                        form['name'].$dirty = true;
                       form['name'].$error = data.data.message;
                    }
                   // SweetAlert.swal(data.data.message, data.data.statusText, "error");
                    return false;
                })

            },
            reset: function (form) {

                $scope.facility = angular.copy($scope.master);
                form.$setPristine(true);

            }
        };
        var uploaderImages = $scope.uploaderImages = new FileUploader({
            url: 'api/v1/vendor/images',
            alias: 'image_name',
            removeAfterUpload: true,
            autoUpload: true
        });
        uploaderImages.filters.push({
            name: 'imageFilter',
            fn: function (item/* {File|FileLikeObject} */, options) {
                var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            }
        });
        uploaderImages.onErrorItem = function (fileItem, response, status, headers) {
            console.info('onErrorItem', fileItem, response, status, headers);
            SweetAlert.swal("somethings going wrong", response.message, "error");
            return;
        };
         uploaderImages.onCancelItem = function (fileItem, response, status,
         headers) {
         console.info('onCancelItem', fileItem, response, status, headers);
         };
        uploaderImages.onCompleteItem = function (fileItem, response, status, headers) {
            console.info('onCompleteItem', fileItem, response, status, headers);
            //$scope.getVendorImages();
        };
        uploaderImages.onCompleteAll = function () {
            console.info('onCompleteAll');
        };

//        $scope.getVendorImages = function () {
//            userService.getVendorImages().then(function (images) {
//                $scope.images = images.images || {};
////			$scope.uploaderImages.queue.length = $scope.images.length;
//             
//            }).catch(function (response) {
//                console.log(response);
//                $scope.images = {};
//            });
//        }
//        $scope.removeImage = function (imageId) {
//            userService.deleteVendorImage(imageId).then(function (images) {
//                $scope.getVendorImages();
//            }).catch(function (response) {
//                console.log(response);
//                $scope.images = {};
//            });
//        }
//        $scope.getVendorImages();
        function getRootCategorySuccess(categoryData) {
            $scope.categoryData = categoryData.category;

            console.log($scope.categoryData);
        }

        function getDurationSuccess(durations) {
            $scope.durations = durations.duration;
        }
    }]);

//List of facility
app.controller('facilityListCtrl', ["$scope", "$filter", "$modal", "$log", "ngTableParams", "facilityService", "SweetAlert",
    function ($scope, $filter, $modal, $log, ngTableParams, facilityService, SweetAlert) {

        $scope.facilityData = {};
        facilityService.getAllFacilities()
                .then(getFacilitySuccess);

        function getFacilitySuccess(facilityData) {
          //  $scope.facilityData = facilityData.facility;
            if(facilityData.facility) {
                $scope.facilityData = Object.keys(facilityData.facility).map(function (key) {
                    return facilityData.facility[key];
                });
                $scope.tableParams = new ngTableParams({
                    page: 1, // show first page
                    count: 5, // count per page
                    sorting: {
                        name: 'desc' // initial sorting
                    }
                }, {
                    total: $scope.facilityData.length, // length of data
                    getData: function ($defer, params) {
                        // use build-in angular filter
                        var orderedData = params.sorting() ? $filter('orderBy')($scope.facilityData, params.orderBy()) : $scope.facilityData;

                        $defer.resolve(orderedData);
                    }
                });

            }
        }

        $scope.blockUnblockFacility = function (facility) {
            SweetAlert.swal({
                title: "Are you sure?",
                text: "Your will not be able to recover this event!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel it!",
                closeOnConfirm: true,
                closeOnCancel: false
            }, function (isConfirm) {
                if (isConfirm) {
                    facility.is_active = !facility.is_active;
                    var data = {'is_active': facility.is_active};

                    var statusBlocking = facilityService.blockUnblockFacility(facility.id, data);

                    statusBlocking.then(function (response) {
                        SweetAlert.swal("Deleted!", response.data.message, "success");
                    });

                    statusBlocking.catch(function (response) {
                        SweetAlert.swal("Not Deleted", response.data.message, "error");
                    });
                } else {
                    SweetAlert.swal("Cancelled", "facility is safe :)", "error");
                }
            });

        };

        $scope.open = function (size, selectedFacility, tabActive, isAdd) {

            var isAdd = isAdd;
            var modalInstance = $modal.open({
                templateUrl: 'sessionPackageModal.html',
                controller: 'SessionModalInstanceCtrl',
                size: size,
                resolve: {
                    selectedFacility: function () {
                        return selectedFacility;
                    },
                    tab: function () {
                        return tabActive;
                    },
                    isAdd: function () {
                        return isAdd;
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


app.controller('SessionModalInstanceCtrl', ["$scope", "$modalInstance", "$filter", "selectedFacility", "isAdd", "tab", "facilityService", "SweetAlert","FileUploader",
    function ($scope, $modalInstance, $filter, selectedFacility, isAdd, tab, facilityService, SweetAlert, FileUploader) {

        $scope.facility = selectedFacility;

        $scope.tab = tab;
        $scope.tabs = ['opening_hours', 'sessions', 'packages', 'edit', 'images'];
        facilityService.getDuration()
                .then(getDurationSuccess);

        facilityService.getRootCategory()
                .then(getRootCategorySuccess);

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

        $scope.types = {0: "Peak Time", 1: "Off time"};

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
            $scope.setTab('sessions');
            if (!isAdd) {
                if ($scope.sessions.length) {
                    return $scope.sessions;
                }
                facilityService.getSessionsByFacilityId($scope.facility.id).then(function (sessions) {
                    var sessions = (sessions.data === "") ? [] : sessions.data;
                    parseSessions(sessions);
                });
            }
        };

        function parseSessions(sessions) {
            angular.forEach(sessions, function (session, keys) {
                session.peak = parseInt(session.peak);
                session.off_peak = parseInt(session.off_peak);
                this.push(session);
            }, $scope.sessions);
        }

        $scope.getOpeningHours = function () {
            $scope.setTab('opening_hours');
            if (!isAdd) {
                if ($scope.openingHours.length) {
                    return $scope.openingHours;
                }

                facilityService.getOpeningTimesByFacilityId($scope.facility.id)
                        .then(function (openingHours) {
                            var openingHours = (openingHours.data === "") ? [] : openingHours.data;
                            parseOpeningHours(openingHours);
                        });
            }
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
            $scope.setTab('packages');
            if (!isAdd) {

                if ($scope.packages.length) {
                    return $scope.packages;
                }
                facilityService.getPackagesByFacilityId($scope.facility.id)
                        .then(function (packages) {
                            var packages = (packages.data === "") ? [] : packages.data;
                            parsePackages(packages);
                        }).catch(function (data) {
                    //console.log(data.data);
                });
            }
        };

        function parsePackages(packages) {
            angular.forEach(packages, function (pack, keys) {
                pack.is_peak = parseInt(pack.is_peak);
                pack.month = parseInt(pack.month);
                this.push(pack);
            }, $scope.packages);
        }

        $scope.checkHoursData = function (data, editHoursForm) {

            editHoursForm.$show();

        };

        $scope.saveHours = function (data, id, editHoursForm) {
            // $scope.user not updated yet

            angular.extend(data, {id: id, available_facility_id: $scope.facility.id});

            var saveHours = facilityService.saveOpeningTime(data).then(function (response) {
                editHoursForm.$dirty = false;
                editHoursForm.$invalid = false;

            }).catch(function (response) {
                $scope.hoursErrors = {};
                if (response.status == 422) {
                    angular.forEach(response.data, function (errors, field) {

                        editHoursForm.$setError(field, errors.join(','));

                        $scope.hoursErrors[field] = errors.join(',');

                    });
                } else {
                    editHoursForm.$setError('end', response.data.message);

                    $scope.hoursErrors['end'] = response.data.message;
                }
                editHoursForm.$dirty = true;
                editHoursForm.$invalid = true;
                editHoursForm.$show();
            });
        };

        $scope.setTab = function (tab) {
            $scope.tab = tab;
        };

        $scope.showDay = function (time) {

            var selected = [];
            if (time.day) {
                selected = $filter('filter')($scope.days, {id: time.day});
            }
            return selected.length ? selected[0].name : 'Not set';
        };

        $scope.saveSession = function (data, id, rowform) {
            // $scope.user not updated yet
            angular.extend(data, {id: id, available_facility_id: $scope.facility.id});

            $scope.errors = {};
            rowform.$dirty = true;
            rowform.$invalid = true;
            rowform.$visible = true;
            var saveSession = facilityService.saveSession(data).then(function (data) {
                rowform.$dirty = false;
                rowform.$invalid = false;
            }).catch(function (response) {

                if (response.status == 422) {
                    angular.forEach(response.data, function (errors, field) {

                        rowform.$setError(field, errors.join(','));

                        $scope.errors[field] = errors.join(',');

                    });
                } else {
                    rowform.$setError('message', response.data.message);

                    $scope.errors['message'] = response.data.message;
                }

                rowform.$show();
            });
        };

        $scope.savePackage = function (data, id, rowform) {
            // $scope.user not updated yet
            angular.extend(data, {id: id, available_facility_id: $scope.facility.id});

            $scope.errors = {};
            rowform.$dirty = true;
            rowform.$invalid = true;
            rowform.$visible = true;
            var savePackage = facilityService.savePackage(data);

            savePackage.then(function (data) {
//                $scope.showSessionEdit = false;
//                console.log(data);
                rowform.$dirty = false;
                rowform.$invalid = false;
            });
            savePackage.catch(function (response) {
                if (response.status == 422) {
                    angular.forEach(response.data, function (errors, field) {

                        rowform.$setError(field, errors.join(','));

                        $scope.errors[field] = errors.join(',');

                    });
                } else {
                    rowform.$setError('message', response.data.message);

                    $scope.errors['message'] = response.data.message;
                }

                rowform.$show();
            });
        };

        // remove Opening Hours
        $scope.removeOpeningHours = function (index, timeId) {

            if (timeId) {
                SweetAlert.swal({
                    title: "Are you sure to delete this opening time?",
                    text: "Your will not be able to recover this event!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel it!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function (isConfirm) {
                    if (isConfirm) {
                        var removeStatus = facilityService.removeOpeningTime(timeId);
                        removeStatus.then(function (response) {
                            //console.log(response);
                            $scope.openingHours.splice(index, 1);
                            SweetAlert.swal("Deleted!", "Opening time has been deleted.", "success");
                        });

                    } else {
                        SweetAlert.swal("Cancelled", "Opening time is safe :)", "error");
                    }
                });
            } else {
                $scope.openingHours.splice(index, 1);
            }
        };

        // remove Session
        $scope.removeSession = function (index, sessionId) {
            if (sessionId) {

                SweetAlert.swal({
                    title: "Are you sure to delete this session?",
                    text: "Your will not be able to recover this event!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "btn-primary",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel it!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function (isConfirm) {
                    if (isConfirm) {
                        var removeStatus = facilityService.removeSession(sessionId);
                        removeStatus.then(function (response) {
                            //console.log(response);
                            $scope.sessions.splice(index, 1);
                            SweetAlert.swal("Deleted!", "Session has been deleted.", "success");
                        });

                    } else {
                        SweetAlert.swal("Cancelled", "Session is safe :)", "error");
                    }
                });
            } else {
                $scope.sessions.splice(index, 1);
            }
        };

        // remove Package
        $scope.removePackage = function (index, packageId) {
            if (packageId) {

                SweetAlert.swal({
                    title: "Are you sure to delete this package?",
                    text: "Your will not be able to recover this event!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-primary",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel it!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function (isConfirm) {
                    if (isConfirm) {
                        var removeStatus = facilityService.removePackage(packageId);
                        removeStatus.then(function (response) {
                            //console.log(response);
                            $scope.packages.splice(index, 1);
                            SweetAlert.swal("Deleted!", "Package has been deleted.", "success");
                        });

                    } else {
                        SweetAlert.swal("Cancelled", "Package is safe :)", "error");
                    }
                });
            } else {
                $scope.packages.splice(index, 1);
            }
        };

        // add Opening Hours
        $scope.addHours = function () {
            var dt = new Date();
            $scope.inserted = {
                id: '',
                day: '',
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
            $scope.calculateSessionPricing($scope.sessionInserted, $scope.sessionInserted);
            $scope.sessions.push($scope.sessionInserted);
            $scope.showSessionEdit = true;
        };

        $scope.calculateSessionPricing = function (rowData, session) {

            var peakPricing = $scope.facility.peak_hour_price * rowData.peak;
            var offPeakPricing = $scope.facility.off_peak_hour_price * rowData.off_peak;

            session['peak'] = rowData.peak;
            session['off_peak'] = rowData.off_peak;
            session['price'] = peakPricing + offPeakPricing;


            console.log(session);
        };

        // add Sessions
        $scope.addPackage = function () {
            $scope.packageInserted = {
                id: '',
                name: "",
                month: 1,
                actual_price: "",
                is_peak: 1,
                discount: 0,
                description: ""
            };
            $scope.packages.push($scope.packageInserted);
        };


        // edit facility

        $scope.update = function (form) {
             var field = null, firstError = null;
            for (field in form) {
                if (field[0] != '$') {
                    if (firstError === null && !form[field].$valid) {
                        firstError = form[field].$name;
                    }

                        form[field].$dirty = true;
                        form[field].$invalid = false;
                    form[field].$valid = true;
                    }
            }
            var addFacility = facilityService.updateFacility($scope.facility);
            addFacility.then(function (response) {
                SweetAlert.swal("Success", response.data.message, "success");
            });
            addFacility.catch(function (data, status) {
                angular.forEach(data.data, function (errors, field) {
                 form[field].$invalid = true;
                   form[field].$error = errors;
                });

                return false;
            })

        };

        function getRootCategorySuccess(categoryData) {
            $scope.categoryData = categoryData.category;
            angular.forEach($scope.categoryData, function (data, id) {
                if (data.id == $scope.facility.category.root.id) {
                    $scope.selectedCategory = data.sub_category;
                }
            });
        }
        
        var uploaderImages = $scope.uploaderImages = new FileUploader({
            url: 'api/v1/vendor/facility/upload',
            alias: 'image_name',
            formData: [{'facility_id':$scope.facility.id}],
            removeAfterUpload: true,
            autoUpload: true
        });
        uploaderImages.filters.push({
            name: 'imageFilter',
            fn: function (item/* {File|FileLikeObject} */, options) {
                var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            }
        });
        uploaderImages.onErrorItem = function (fileItem, response, status, headers) {
            SweetAlert.swal("somethings going wrong", response.message, "error");
            return;
        };
         uploaderImages.onCancelItem = function (fileItem, response, status,
         headers) {
         };
        uploaderImages.onCompleteItem = function (fileItem, response, status, headers) {
            //$scope.getVendorImages();
        };
        uploaderImages.onCompleteAll = function () {
            $scope.getVendorImages();
        };

        $scope.getVendorImages = function () {
            facilityService.getFacilityImages($scope.facility.id).then(function (images) {
                $scope.images = images.images || {};
                $scope.tab = 'images';
//			$scope.uploaderImages.queue.length = $scope.images.length;
             
            }).catch(function (response) {
                $scope.images = {};
                $scope.imagesReponse = response;
            });
        }

        $scope.removeImage = function (imageId) {
            facilityService.deleteFacilityImage(imageId).then(function (images) {
                $scope.getVendorImages();
            }).catch(function (response) {
                $scope.images = {};
            });
        }
        $scope.getVendorImages();

        function getDurationSuccess(durations) {
            $scope.durations = durations.duration;
        }

        if ($scope.tab === 'opening_hours') {
            $scope.getOpeningHours();
        } else if ($scope.tab === 'sessions') {
            $scope.getSessions();
        } else if ($scope.tab === 'packages') {
            $scope.getPackages();
        } else if ($scope.tab === 'images') {
            $scope.getVendorImages();
        }
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

app.controller('facilityBookingCtrl', ["$scope", "$stateParams", "$aside", "moment", "facilityService", "SweetAlert",
    function ($scope, $stateParams, $aside, moment, facilityService, SweetAlert) {
        $scope.facilityId = $stateParams.facilityId;

        $scope.facilityData = {};
        $scope.showAvailableSessions = false;    
        
        var vm = this;

        $scope.events = [];
//console.log($scope.facilityId);
        function init() {
        

            facilityService.getAllFacilities()
                    .then(getAllFacilitySuccess);
            //console.log($scope.facilityId);
            if ($scope.facilityId) {
                facilityService.getFacilityById($scope.facilityId)
                        .then(getFacilitySuccess);
            }
            getBlockData();
        }

        init();

        function getBlockData() {
            var startDate = new Date().getTime();

            if ($scope.facilityId) {
                getBlockedSessionByFacilityId(startDate,'week')
            } else {
                getBlockedSession(startDate,'week');
            }
        }

        function getAllFacilitySuccess(facilityData) {
            $scope.facilityData = facilityData.facility;
        }

        function getFacilitySuccess(facilityData) {
            $scope.facility = facilityData.facility;
        }

        function getBlockedSessionByFacilityId(startDate,calendarView) {
            facilityService.getBlockedSessionsByFacilityId($scope.facilityId, startDate,calendarView)
                    .then(function (events) {
                        parseEvents(events.data);
                    });
        }

        function getBlockedSession(startDate,calendarView) {
            facilityService.getBlockedSessions(startDate,calendarView).then(function (events) {
                parseEvents(events.data);
            });
        }

        function parseEvents(events) {
            $scope.events = [];
            angular.forEach(events, function (event, keys) {
               // var thisStartT = event.startsAt.substr(0, 10) + "T" + event.startsAt.substr(11, 8) + "+0530";
               event.startsAt = new Date(event.startsAt);

              //  var thisEndT = event.endsAt.substr(0, 10) + "T" + event.endsAt.substr(11, 8) + "+0530";
                event.endsAt = new Date(event.endsAt);
                this.push(event);
            }, $scope.events);
        }

        $scope.calendarView = 'week';
        $scope.calendarTitle = 'Name';
        $scope.viewDate = new Date();
        
        $scope.getEvents = function (viewDate, calendarView) {
            //if (calendarView === "month") {
                var startDate = viewDate.getTime();
                if ($scope.facilityId) {

                    getBlockedSessionByFacilityId(startDate,calendarView);
                } else {
                    getBlockedSession(startDate,calendarView);
                }
            //}
        };
        
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
                    $scope.showAvailableSessions = false;
                    $scope.selectedFacility = selectedFacility;
                    $scope.$modalInstance = $modalInstance;
                    $scope.action = action;
                    $scope.event = angular.copy(event);
                    $scope.event.peakHourOptions = [
                        {id: "peakHour", name:"peakHour", description:"Peak Hour", value:true},
                        {id: "nonPeakHour", name:"nonPeakHour", description: "Non Peak Hour", value:false}
                    ];
                    $scope.event.peakHourSelected = $scope.event.peakHourOptions[0];
                    $scope.availableSessions = [];
                    $scope.getAvailableSessionsByFacilityId = function() {
                        var facilityId = $scope.event.available_facility_id;
                        var eventStartsAt = new Date($scope.event.startsAt).getTime();
                        var peakHourSelectedValue = $scope.event.peakHourSelected.value;
                        facilityService.getAvailableSessionsByFacilityId(facilityId, eventStartsAt, peakHourSelectedValue)
                        .then(function(response){
                            
                            if(angular.isArray(response.data.message.success)){
                                $scope.showAvailableSessions = true;
                                $scope.availableSessions = response.data.data;
                                $scope.event.selectedSession = $scope.eventForm.blockSession =response.data.data[0];
                                $scope.availableSessions.message = response.data.message.success[0];
                            }
                            
                            else
                                $scope.availableSessions.message = response.data.message.success;
                        })
                        .catch(function (response) {
                            $scope.errors = {};
                            angular.forEach(response.data, function (errors, field) {

                                $scope.errors[field] = (angular.isArray(errors)) ? errors.join(', ') : errors;
                            });
                        });
                        

                    };
                    
                    $scope.hideAvailableSessions = function(){
                         $scope.showAvailableSessions = false;
                    };
                    
                    $scope.toggleMin = function () {
                        $scope.minDate = $scope.minDate ? null : new Date();
                    };
                    $scope.toggleMin();

                    $scope.addEvent = function () {
                        var sessionToBlock = {
                          facility_id: $scope.event.available_facility_id,
                          date: new Date($scope.event.startsAt).getTime(),
                          is_peak: $scope.event.peakHourSelected.value,
                          slot_timing:$scope.event.selectedSession
                        };
                        //$modalInstance.dismiss('cancel');
                        //$scope.event.startsAt =$scope.event.startsAt.toLocaleString()
                        facilityService.blockSession(sessionToBlock).then(function (response) {
                            //$modalInstance.close($scope.event, 'add');
                            SweetAlert.swal("Good job!", "Event added successfully!", "success");
                            getBlockData();

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
                    $scope.deleteEvent = function (eventId) {
                        SweetAlert.swal({
                            title: "Are you sure?",
                            text: "Your will not be able to recover this event!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete it!",
                            cancelButtonText: "No, cancel it!",
                            closeOnConfirm: true,
                            closeOnCancel: false
                        }, function (isConfirm) {
                            if (isConfirm) {
                                facilityService.removeBlockedSession(eventId).then(function (response) {
                                    //$modalInstance.close($scope.event, 'add');
                                    console.log(response);
                                    getBlockData();
                                    $modalInstance.close($scope.event, $scope.event)

                                }).catch(function (response) {
                                    console.log(response);
                                });
                                //$scope.events.splice(event.$id, 1);
                                //SweetAlert.swal("Deleted!", "Event has been deleted.", "success");
                            } else {
                                SweetAlert.swal("Cancelled", "Event is safe :)", "error");
                            }
                        });
                        //$modalInstance.close($scope.event, $scope.event);
                    };

                }
            });
            modalInstance.result.then(function (selectedEvent, action) {

                $scope.addEvent(selectedEvent);

            });
        }


        $scope.eventClicked = function (event) {
            var event = {
                title: "Blocked",
                startsAt: new Date(),
                available_facility_id: $scope.facilityId
            };
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
                cancelButtonText: "No, cancel it!",
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
