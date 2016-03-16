'use strict';

/**
 * Config for the router
 */
app.config(['$stateProvider', '$urlRouterProvider', '$controllerProvider', '$compileProvider', '$filterProvider', '$provide', '$ocLazyLoadProvider', 'JS_REQUIRES','$httpProvider',
    function ($stateProvider, $urlRouterProvider, $controllerProvider, $compileProvider, $filterProvider, $provide, $ocLazyLoadProvider, jsRequires,$httpProvider) {

        app.controller = $controllerProvider.register;
        app.directive = $compileProvider.directive;
        app.filter = $filterProvider.register;
        app.factory = $provide.factory;
        app.service = $provide.service;
        app.constant = $provide.constant;
        app.value = $provide.value;

        // LAZY MODULES

        $ocLazyLoadProvider.config({
            debug: true,
            events: true,
            modules: jsRequires.modules
        });

        // APPLICATION ROUTES
        // -----------------------------------
        // For any unmatched url, redirect to /app/dashboard
        //$urlRouterProvider.otherwise("/");
        //
        $stateProvider

                //vendor routes
                .state('vendor', {
                    url: "/:name",
                    templateUrl: "assets/views/vendor/app.html",
                    resolve: loadSequence('modernizr', 'logger', 'moment', 'angularMoment', 'uiSwitch', 'perfect-scrollbar-plugin', 'toaster', 'ngAside', 'sweet-alert', 'oitozero.ngSweetAlert'),
                    abstract: true
                }).state('vendor.dashboard', {
            url: "/dashboard",
            templateUrl: "assets/views/vendor/dashboard.html",
            resolve: loadSequence('jquery-sparkline', 'dashboardCtrl'),
            title: 'Dashboard',
            ncyBreadcrumb: {
                label: 'Dashboard'
            }}).state('vendor.setting', {
            url: '/setting',
            templateUrl: "assets/views/vendor/profile.html",
            title: 'Vendor Profile Setting',
            ncyBreadcrumb: {
                label: 'Vendor Profile Setting'
            },
            resolve: loadSequence('flow', 'angularFileUpload', 'userService','logger','uiMap', 'vendorCtrl')
        }).state('vendor.facility', {
            url: '/facility',
            template: '<div ui-view class="fade-in-up"></div>',
            resolve: loadSequence('ngTable', 'xeditable', 'facilityService', 'facilityCtrl'),
            title: 'Venue',
            ncyBreadcrumb: {
                label: 'Venue'
            }}).state('vendor.facility.list', {
            url: '/list',
            templateUrl: "assets/views/vendor/facility/list.html",
            title: 'Venue List',
            ncyBreadcrumb: {
                label: 'Venue List'
            }
        }).state('vendor.facility.add', {
            url: '/add',
            templateUrl: "assets/views/vendor/facility/add.html",
            title: 'Venue Add',
            ncyBreadcrumb: {
                label: 'Venue Add'
            },
            resolve: loadSequence('ui.select', 'monospaced.elastic', 'ui.mask', 'touchspin-plugin', 'selectCtrl')
        }).state('vendor.facility.calender', {
            url: '/calender/:facilityId',
            templateUrl: "assets/views/vendor/facility/booking.html",
            title: 'Venue Booking',
            ncyBreadcrumb: {
                label: 'Venue Booking'
            },
            resolve: loadSequence('moment', 'mwl.calendar')
        }).state('login', {
            url: '/',
            template: '<div ui-view class="fade-in-right-big smooth"></div>',
            resolve: loadSequence('sweet-alert', 'oitozero.ngSweetAlert', 'loginService', 'loginCtrl'),
            abstract: true,
            data: {
                noLogin: !0
            }
        }).state('login.signin', {
            url: '',
            templateUrl: "assets/views/login_login.html",
            title: 'Sign-In',
            data: {
                noLogin: !0
            }
        }).state('login.forgot', {
            url: 'forgot',
            templateUrl: "assets/views/login_forgot.html",
            data: {
                noLogin: !0
            }
        }).state('login.registration', {
            url: 'registration',
            templateUrl: "assets/views/login_registration.html",
            data: {
                noLogin: !0
            }
        }).state('login.resetpassword', {
            url: 'reset-password/:token',
            templateUrl: "assets/views/login_reset_password.html",
            data: {
                noLogin: !0
            }
        }).state('login.logout', {
            url: 'logout',
            controller: function (Login, $state, SessionService) {
                Login.logout().then(function () {
                    SessionService.unset('auth');
                    $state.go('login.signin');
                });
            },
            data: {
                noLogin: !0
            }
        });

        $httpProvider.interceptors.push('authInterceptor');
        // Generates a resolve object previously configured in constant.JS_REQUIRES (config.constant.js)
        function loadSequence() {
            var _args = arguments;
            return {
                deps: ['$ocLazyLoad', '$q',
                    function ($ocLL, $q) {
                        var promise = $q.when(1);
                        for (var i = 0, len = _args.length; i < len; i++) {
                            promise = promiseThen(_args[i]);
                        }
                        return promise;

                        function promiseThen(_arg) {
                            if (typeof _arg == 'function')
                                return promise.then(_arg);
                            else
                                return promise.then(function () {
                                    var nowLoad = requiredData(_arg);
                                    if (!nowLoad)
                                        return $.error('Route resolve: Bad resource name [' + _arg + ']');
                                    return $ocLL.load(nowLoad);
                                });
                        }

                        function requiredData(name) {
                            if (jsRequires.modules)
                                for (var m in jsRequires.modules)
                                    if (jsRequires.modules[m].name && jsRequires.modules[m].name === name)
                                        return jsRequires.modules[m];
                            return jsRequires.scripts && jsRequires.scripts[name];
                        }
                    }]
            };
        }
    }]);
