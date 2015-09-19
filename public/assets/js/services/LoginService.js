'use strict';

app.factory('Login',function($http){
return{
auth:function(credentials){
var authUser = $http.post('api/v1/user/auth',credentials);
return authUser;
},
register:function(credentials){
var newUser = $http.post('api/v1/vendor/create',credentials);
return newUser;
}
}
});
