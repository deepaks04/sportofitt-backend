var app = angular.module('sportofitApp', ['sportofit']);
app.run(['$rootScope', '$state', '$stateParams', "$cookieStore", "$location",
    function ($rootScope, $state, $stateParams, $cookieStore, $location) {

        // Attach Fastclick for eliminating the 300ms delay between a physical tap and the firing of a click event on mobile browsers
        FastClick.attach(document.body);

        // Set some reference to access them from any scope
        $rootScope.$state = $state;
        $rootScope.$stateParams = $stateParams;

        // GLOBAL APP SCOPE
        // set below basic information
        $rootScope.app = {
            name: 'SPORTOFIT', // name of your project
            author: 'Aquanta Consulting Services LLP', // author's name or company name
            description: 'Book GYM', // brief description
            version: '2.0', // current version
            year: ((new Date()).getFullYear()), // automatic current year (for copyright information)
            isMobile: (function () {// true if the browser is a mobile device
                var check = false;
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    check = true;
                }
                return check;
            })(),
            layout: {
                isNavbarFixed: true, //true if you want to initialize the template with fixed header
                isSidebarFixed: true, // true if you want to initialize the template with fixed sidebar
                isSidebarClosed: false, // true if you want to initialize the template with closed sidebar
                isFooterFixed: false, // true if you want to initialize the template with fixed footer
                theme: 'theme-2', // indicate the theme chosen for your project
                logo: 'assets/images/logo.png', // relative path of the project logo
            }
        };


        $rootScope.$on('$locationChangeStart', function (event, current, previous) {
            var auth = $cookieStore.get('auth');
            //event.preventDefault();
            //console.log($state.includes('login'));
            var currentLocation = $location.path().split('/') || '';
            var publicUrls = ['', 'forgot', 'registration', 'signin', 'reset-password', 'logout'];
//console.log(auth);
            if (!auth && ($.inArray(currentLocation[2], publicUrls) === -1)) {
                if (currentLocation[2] !== 'reset-password' && currentLocation[3] !== undefined) {
                    $location.path('/').replace();
                }
            } else {
                $rootScope.user = auth;
            }

        });

        $rootScope.$on('$routeChangeError', function (event, current, previous, rejection) {

            $location.path('/select').replace();

        });

    }]);
// translate config
app.config(['$translateProvider',
    function ($translateProvider) {

        // prefix and suffix information  is required to specify a pattern
        // You can simply use the static-files loader with this pattern:
        $translateProvider.useStaticFilesLoader({
            prefix: 'assets/i18n/',
            suffix: '.json'
        });

        // Since you've now registered more then one translation table, angular-translate has to know which one to use.
        // This is where preferredLanguage(langKey) comes in.
        $translateProvider.preferredLanguage('en');

        // Store the language in the local storage
        $translateProvider.useLocalStorage();

        // Enable sanitize
        $translateProvider.useSanitizeValueStrategy('sanitize');

    }]);
// Angular-Loading-Bar
// configuration
app.config(['cfpLoadingBarProvider',
    function (cfpLoadingBarProvider) {
        cfpLoadingBarProvider.includeBar = true;
        cfpLoadingBarProvider.includeSpinner = false;

    }]);
