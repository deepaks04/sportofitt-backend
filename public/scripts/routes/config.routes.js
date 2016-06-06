/**
 * Created by pramod on 6/3/16.
 */
angular.module('sportofittApp').config(function ($stateProvider, $urlRouterProvider) {

    $urlRouterProvider.otherwise("/");

    // Now set up the states for acsi app
    $stateProvider
            .state('app', {
                url: "/",
                abstract:true,
                views: {
                    '': {templateUrl: 'front/views/main.html'},
                    'nav@app': {templateUrl: 'front/views/layouts/navbar.html'},
                    'footer@app': {templateUrl: 'front/views/layouts/footer.html'}
                }
            }).state('app.home', {
        url: "",
        views: {
            'body@app': {templateUrl: 'front/views/home/index.html',
               controller: 'HomeCtrl',
                controllerAs: 'vm'}
        }
    }).state('app.listings', {
        url: "listings",
        params: {
            category: '',
            area : '',
            type : ''
        },
        views: {
            'body@app': {
                templateUrl: "front/views/listings/index.html",
                controller: 'MapCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.login', {
        url: "sign-in",
        views: {
            'body@app': {
                templateUrl: "front/views/login/index.html",
                controller: 'AuthCtrl',
                controllerAs: 'vm'
            }

        }
    }).state('app.forget-password', {
        url: "forget-password",
        views: {
            'body@app': {
                templateUrl: "front/views/login/forget-password.html",
                controller: 'AuthCtrl',
                controllerAs: 'vm'
            }

        }
    }).state('app.reset-password', {
        url: "reset-password/:token",
        views: {
            'body@app': {
                templateUrl: "front/views/login/reset-password.html",
                controller: 'AuthCtrl',
                controllerAs: 'vm'
            }

        }
    }).state('app.confirmation', {
        url: "confirmation/:token",
        views: {
            'body@app': {
                templateUrl: "front/views/confirmation/index.html",
                controller: 'ConfirmationCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.register', {
        url: "register",
        views: {
            'body@app': {
                templateUrl: "front/views/register/index.html",
                controller: 'RegisterCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.profile', {
        url: "profile",
        views: {
            'body@app': {
                templateUrl: "front/views/profile/index.html",
                controller: 'ProfileCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.mybookings', {
        url: "my-bookings",
        views: {
            'body@app': {
                templateUrl: "front/views/my-bookings/index.html",
                controller: 'UserbookingsCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.vendor', {
        url: "vendor/{vendorId}",
        views: {
            'body@app': {
                templateUrl: "front/views/vendor/company-details.html",
                controller: 'VendorInfoCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.venue', {
        url: "venue/{facilityId}",
        views: {
            'body@app': {
                templateUrl: "front/views/vendor/index.html",
                controller: 'FacilityViewCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.orderconformation', {
        url: "confirm-bookings",
        views: {
            'body@app': {
                templateUrl: "front/views/orderconfirmation/index.html",
                controller: 'OrderConfirmationCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.about', {
        url: "about-us",
        views: {
            'body@app': {
                templateUrl: "front/views/layouts/about-us.html",

            }
        }
    }).state('app.terms', {
        url: "terms-conditions",
        views: {
            'body@app': {
                templateUrl: "front/views/layouts/terms-conditions.html",

            }
        }
    }).state('app.privacy', {
        url: "privacy-policy",
        views: {
            'body@app': {
                templateUrl: "front/views/layouts/privacy-policy.html",

            }
        }
    }).state('app.faq', {
        url: "faq",
        views: {
            'body@app': {
                templateUrl: "front/views/layouts/faq.html",

            }
        }
    }).state('app.contact', {
        url: "contact-us",
        views: {
            'body@app': {
                templateUrl: "front/views/layouts/contact-us.html",
            }
        }
    }).state('app.logout', {
        url: "logout",
        views: {
            'body@app': {
                templateUrl: "front/views/login/index.html",
                controller: function ($rootScope,$state, $auth,$cookies) {
                $auth.logout();
                    $cookies.remove("loggedUser");

                    $rootScope.isAuthenticated = $auth.isAuthenticated();
                    $rootScope.user = {};

                $state.go('app.login');
            },
                controllerAs: 'vm'
            }

        }
    });

})