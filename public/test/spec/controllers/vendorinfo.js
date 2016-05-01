'use strict';

describe('Controller: VendorinfoCtrl', function () {

  // load the controller's module
  beforeEach(module('sportofittApp'));

  var VendorinfoCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    VendorinfoCtrl = $controller('VendorinfoCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(VendorinfoCtrl.awesomeThings.length).toBe(3);
  });
});
