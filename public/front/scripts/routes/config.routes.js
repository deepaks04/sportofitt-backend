/**
 * Created by pramod on 6/3/16.
 */
angular.module('sportofittApp').config(function($stateProvider, $urlRouterProvider){

    $urlRouterProvider.otherwise("/");

    // Now set up the states for acsi app
    $stateProvider
        .state('home', {
            url: "/",
            views : {
                '' : {templateUrl: 'views/home/index.html'},
                'nav': { templateUrl: 'views/layouts/navbar.html',
                    controller : 'MainCtrl',
                    controllerAs : 'vm'
                },
                'footer': { templateUrl: 'views/layouts/footer.html' }
            }
        }).state('login', {
            url: "/sign-in",
            views : {
                '': {
                    templateUrl: "views/login/index.html",
                    controller: 'AuthCtrl',
                    controllerAs: 'vm'
                },
                'nav': { templateUrl: 'views/layouts/navbar.html',
                    controller : 'MainCtrl',
                    controllerAs : 'vm'
                },
                'footer': { templateUrl: 'views/layouts/footer.html' }
            }
        }).state('register', {
        url: "/register",
        views : {
            '': {
                templateUrl: "views/register/index.html",
                controller: 'RegisterCtrl',
                controllerAs: 'vm'
            },
            'nav': { templateUrl: 'views/layouts/navbar.html',
                controller : 'MainCtrl',
                controllerAs : 'vm'
            },
            'footer': { templateUrl: 'views/layouts/footer.html' }
        }
    }).state('logout', {
            url: "/logout",
            controller: function ($state, $auth) {
                $auth.logout();
                $state.go('login');
            }
        });

})