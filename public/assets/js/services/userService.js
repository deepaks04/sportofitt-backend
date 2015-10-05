"use strict";

(function() {

    app.factory('userService', ['$q', '$timeout', '$http', '$cacheFactory', userService]);


    function userService($q,$timeout,$http,$cacheFactory){


return {
	getVendorProfile : getVendorProfile,
	updateUserInfo:updateUserInfo,
	getBillingInfo:getBillingInfo,
	getBankDetails : getBankDetails,
	updateBillingInfo:updateBillingInfo,
	updateBankDetails:updateBankDetails,
  getAreas:getAreas
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

            return response.data;

        }

         function sendGetUserError(response) {

            return $q.reject('Error retrieving User(s). (HTTP status: ' + response.status + ')');

        }

    function getAreas() {

            return $http({
                method: 'GET',
                url: 'api/v1/user/areas',
                 cache: true
            })
            .then(sendResponseData)
            .catch(sendGetAreasError)

        }

        function sendGetAreasError(response) {

            return $q.reject('Error retrieving Areas. (HTTP status: ' + response.status + ')');

        }
function getBillingInfo(){
	 return $http({
                method: 'GET',
                url: 'api/v1/vendor/billing-info',
                 cache: true
            })
            .then(sendResponseData)
            .catch(sendGetBillingInfoError)
};

         function sendGetBillingInfoError(response) {

            return $q.reject('Error retrieving Billing Info. (HTTP status: ' + response.status + ')');

        }

        function getBankDetails(){
	 return $http({
                method: 'GET',
                url: 'api/v1/vendor/bank-info',
                 cache: true
            })
            .then(sendResponseData)
            .catch(sendGetBankDetailsError)
};


         function sendGetBankDetailsError(response) {

            return $q.reject('Error retrieving Bank Detail(s). (HTTP status: ' + response.status + ')');

        }

  

       function updateUserInfo(userInfo){           

             var fd = new FormData();
          		for(var key in userInfo)
          			fd.append(key, userInfo[key]);
          		fd.append("_method","PUT");
          return		$http.post('api/v1/vendor/my-profile', fd, {
          			transformRequest: angular.indentity,
          			headers: { 'Content-Type': undefined }
          		});

        }

         function updateBillingInfo(billingInfo){           

             var fd = new FormData();
          		for(var key in billingInfo)
          			fd.append(key, billingInfo[key]);
          		fd.append("_method","PUT");
          return		$http.post('api/v1/vendor/billing-info', fd, {
          			transformRequest: angular.indentity,
          			headers: { 'Content-Type': undefined }
          		});

        }

          function updateBankDetails(bankInfo){           

             var fd = new FormData();
          		for(var key in bankInfo)
          			fd.append(key, bankInfo[key]);
          		fd.append("_method","PUT");
          return		$http.post('api/v1/vendor/bank-info', fd, {
          			transformRequest: angular.indentity,
          			headers: { 'Content-Type': undefined }
          		});

        }

    }
})();