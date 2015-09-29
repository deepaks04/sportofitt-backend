'use strict';
/** 
  * controller for User Profile Example
*/
app.controller('ProfileCtrl', ["$scope", "flowFactory","userService","SweetAlert",
 function ($scope, flowFactory,userService,SweetAlert) {
    $scope.removeImage = function () {
        $scope.noImage = true;
    };
    $scope.obj = new Flow();

 userService.getVendorProfile().then(function(userInfo){
$scope.userInfo=userInfo;	
$scope.userInfo.profile_picture = {};
    // if ($scope.userInfo.profile_picture == '') {
        $scope.noImage = true;
    // }

});

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

                  var updateProfile =userService.updateUserInfo($scope.userInfo);
      updateProfile.then(function(response){
      SweetAlert.swal(response.data.message, "success");
     
      console.log(response);
      });
      updateProfile.catch(function(data,status){
      console.log(data);

      SweetAlert.swal(data.message,data.statusText, "error");
      return;
      })

                }

            },
            reset: function (form) {

                $scope.userInfo = angular.copy($scope.master);
                form.$setPristine(true);

            }
        };
}]);