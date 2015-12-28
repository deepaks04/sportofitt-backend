"use strict";


app.factory('SessionService', function ($cookieStore) {
return{
get:function(key){
    return $cookieStore.get(key);
},
set:function(key,val){
    return $cookieStore.put(key, val);
},
unset:function(key){
    return $cookieStore.remove(key);
}
}
});
