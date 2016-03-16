'use strict';

describe('Controller: ConfirmationCtrl', function () {

  // load the controller's module
  beforeEach(module('sportofittApp'));

  var ConfirmationCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    ConfirmationCtrl = $controller('ConfirmationCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(ConfirmationCtrl.awesomeThings.length).toBe(3);
  });
});
