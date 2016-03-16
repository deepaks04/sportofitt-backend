'use strict';

/**
 * @ngdoc overview
 * @name sportofittApp
 * @description
 * # sportofittApp
 *
 * Main module of the application.
 */
angular
  .module('sportofittApp', [
      'ngAnimate',
      'ngCookies',
      'ngStorage',
      'ngSanitize',
      'ngTouch',
      'ui.router',
      'ui.bootstrap',
      'oc.lazyLoad',
      'cfp.loadingBar',
      'ncy-angular-breadcrumb',
      'duScroll',
      'pascalprecht.translate', 'satellizer', 'toastr','sportofittApp.config'
  ]).config(function(toastrConfig,$authProvider,myConfig) {
      angular.extend(toastrConfig, {
            autoDismiss: false,
            containerId: 'toast-container',
            maxOpened: 0,
            newestOnTop: true,
            positionClass: 'toast-top-right',
            preventDuplicates: false,
            preventOpenDuplicates: true,
            target: 'body'
      });

      // Satellizer configuration that specifies which API
      // route the JWT should be retrieved from
      $authProvider.loginUrl = myConfig.authorizer;
}).run(function ($rootScope,$auth) {
      $rootScope.isAuthenticated = function() {
            return $auth.isAuthenticated();
      };
});
