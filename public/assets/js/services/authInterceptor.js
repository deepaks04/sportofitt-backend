'use stict';

app.factory('authInterceptor', [
    '$rootScope', '$q',
    function($rootScope, $q,$state) {
        return {
            // Intercept 401s and redirect you to login
            responseError: function(response) {
                if (response.status === 401) {
                    $rootScope.$broadcast('auth.request.error');
                  //  $state.go('app.login');
                    return $q.reject(response);
                } else {
                    return $q.reject(response);
                }
            }
        };
    }
]);
