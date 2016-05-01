'use strict';

/**
 * @ngdoc directive
 * @name sportofittApp.directive:dropzone
 * @description
 * # dropzone
 */
angular.module('sportofittApp')
  .directive('dropzone', function ($auth) {
    return {
      restrict: 'EC',
      link: function postLink(scope, element, attrs) {

          var config = {
              url: "/api/v1/user/change-profile-picture",
              maxFilesize: 100,
              paramName: "profile_picture",
              maxThumbnailFilesize: 10,
              parallelUploads: 1,
              autoProcessQueue: true,
              headers : {
                  Authorization : "Bearer " + $auth.getToken()
              }
          };

          var eventHandlers = {
              'addedfile': function(file) {
                  scope.file = file;
                  if (this.files[1]!=null) {
                      this.removeFile(this.files[0]);
                  }
                  scope.$apply(function() {
                      scope.fileAdded = true;
                  });
              },

              'success': function (file, response) {
              }

          };

          var dropzone = new Dropzone(element[0], config);

          angular.forEach(eventHandlers, function(handler, event) {
              dropzone.on(event, handler);
          });

          scope.processDropzone = function() {
              dropzone.processQueue();
          };

          scope.resetDropzone = function() {
              dropzone.removeAllFiles();
          }
      }

    };
  });
