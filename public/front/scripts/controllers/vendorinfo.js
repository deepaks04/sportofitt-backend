'use strict';

/**
 * @ngdoc function
 * @name sportofittApp.controller:VendorinfoCtrl
 * @description
 * # VendorinfoCtrl
 * Controller of the sportofittApp
 */
angular.module('sportofittApp')
  .controller('VendorInfoCtrl', function ($stateParams,searchService,toastr) {


      var vm = this;
      vm.vendorId = $stateParams.vendorId;
      vm.init = function(){
          searchService.getVendorById(vm.vendorId).then(function(response){
             vm.vendor = response.data.data.vendor;
              initializeOwl();
              console.log(vm.vendor);
          }).catch(function(response){
              toastr.error(response);
          })
      }
// Initialize Owl carousel ---------------------------------------------------------------------------------------------

      function initializeOwl(_rtl){
          $.getScript( "assets/js/owl.carousel.min.js", function( data, textStatus, jqxhr ) {
              if ($('.owl-carousel').length > 0) {
                  if ($('.carousel-full-width').length > 0) {
                      setCarouselWidth();
                  }
                  $(".carousel.wide").owlCarousel({
                      rtl: _rtl,
                      items: 1,
                      responsiveBaseWidth: ".slide",
                      nav: true,
                      navText: ["",""]
                  });
                  $(".item-slider").owlCarousel({
                      rtl: _rtl,
                      items: 1,
                      autoHeight: true,
                      responsiveBaseWidth: ".slide",
                      nav: false,
                      callbacks: true,
                      URLhashListener: true,
                      navText: ["",""]
                  });
                  $(".list-slider").owlCarousel({
                      rtl: _rtl,
                      items: 1,
                      responsiveBaseWidth: ".slide",
                      nav: true,
                      navText: ["",""]
                  });
                  $(".testimonials").owlCarousel({
                      rtl: _rtl,
                      items: 1,
                      responsiveBaseWidth: "blockquote",
                      nav: true,
                      navText: ["",""]
                  });

                  $('.item-gallery .thumbnails a').on('click', function(){
                      $('.item-gallery .thumbnails a').each(function(){
                          $(this).removeClass('active');
                      });
                      $(this).addClass('active');
                  });
                  $('.item-slider').on('translated.owl.carousel', function(event) {
                      var thumbnailNumber = $('.item-slider .owl-item.active img').attr('data-hash');
                      $( '.item-gallery .thumbnails #thumbnail-' + thumbnailNumber ).trigger('click');
                  });
                  return false;
              }
          });
      }

      vm.init();
  });
