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
},
logout:function(){
var newUser = $http.get('api/v1/user/logout');
return newUser;
}
}
});

app.factory('SessionService',function(){
return{
get:function(key){
return sessionStorage.getItem(key);
},
set:function(key,val){
return sessionStorage.setItem(key,val);
},
unset:function(key){
return sessionStorage.removeItem(key);
}
}
});
