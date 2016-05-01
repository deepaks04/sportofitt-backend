/**
 * Created by pramod on 13/3/16.
 */

angular.module('sportofittApp.config', [])
    .constant('myConfig', {
        'backend' : "http://www.sportofit.in/api/v1/",
        'authorizer' : 'http://www.sportofit.in/api/v1/user/sign-in',
        //'backend' : "/api/v1/",
        //'authorizer' : 'http://www.sportofitt.in/api/v1/user/sign-in',
        'version' : "1.0"
    });