'use strict';
app.factory('SessionService',function(){
return{
get:function(key){
return sessionStorage.getItem(angular.fromJson(key));
},
set:function(key,val){
return sessionStorage.setItem(key,JSON.stringify(val));
},
unset:function(key){
return sessionStorage.removeItem(key);
}
}
});
app.factory('Login',function($http,$rootScope,$cookieStore,SessionService){
return{
auth:function(credentials){
var request = $http.post('api/v1/user/auth',credentials);
  // Store the data-dump of the FORM scope.
                request.success(
                    function( response ,status, headers, config) {   
                     console.log(response);
                           $rootScope.user = response.user;

			SessionService.set('auth',$rootScope.user);
            $cookieStore.put('user', $rootScope.user);
            
            return { success: true };
                    }
                );
                
                // fire errors
                request.error(function (data, status, headers, config) {
                        callback({ success: false });
                });
return request;
},
register:function(credentials){
var newUser = $http.post('api/v1/vendor/create',credentials);
return newUser;
},
logout:function(){
var request = $http.get('api/v1/user/logout');
 request.success(
                    function( response ,status, headers, config) {     
	SessionService.unset('auth');
            $rootScope.user = {};
            $cookieStore.remove('user');
          return {success : true};
                    }
                );
                
                // fire errors
                request.error(function (data, status, headers, config) {
                        callback({ success: false });
                });
return request;
}
}
});


