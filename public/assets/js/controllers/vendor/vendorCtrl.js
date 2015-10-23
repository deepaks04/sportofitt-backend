'use strict';
/** 
  * controller for User Profile Example
*/
app.controller('ProfileCtrl', ["$scope","$timeout", "flowFactory","userService","SweetAlert",'FileUploader',
 function ($scope,$timeout, flowFactory,userService,SweetAlert,FileUploader) {



    $scope.removeProfileImage = function () {
        $scope.userInfo.profile_picture = "";
        $scope.noImage = true;
    };
    $scope.obj = new Flow();

    $scope.bankDetail = {};
 userService.getBillingInfo().then(function(billingInfo){
 $scope.billingInfo=billingInfo.billing;	
});
 userService.getVendorProfile().then(function(userInfo){
$scope.userInfo=userInfo.profile;	
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

  
  //map
$scope.setMap = function (){
  $scope.map = { 
   show: true,
      control: {},
      version: "uknown",
      heatLayerCallback: function (layer) {
        //set the heat layers backend data
        var mockHeatLayer = new MockHeatLayer(layer);
      },
      showTraffic: true,
      showBicycling: false,
      showWeather: false,
      showHeat: false,
      center: {
        latitude: $scope.userInfo.latitude || 18.52,
        longitude: $scope.userInfo.longitude || 73.82
      },
      options: {
        streetViewControl: false,
        panControl: false,
        maxZoom: 20,
        minZoom: 3
      },
      zoom: 20,
      dragging: false,
      bounds: {},
        markers2: [
             {
          id: 2,
         // icon: 'assets/images/blue_marker.png',
          latitude: $scope.userInfo.latitude || 18.52,
          longitude: $scope.userInfo.longitude || 73.82,
          showWindow: false,
          options: {
            labelContent: 'DRAG ME!',
            labelAnchor: "22 0",
            labelClass: "marker-labels",
            draggable: true
          }
        }
        ]
      
       };

        $scope.map.markers2Events = {
    dragend: function (marker, eventName, model, args) {
      model.options.labelContent = "Dragged lat: " + model.latitude + " lon: " + model.longitude;
      $scope.userInfo.latitude = model.latitude;
      $scope.userInfo.longitude = model.longitude;
    }
  };
}

  $scope.form = {

            submit: function (form) {
              if(form.profile_picture === $scope.userInfo.profile_picture){
                $scope.userInfo.profile_picture = "";
              }
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
              

                  var updateBillingInfo =userService.updateBillingInfo(form);
      updateBillingInfo.then(function(response){
      SweetAlert.swal(response.data.message, "success");
     
      console.log(response);
      });
      updateBillingInfo.catch(function(data,status){
      console.log(data);
  $scope.errors = {};
   angular.forEach(data.data,function(errors,field){
          
  $scope.errors[field] = errors.join(', ');
   });
      })

                }
                     };

               $scope.bankForm = {

            submit: function (form) {
            	
                var firstError = null;
                

                  var updateBankDetails =userService.updateBankDetails(form);
      updateBankDetails.then(function(response){
  $scope.errors = {};
      SweetAlert.swal(response.data.message, "success");
     
      console.log(response);
      });
      updateBankDetails.catch(function(data,status){
      console.log(data);
 $scope.errors = {};
   angular.forEach(data.data,function(errors,field){
          
  $scope.errors[field] = errors.join(', ');
   });
      })

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