"use strict";

(function () {

    app.factory('facilityService', ['$q', '$timeout', '$http', '$cacheFactory', dataService]);


    function dataService($q, $timeout, $http, $cacheFactory) {

        return {
            getRootCategory: getRootCategory,
            addFacility: addFacility,
            updateFacility: updateFacility,
            getAllFacilities: getAllFacilities,
            getFacilityById: getFacilityById,
            getFacilityDetailsById: getFacilityDetailsById,
            getDuration: getDuration,
            addOpeningTime: addOpeningTime,
            getOpeningTimesByFacilityId: getOpeningTimesByFacilityId,
            getSessionsByFacilityId: getSessionsByFacilityId,
            getPackagesByFacilityId: getPackagesByFacilityId,
            saveSession: saveSession,
            addPackage: addPackage
        };

        function getDuration() {
            return $http({
                method: 'GET',
                url: 'api/v1/vendor/duration',
                // transformResponse: transformGetFacilities,
                // cache: true
            })
                    .then(sendResponseData)
                    .catch(sendGetError);
        };

        function sendGetError(response) {

            return $q.reject('Error retrieving data(s). (HTTP status: ' + response.status + ')');

        };

        function getSessionsByFacilityId(facilityId) {
            return $http({
                method: 'GET',
                url: 'api/v1/vendor/sessions-data/' + facilityId,
                // transformResponse: transformGetFacilities,
                // cache: true
            })
                    .then(sendResponseData)
                    .catch(sendGetError);
        };

        function getOpeningTimesByFacilityId(facilityId) {
            return $http({
                method: 'GET',
                url: 'api/v1/vendor/opening-time/' + facilityId,
                // transformResponse: transformGetFacilities,
                // cache: true
            })
                    .then(sendResponseData)
                    .catch(sendGetError);
        };

        function getPackagesByFacilityId(facilityId) {
            return $http({
                method: 'GET',
                url: 'api/v1/vendor/package/' + facilityId,
                // transformResponse: transformGetFacilities,
                // cache: true
            })
                    .then(sendResponseData)
                    .catch(sendGetError);
        };

        function getAllFacilities() {
            return $http({
                method: 'GET',
                url: 'api/v1/vendor/facility',
                // transformResponse: transformGetFacilities,
                // cache: true
            })
                    .then(sendResponseData)
                    .catch(sendGetFaclityError);
        };

        function getRootCategory() {
            return $http({
                method: 'GET',
                url: 'api/v1/user/get-root-category',
                cache: true
            })
                    .then(sendResponseData)
                    .catch(sendGetRootCategoriesError);
        };

        function deleteAllBooksResponseFromCache() {
            var httpCache = $cacheFactory.get('$http');
            httpCache.remove('api/books');
        };

        function transformGetFacilities(data, headersGetter) {
            var transformed = angular.fromJson(data);

            transformed.forEach(function (currentValue, index, array) {
                currentValue.dateDownloaded = new Date();
            });

            // console.log(transformed);
            return transformed;
        };

        function sendResponseData(response) {

            return response.data;

        }

        function sendGetFaclityError(response) {

            return $q.reject('Error retrieving facility(s). (HTTP status: ' + response.status + ')');

        }

        function sendGetRootCategoriesError(response) {

            return $q.reject('Error retrieving Root categories(s). (HTTP status: ' + response.status + ')');

        }

        function getFacilityById(facilityId) {

            return $http.get('api/v1/vendor/facility/' + facilityId)
                    .then(sendResponseData)
                    .catch(sendGetFaclityError);

        }

        function getFacilityDetailsById(facilityId) {
            return $http.get('api/v1/vendor/facility-detail/' + facilityId).then(sendResponseData)
                    .catch(sendGetFaclityError);
        }

        function updateFacility(facility) {
            return $http({
                method: 'PUT',
                url: 'api/v1/vendor/facility/' + facility.id,
                data: facility
            })
                    .then(updateFacilitySuccess)
                    .catch(updateFacilityError);
        }

        function updateFacilitySuccess(response) {

            return 'Facility updated: ' + response.config.data.title;

        }

        function updateFacilityError(response) {

            return $q.reject('Error updating Facility.(HTTP status: ' + response.status + ')');

        }

        function addFacility(data) {

            // deleteSummaryFromCache();
            // deleteAllBooksResponseFromCache();
            var fd = new FormData();
            for (var key in data)
                fd.append(key, data[key]);
            return		$http.post('api/v1/vendor/facility', fd, {
                transformRequest: angular.indentity,
                headers: {'Content-Type': undefined}
            });
            // .then(addFacilitySuccess)
            // .catch(addFacilityError);
        }


        function addFacilitySuccess(response) {
            return 'Facility added: ' + response.config.data.title;

        }

        function addFacilityError(response) {

            return $q.reject('Error adding Facility. (HTTP status: ' + response.status + ')');

        }

        function addOpeningTime(data) {
            if (data.start instanceof Date)
            {
                data.start = data.start.getHours() + ":" + ("0" + data.start.getMinutes()).slice(-2);
                ;
            }

            if (data.end instanceof Date)
            {
                data.end = data.end.getHours() + ":" + ("0" + data.end.getMinutes()).slice(-2);
                ;
            }
            var fd = new FormData();
            for (var key in data)
                fd.append(key, data[key]);
            console.log(fd);
            return		$http.post('api/v1/vendor/opening-time', fd, {
                transformRequest: angular.indentity,
                headers: {'Content-Type': undefined}
            });
        }

        function saveSession(data) {
            var fd = new FormData();
            for (var key in data)
                fd.append(key, data[key]);
            var url = 'api/v1/vendor/multiple-sessions';
            if (data.id !== "") {
                url += "/" + data.id;
                fd.append("_method", "PUT");
            }
            return		$http.post(url, fd, {
                transformRequest: angular.indentity,
                headers: {'Content-Type': undefined}
            });
        }

        function addPackage(data) {
            var fd = new FormData();
            for (var key in data)
                fd.append(key, data[key]);
            console.log(fd);
            return		$http.post('api/v1/vendor/package', fd, {
                transformRequest: angular.indentity,
                headers: {'Content-Type': undefined}
            });
        }


//		function deleteBook(bookID) {

//		deleteSummaryFromCache();
//		deleteAllBooksResponseFromCache();

//		return $http({
//		method: 'DELETE',
//		url: 'api/books/' + bookID
//		})
//		.then(deleteBookSuccess)
//		.catch(deleteBookError);

//		}

//		function deleteBookSuccess(response) {

//		return 'Book deleted.';

//		}

//		function deleteBookError(response) {

//		return $q.reject('Error deleting book. (HTTP status: ' + response.status +
//		')');

//		}

    }

}());
