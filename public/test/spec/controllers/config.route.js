'use strict';

describe('Controller: ConfigRouteCtrl', function () {

  // load the controller's module
  beforeEach(module('sportofittApp'));

  var ConfigRouteCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    ConfigRouteCtrl = $controller('ConfigRouteCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(ConfigRouteCtrl.awesomeThings.length).toBe(3);
  });
});
