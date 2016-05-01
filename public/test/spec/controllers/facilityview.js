'use strict';

describe('Controller: FacilityviewCtrl', function () {

  // load the controller's module
  beforeEach(module('sportofittApp'));

  var FacilityviewCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    FacilityviewCtrl = $controller('FacilityviewCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(FacilityviewCtrl.awesomeThings.length).toBe(3);
  });
});
