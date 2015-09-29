"use strict";

(function() {

    app.factory('userService', ['$q', '$timeout', '$http', '$cacheFactory', userService]);


    function userService($q,$timeout,$http,$cacheFactory){


return {
	getVendorProfile : getVendorProfile,
	updateUserInfo:updateUserInfo
}

function getVendorProfile(){
	 return $http({
                method: 'GET',
                url: 'api/v1/vendor/my-profile',
                 cache: true
            })
            .then(sendResponseData)
            .catch(sendGetUserError)
};

 function sendResponseData(response) {

            return response.data.profile;

        }

         function sendGetUserError(response) {

            return $q.reject('Error retrieving User(s). (HTTP status: ' + response.status + ')');

        }

    

  

       function updateUserInfo(userInfo){           

             var fd = new FormData();
          		for(var key in userInfo)
          			fd.append(key, userInfo[key]);
          return		$http.post('api/v1/vendor/my-profile', fd, {
          			transformRequest: angular.indentity,
          			headers: { 'Content-Type': undefined }
          		});

        }

        function updateUserSuccess(response) {
console.log(response);
            return 'User updated: ' + response.config.data;

        }

        function updateUserError(response) {

            return $q.reject('Error updating user.(HTTP status: ' + response.status + ')');

        }
    }
})();