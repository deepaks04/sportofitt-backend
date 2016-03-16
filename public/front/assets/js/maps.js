"use strict";
var $ = jQuery.noConflict();

var mapStyles = [ {"featureType":"road","elementType":"labels","stylers":[{"visibility":"simplified"},{"lightness":20}]},{"featureType":"administrative.land_parcel","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"all","stylers":[{"saturation":-100},{"visibility":"on"},{"lightness":10}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":50}]},{"featureType":"water","elementType":"all","stylers":[{"hue":"#a1cdfc"},{"saturation":30},{"lightness":49}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"hue":"#f49935"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"hue":"#fad959"}]}, {featureType:'road.highway',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:60},{visibility:'on'}]}, {featureType:'landscape.natural',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-71},{lightness:-18},{visibility:'on'}]},  {featureType:'poi',elementType:'all',stylers:[{hue:'#d9d5cd'},{saturation:-70},{lightness:20},{visibility:'on'}]} ];

// Set map height to 100% ----------------------------------------------------------------------------------------------

var $body = $('body');
if( $body.hasClass('map-fullscreen') ) {
    if( $(window).width() > 768 ) {

        $('.map-canvas').height( $(window).height() - $('.header').height() );
    }
    else {
        $('.map-canvas #map').height( $(window).height() - $('.header').height() );
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Homepage map - Google
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OpenStreetMap - Homepage
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function createHomepageOSM(_latitude,_longitude,json,mapProvider){

    $.get("assets/external/_infobox.js", function() {
        osmMap();
    });

    function osmMap(){
        var map = L.map('map', {
                center: [_latitude,_longitude],
                zoom: 14,
                scrollWheelZoom: false
        });

        L.tileLayer.provider(mapProvider).addTo(map);

        var markers = L.markerClusterGroup({
            showCoverageOnHover: false,
            zoomToBoundsOnClick: false
        });

        var loadedMarkers = [];

        // Create markers on the map -----------------------------------------------------------------------------------

        for (var i = 0; i < json.data.length; i++) {

            // Set icon for marker -------------------------------------------------------------------------------------

            if( json.data[i].type_icon ) var icon = '<img src="' + json.data[i].type_icon +  '">';
            else icon = '';

            if( json.data[i].color ) var color = json.data[i].color;
            else color = '';

            var markerContent =
                '<div class="map-marker ' + color + '">' +
                    '<div class="icon">' +
                    icon +
                    '</div>' +
                '</div>';

            var _icon = L.divIcon({
                html: markerContent,
                iconSize:     [36, 46],
                iconAnchor:   [18, 32],
                popupAnchor:  [130, -28],
                className: ''
            });

            var title = json.data[i].title;
            var marker = L.marker(new L.LatLng( json.data[i].latitude, json.data[i].longitude ), {
                title: title,
                icon: _icon
            });

            loadedMarkers.push(marker);

            // Infobox HTML element ------------------------------------------------------------------------------------

            var category = json.data[i].category;
            var infoboxContent = document.createElement("div");
            marker.bindPopup(
                drawInfobox(category, infoboxContent, json, i)
            );
            markers.addLayer(marker);

            // Set hover states for marker -----------------------------------------------------------------------------

            marker.on('popupopen', function () {
                this._icon.className += ' marker-active';
            });
            marker.on('popupclose', function () {
                this._icon.className = 'leaflet-marker-icon leaflet-zoom-animated leaflet-clickable marker-loaded';
            });

        }

        map.addLayer(markers);

        // Animate already created markers -----------------------------------------------------------------------------

        animateOSMMarkers(map, loadedMarkers, json);
        map.on('moveend', function() {
            animateOSMMarkers(map, loadedMarkers, json);
        });

        markers.on('clusterclick', function (a) {

            var markersInCLuster = a.layer.getAllChildMarkers();
            var latitudeArray = [];
            var longitudeArray = [];

            for (var b=0; b < markersInCLuster.length; b++)
            {
                var formattedLatitude = parseFloat( markersInCLuster[b]._latlng.lat ).toFixed(6);
                var formattedLongitude = parseFloat( markersInCLuster[b]._latlng.lng ).toFixed(6);
                latitudeArray.push( formattedLatitude );
                longitudeArray.push( formattedLongitude );
            }

            Array.prototype.allValuesSame = function() {
                for(var i = 1; i < this.length; i++)
                {
                    if(this[i] !== this[0])
                        return false;
                }
                return true;
            };

            if( latitudeArray.allValuesSame() && longitudeArray.allValuesSame() ){
                multiChoice(latitudeArray[0], longitudeArray[0], json);
            }
            else {
                a.layer.zoomToBounds();
            }
        });

        $('.results .item').hover(
            function(){
                loadedMarkers[ $(this).attr('id') - 1 ]._icon.className = 'leaflet-marker-icon leaflet-zoom-animated leaflet-clickable marker-loaded marker-active';
            },
            function() {
                loadedMarkers[ $(this).attr('id') - 1 ]._icon.className = 'leaflet-marker-icon leaflet-zoom-animated leaflet-clickable marker-loaded';
            }
        );


        $('.geolocation').on("click", function() {
            map.locate({setView : true})
        });

        $('body').addClass('loaded');
        setTimeout(function() {
            $('body').removeClass('has-fullscreen-map');
        }, 1000);
        $('#map').removeClass('fade-map');

        redrawMap('osm', map);
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Item Detail Map - Google
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function itemDetailMap(json){
    var mapCenter = new google.maps.LatLng(json.latitude,json.longitude);
    var mapOptions = {
        zoom: 14,
        center: mapCenter,
        disableDefaultUI: true,
        scrollwheel: false,
        styles: mapStyles,
        panControl: false,
        zoomControl: false,
        draggable: true
    };
    var mapElement = document.getElementById('map-detail');
    var map = new google.maps.Map(mapElement, mapOptions);
    if( json.type_icon ) var icon = '<img src="' + json.type_icon +  '">';
    else icon = '';

    // Google map marker content -----------------------------------------------------------------------------------

    var markerContent = document.createElement('DIV');
    markerContent.innerHTML =
        '<div class="map-marker">' +
            '<div class="icon">' +
            icon +
            '</div>' +
        '</div>';

    // Create marker on the map ------------------------------------------------------------------------------------

    var marker = new RichMarker({
        position: new google.maps.LatLng( json.latitude, json.longitude ),
        map: map,
        draggable: false,
        content: markerContent,
        flat: true
    });

    marker.content.className = 'marker-loaded';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Simple Google Map (contat, submit...)
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function simpleMap(_latitude, _longitude, draggableMarker){
    var mapCenter = new google.maps.LatLng(_latitude, _longitude);
    var mapOptions = {
        zoom: 14,
        center: mapCenter,
        disableDefaultUI: true,
        scrollwheel: false,
        styles: mapStyles,
        panControl: false,
        zoomControl: false,
        draggable: true
    };
    var mapElement = document.getElementById('map-simple');
    var map = new google.maps.Map(mapElement, mapOptions);

    // Google map marker content -----------------------------------------------------------------------------------

    var markerContent = document.createElement('DIV');
    markerContent.innerHTML =
        '<div class="map-marker">' +
            '<div class="icon"></div>' +
        '</div>';

    // Create marker on the map ------------------------------------------------------------------------------------

    var marker = new RichMarker({
        //position: mapCenter,
        position: new google.maps.LatLng( _latitude, _longitude ),
        map: map,
        draggable: draggableMarker,
        content: markerContent,
        flat: true
    });

    marker.content.className = 'marker-loaded';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Functions
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Push items to array and create <li> element in Results sidebar ------------------------------------------------------

function pushItemsToArray(json, a, category, visibleItemsArray){
    var itemPrice;
    visibleItemsArray.push(
        '<li>' +
            '<div class="item" id="' + json.data[a].id + '">' +
                '<a href="#" class="image">' +
                    '<div class="inner">' +
                        '<div class="item-specific">' +
                            drawItemSpecific(category, json, a) +
                        '</div>' +
                        '<img src="' + json.data[a].gallery[0] + '" alt="">' +
                    '</div>' +
                '</a>' +
                '<div class="wrapper">' +
                    '<a href="#" id="' + json.data[a].id + '"><h3>' + json.data[a].title + '</h3></a>' +
                    '<figure>' + json.data[a].location + '</figure>' +
                    drawPrice(json.data[a].price) +
                    '<div class="info">' +
                        '<div class="type">' +
                            '<i><img src="' + json.data[a].type_icon + '" alt=""></i>' +
                            '<span>' + json.data[a].type + '</span>' +
                        '</div>' +
                        '<div class="rating" data-rating="' + json.data[a].rating + '"></div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</li>'
    );

    function drawPrice(price){
        if( price ){
            itemPrice = '<div class="price">' + price +  '</div>';
            return itemPrice;
        }
        else {
            return '';
        }
    }
}

// Center map to marker position if function is called (disabled) ------------------------------------------------------

function centerMapToMarker(){
    $.each(json.data, function(a) {
        if( json.data[a].id == id ) {
            var _latitude = json.data[a].latitude;
            var _longitude = json.data[a].longitude;
            var mapCenter = new google.maps.LatLng(_latitude,_longitude);
            map.setCenter(mapCenter);
        }
    });
}

// Create modal if more items are on the same location (example: one building with floors) -----------------------------

function multiChoice(sameLatitude, sameLongitude, json) {
    //if (clickedCluster.getMarkers().length > 1){
        var multipleItems = [];
        $.each(json.data, function(a) {
            if( json.data[a].latitude == sameLatitude && json.data[a].longitude == sameLongitude ) {
                pushItemsToArray(json, a, json.data[a].category, multipleItems);
            }
        });
        $('body').append('<div class="modal-window multichoice fade_in"></div>');
        $('.modal-window').load( 'assets/external/_modal-multichoice.html', function() {
            $('.modal-window .modal-wrapper .items').html( multipleItems );
            rating('.modal-window');
        });
        $('.modal-window .modal-background, .modal-close').live('click',  function(e){
            $('.modal-window').addClass('fade_out');
            setTimeout(function() {
                $('.modal-window').remove();
            }, 300);
        });
    //}
}

// Animate OSM marker --------------------------------------------------------------------------------------------------

function animateOSMMarkers(map, loadedMarkers, json){
    var bounds = map.getBounds();
    var visibleItemsArray = [];
    var multipleItems = [];

    $.each( loadedMarkers, function (i) {
        if ( bounds.contains( loadedMarkers[i].getLatLng() ) ) {
            var category = json.data[i].category;
            pushItemsToArray(json, i, category, visibleItemsArray);

            setTimeout(function(){
                if( loadedMarkers[i]._icon != null ){
                    loadedMarkers[i]._icon.className = 'leaflet-marker-icon leaflet-zoom-animated leaflet-clickable bounce-animation marker-loaded';
                }
            }, i* 50);
        }
        else {
            if( loadedMarkers[i]._icon != null ){
                loadedMarkers[i]._icon.className = 'leaflet-marker-icon leaflet-zoom-animated leaflet-clickable';
            }
        }
    });

    // Create list of items in Results sidebar -------------------------------------------------------------------------

    $('.items-list .results').html( visibleItemsArray );

    rating('.results .item');

}

// Redraw map after item list is closed --------------------------------------------------------------------------------

function redrawMap(mapProvider, map){
    $('.map .toggle-navigation').click(function() {
        $('.map-canvas').toggleClass('results-collapsed');
        $('.map-canvas .map').one("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){
            if( mapProvider == 'osm' ){
                map.invalidateSize();
            }
            else if( mapProvider == 'google' ){
                google.maps.event.trigger(map, 'resize');
            }
        });
    });
}