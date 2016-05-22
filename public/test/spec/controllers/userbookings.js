'use strict';

describe('Controller: UserbookingsCtrl', function () {

  // load the controller's module
  beforeEach(module('publicApp'));

  var UserbookingsCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    UserbookingsCtrl = $controller('UserbookingsCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(UserbookingsCtrl.awesomeThings.length).toBe(3);
  });
});
