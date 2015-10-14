'use strict';
/** 
  * controller for User Profile Example
*/
app.controller('ProfileCtrl', ["$scope", "flowFactory",'$geolocation',"userService","SweetAlert",'FileUploader',
 function ($scope, flowFactory,$geolocation,userService,SweetAlert,FileUploader) {



    $scope.removeImage = function () {
        $scope.noImage = true;
    };
    $scope.obj = new Flow();

    $scope.bankDetail = {};
 userService.getBillingInfo().then(function(billingInfo){
 $scope.billingInfo=billingInfo.billing;	
});
 userService.getVendorProfile().then(function(userInfo){
$scope.userInfo=userInfo.profile;	
$geolocation.getCurrentPosition({
            timeout: 0
         }).then(function(position) {
          // console.log(position);
       $scope.userInfo.longitude = position.coords.longitude;
$scope.userInfo.latitude = position.coords.latitude;
         });
    if ($scope.userInfo.profile_picture == '') {
        $scope.noImage = true;
     }


});



userService.getAreas().then(function(areas){
$scope.areas=areas.area;  
});

 userService.getBankDetails().then(function(bankDetail){
$scope.bankDetail=bankDetail.bank;	
});  
 

  $scope.form = {

            submit: function (form) {
                  var updateProfile =userService.updateUserInfo($scope.userInfo);
      updateProfile.then(function(response){
      SweetAlert.swal(response.data.message, "success");
     
      console.log(response);
      });
      updateProfile.catch(function(data,status){
      // console.log(data);
  $scope.errors = {};
   angular.forEach(data.data,function(errors,field){
          
  $scope.errors[field] = errors.join(', ');
   });
   console.log($scope.errors);
      // SweetAlert.swal("somethings going wrong",data.data.statusText, "error");
      return;
      })             

            },
            reset: function (form) {

                $scope.userInfo = angular.copy($scope.master);
                form.$setPristine(true);

            }
        };



        $scope.billingForm = {

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

                  var updateBillingInfo =userService.updateBillingInfo(form);
      updateBillingInfo.then(function(response){
      SweetAlert.swal(response.data.message, "success");
     
      console.log(response);
      });
      updateBillingInfo.catch(function(data,status){
      console.log(data);

      SweetAlert.swal("somethings going wrong",data.data.statusText, "error");
      return;
      })

                }

            }        };

               $scope.bankForm = {

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

                  var updateBankDetails =userService.updateBankDetails(form);
      updateBankDetails.then(function(response){
      SweetAlert.swal(response.data.message, "success");
     
      console.log(response);
      });
      updateBankDetails.catch(function(data,status){
      console.log(data);

      SweetAlert.swal("somethings going wrong",data.data.statusText, "error");
      return;
      })

                }

            }        };

            var uploaderImages = $scope.uploaderImages = new FileUploader({
        url: 'api/v1/vendor/images',
        alias : 'image_name',
        removeAfterUpload: true,
        queueLimit : 3
    });

    // FILTERS

    uploaderImages.filters.push({
        name: 'imageFilter',
        fn: function (item/*{File|FileLikeObject}*/, options) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
    });

    // CALLBACKS

    // uploaderImages.onWhenAddingFileFailed = function (item/*{File|FileLikeObject}*/, filter, options) {
    //     console.info('onWhenAddingFileFailed', item, filter, options);
    // };
    // uploaderImages.onAfterAddingFile = function (fileItem) {
    //     console.info('onAfterAddingFile', fileItem);
    // };
    // uploaderImages.onAfterAddingAll = function (addedFileItems) {
    //     console.info('onAfterAddingAll', addedFileItems);
    // };
    // uploaderImages.onBeforeUploadItem = function (item) {
    //     console.info('onBeforeUploadItem', item);
    // };
    // uploaderImages.onProgressItem = function (fileItem, progress) {
    //     console.info('onProgressItem', fileItem, progress);
    // };
    // uploaderImages.onProgressAll = function (progress) {
    //     console.info('onProgressAll', progress);
    // };
    // uploaderImages.onSuccessItem = function (fileItem, response, status, headers) {
    //     console.info('onSuccessItem', fileItem, response, status, headers);
    // };
    uploaderImages.onErrorItem = function (fileItem, response, status, headers) {
        console.info('onErrorItem', fileItem, response, status, headers);
        SweetAlert.swal("somethings going wrong",response.message, "error");
        return;
    };
    // uploaderImages.onCancelItem = function (fileItem, response, status, headers) {
    //     console.info('onCancelItem', fileItem, response, status, headers);
    // };
    uploaderImages.onCompleteItem = function (fileItem, response, status, headers) {
        console.info('onCompleteItem', fileItem, response, status, headers);
        $scope.getVendorImages();
    };
    uploaderImages.onCompleteAll = function () {
        console.info('onCompleteAll');
    };

    console.info('uploader', uploaderImages);

    $scope.getVendorImages = function(){
      userService.getVendorImages().then(function(images){
$scope.images=images.images || {};  
// $scope.uploaderImages.queue.length = $scope.images.length;
console.log($scope.images);
}).catch(function(response){
  console.log(response);
  $scope.images = {};
});  
    }
    $scope.removeImage = function(imageId) {
userService.deleteVendorImage(imageId).then(function(images){
  $scope.getVendorImages();
}).catch(function(response){
  console.log(response);
  $scope.images = {};
}); 
    }
    $scope.getVendorImages();
}]);