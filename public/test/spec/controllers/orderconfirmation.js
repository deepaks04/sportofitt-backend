'use strict';

describe('Controller: OrderconfirmationCtrl', function () {

  // load the controller's module
  beforeEach(module('sportofittApp'));

  var OrderconfirmationCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    OrderconfirmationCtrl = $controller('OrderconfirmationCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(OrderconfirmationCtrl.awesomeThings.length).toBe(3);
  });
});
