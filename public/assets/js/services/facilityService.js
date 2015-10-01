"use strict";

(function() {

    app.factory('facilityService', ['$q', '$timeout', '$http', '$cacheFactory', dataService]);


    function dataService($q, $timeout, $http,  $cacheFactory) {

        return {
            getRootCategory: getRootCategory,
  addFacility: addFacility,
  getAllFacilities:getAllFacilities,
  getFacilityById:getFacilityById
        };

        
function getAllFacilities() {

            return $http({
                method: 'GET',
                url: 'api/v1/vendor/facility',
               // transformResponse: transformGetFacilities,
                // cache: true
            })
            .then(sendResponseData)
            .catch(sendGetFaclityError)

        }

        function getRootCategory() {

            return $http({
                method: 'GET',
                url: 'api/v1/user/get-root-category',
                 cache: true
            })
            .then(sendResponseData)
            .catch(sendGetRootCategoriesError)

        }

        function deleteAllBooksResponseFromCache() {

            var httpCache = $cacheFactory.get('$http');
            httpCache.remove('api/books');

        }


        function transformGetFacilities(data, headersGetter) {

            var transformed = angular.fromJson(data);

            transformed.forEach(function (currentValue, index, array) {
                currentValue.dateDownloaded = new Date();
            });

            //console.log(transformed);
            return transformed;

        }

        function sendResponseData(response) {

            return response.data;

        }

         function sendGetFaclityError(response) {

            return $q.reject('Error retrieving facility(s). (HTTP status: ' + response.status + ')');

        }

        function sendGetRootCategoriesError(response) {

            return $q.reject('Error retrieving Root categories(s). (HTTP status: ' + response.status + ')');

        }

        function getFacilityById(facilityID) {

            return $http.get('api/v1/vendor/facility/' + facilityID)
            .then(sendResponseData)
            .catch(sendGetFaclityError);

        }

        function updateBook(book) {

            deleteSummaryFromCache();
            deleteAllBooksResponseFromCache();

            return $http({
                method: 'PUT',
                url: 'api/books/' + book.book_id,
                data: book
            })
            .then(updateBookSuccess)
            .catch(updateBookError);

        }

        function updateBookSuccess(response) {

            return 'Book updated: ' + response.config.data.title;

        }

        function updateBookError(response) {

            return $q.reject('Error updating book.(HTTP status: ' + response.status + ')');

        }

        function addFacility(data) {

            // deleteSummaryFromCache();
            // deleteAllBooksResponseFromCache();
            var fd = new FormData();
          		for(var key in data)
          			fd.append(key, data[key]);
          return		$http.post('api/v1/vendor/facility', fd, {
          			transformRequest: angular.indentity,
          			headers: { 'Content-Type': undefined }
          		});
          // .then(addFacilitySuccess)
          //   .catch(addFacilityError);
        }


        function addFacilitySuccess(response){
            return 'Facility added: ' + response.config.data.title;

        }

        function addFacilityError(response) {

            return $q.reject('Error adding Facility. (HTTP status: ' + response.status + ')');

        }

        function deleteBook(bookID) {

            deleteSummaryFromCache();
            deleteAllBooksResponseFromCache();

            return $http({
                method: 'DELETE',
                url: 'api/books/' + bookID
            })
                .then(deleteBookSuccess)
                .catch(deleteBookError);

        }

        function deleteBookSuccess(response) {

            return 'Book deleted.';

        }

        function deleteBookError(response) {

            return $q.reject('Error deleting book. (HTTP status: ' + response.status + ')');

        }

        function getAllReaders() {

            var readersArray = [
                {
                    reader_id: 1,
                    name: 'Marie',
                    weeklyReadingGoal: 315,
                    totalMinutesRead: 5600
                },
                {
                    reader_id: 2,
                    name: 'Daniel',
                    weeklyReadingGoal: 210,
                    totalMinutesRead: 3000
                },
                {
                    reader_id: 3,
                    name: 'Lanier',
                    weeklyReadingGoal: 140,
                    totalMinutesRead: 600
                }
            ];

            var deferred = $q.defer();

            $timeout(function() {

                deferred.resolve(readersArray);

            }, 1500);

            return deferred.promise;
        }
    }

}());
