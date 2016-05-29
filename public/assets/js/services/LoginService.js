'use strict';
app.factory('SessionService', function () {
    return {
        get: function (key) {
            return sessionStorage.getItem(angular.fromJson(key));
        },
        set: function (key, val) {
            return sessionStorage.setItem(key, JSON.stringify(val));
        },
        unset: function (key) {
            return sessionStorage.removeItem(key);
        }
    }
});
app.factory('Login', function ($http, $rootScope, $cookieStore,$cookies, SessionService, $state,config) {
    return {
        auth: function (credentials) {
            var request = $http.post(config.backend + 'user/auth', credentials);
            // Store the data-dump of the FORM scope.
            request.success(
                function (response, status, headers, config) {
                    $rootScope.user = response.user;

                    //SessionService.set('auth', $rootScope.user);
                    $cookies.putObject('auth', $rootScope.user);

                    $state.go(response.user.role + '.dashboard', {'name': response.user.extra.business_name});

                }
            );
            return request;
        },
        register: function (credentials) {
            var newUser = $http.post(config.backend + 'vendor/create', credentials);
            return newUser;
        },
        forgetPassword: function (credentials) {
            return $http.post(config.backend + 'user/password/email', credentials);
        },
        getResetPassword: function (credentials) {
            return $http.get(config.backend + 'user/password/reset/' + credentials);
        },
        resetPassword: function (credentials) {
            return $http.post(config.backend + 'user/password/reset', credentials);
        },
        logout: function () {
            var request = $http.get(config.backend + 'user/logout');
            request.success(
                function (response, status, headers, config) {
                    SessionService.unset('auth');
                    $rootScope.user = {};
                    $cookieStore.remove('auth');
                    return {success: true};
                }
            );
            // fire errors
            request.error(function (data, status, headers, config) {
                callback({success: false});
            });
            return request;
        }
    };
});


