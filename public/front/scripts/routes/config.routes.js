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
                    '': {templateUrl: 'views/main.html'},
                    'nav@app': {templateUrl: 'views/layouts/navbar.html'},
                    'footer@app': {templateUrl: 'views/layouts/footer.html'}
                }
            }).state('app.home', {
        url: "",
        views: {
            'body@app': {templateUrl: 'views/home/index.html',
                controller: 'MapCtrl',
                controllerAs: 'vm'}
        }
    }).state('app.login', {
        url: "sign-in",
        views: {
            'body@app': {
                templateUrl: "views/login/index.html",
                controller: 'AuthCtrl',
                controllerAs: 'vm'
            }

        }
    }).state('app.confirmation', {
        url: "confirmation/:token",
        views: {
            'body@app': {
                templateUrl: "views/confirmation/index.html",
                controller: 'ConfirmationCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.register', {
        url: "register",
        views: {
            'body@app': {
                templateUrl: "views/register/index.html",
                controller: 'RegisterCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.profile', {
        url: "profile",
        views: {
            'body@app': {
                templateUrl: "views/profile/index.html",
                controller: 'ProfileCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.vendor', {
        url: "vendor/{vendorId}",
        views: {
            'body@app': {
                templateUrl: "views/vendor/index.html",
                controller: 'VendorInfoCtrl',
                controllerAs: 'vm'
            }
        }
    }).state('app.logout', {
        url: "logout",
        controller: function ($state, $auth) {
            $auth.logout();
            $state.go('login');
        }
    });

})