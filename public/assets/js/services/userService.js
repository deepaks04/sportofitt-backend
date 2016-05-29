"use strict";

(function() {

    app.factory('userService', ['$q', '$timeout', '$http', '$cacheFactory','config', userService]);


    function userService($q,$timeout,$http,$cacheFactory,config){


return {
	getVendorProfile : getVendorProfile,
	updateUserInfo:updateUserInfo,
	getBillingInfo:getBillingInfo,
	getBankDetails : getBankDetails,
	updateBillingInfo:updateBillingInfo,
	updateBankDetails:updateBankDetails,
  getAreas:getAreas,
  getVendorImages:getVendorImages,
  deleteVendorImage : deleteVendorImage
}

function getVendorProfile(){
	 return $http({
                method: 'GET',
                url: config.backend + 'vendor/my-profile'
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
function getVendorImages(facilityId){
  return $http({
                method: 'GET',
                url: config.backend + 'vendor/images',
                data:{'facility':facilityId}
            })
            .then(sendResponseData)
            .catch(sendGetImagesError)
}

function sendGetImagesError(response) {

            return $q.reject('Error retrieving Image(s). (HTTP status: ' + response.status + ')');

        }
function deleteVendorImage(imageId){
return $http({
                method: 'GET',
                url: config.backend + 'vendor/images/'+imageId
            })
            .then(sendResponseData)
            .catch(sendDeleteImagesError)
}

function sendDeleteImagesError(response) {

            return $q.reject('Error deleting Image(s). (HTTP status: ' + response.status + ')');

        }
    function getAreas() {

            return $http({
                method: 'GET',
                url: config.backend + 'user/areas',
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
                url: config.backend + 'vendor/billing-info',
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
                url: config.backend + 'vendor/bank-info',
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
          return		$http.post(config.backend + 'vendor/my-profile', fd, {
          			transformRequest: angular.indentity,
          			headers: { 'Content-Type': undefined }
          		});

        }

         function updateBillingInfo(billingInfo){           

             var fd = new FormData();
          		for(var key in billingInfo)
          			fd.append(key, billingInfo[key]);
          		fd.append("_method","PUT");
          return		$http.post(config.backend + 'vendor/billing-info', fd, {
          			transformRequest: angular.indentity,
          			headers: { 'Content-Type': undefined }
          		});

        }

          function updateBankDetails(bankInfo){           

             var fd = new FormData();
          		for(var key in bankInfo)
          			fd.append(key, bankInfo[key]);
          		fd.append("_method","PUT");
          return		$http.post(config.backend + 'vendor/bank-info', fd, {
          			transformRequest: angular.indentity,
          			headers: { 'Content-Type': undefined }
          		});

        }

    }
})();