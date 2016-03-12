'use strict';

/**
 * @ngdoc directive
 * @name sportofittApp.directive:dropzone
 * @description
 * # dropzone
 */
angular.module('sportofittApp')
  .directive('dropzone', function () {
    return {
      restrict: 'EC',
      link: function postLink(scope, element, attrs) {
          element.dropzone({
              url: "/upload",
              maxFilesize: 100,
              paramName: "uploadfile",
              maxThumbnailFilesize: 5

          });
      }
    };
  });
