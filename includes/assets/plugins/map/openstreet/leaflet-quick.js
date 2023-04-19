$(document).ready(function () {
    if (document.getElementById("map") !== null) {
        if ($(window).width() < 992) {
            var scrollEnabled = false;
        } else {
            var scrollEnabled = true;
        }
        var mapOptions = {gestureHandling: scrollEnabled,}
        window.quickmap = L.map('map', mapOptions);
        $('#scrollEnabling').hide();

        function locationData(jobURL, companyLogo, companyName, jobTitle, verifiedBadge) {
            return ('' +
                '<a href="' + jobURL + '" class="job-listing">' +
                '<div class="job-listing-details">' +
                '<div class="job-listing-company-logo">' +
                '<div class="' + verifiedBadge + '-badge"></div>' +
                '<img src="' + companyLogo + '" alt="">' +
                '</div>' +
                '<div class="job-listing-description">' +
                '<h4 class="job-listing-company">' + companyName + '</h4>' +
                '<h3 class="job-listing-title">' + jobTitle + '</h3>' +
                '</div>' +
                '</div>' +
                '</a>')
        }

        var locations = [[locationData('single-job-page.html', 'images/company-logo-01.png', "Hexagon", 'Bilingual Event Support Specialist', 'verified'), 37.788181, -122.461270, 5, '']];

        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: " &copy;  <a href='https://www.mapbox.com/about/maps/'>Mapbox</a> &copy;  <a href='http://www.openstreetmap.org/copyright'>OpenStreetMap</a>",
            maxZoom: 18,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: openstreet_access_token
        }).addTo(quickmap);
        markers = L.markerClusterGroup({spiderfyOnMaxZoom: true, showCoverageOnHover: false,});
        for (var i = 0; i < locations.length; i++) {
            var quickIcon = L.divIcon({
                iconAnchor: [0, 0],
                popupAnchor: [0, 0],
                className: 'quick-marker-icon',
                html: '<div class="marker-container">' +
                '<div class="marker-card">' +
                '<div class="front face">' + locations[i][4] + '</div>' +
                '<div class="back face">' + locations[i][4] + '</div>' +
                '<div class="marker-arrow"></div>' +
                '</div>' +
                '</div>'
            });
            var popupOptions = {'maxWidth': '320', 'minWidth': '320', 'className': 'leaflet-infoBox'}
            var markerArray = [];
            marker = new L.marker([locations[i][1], locations[i][2]], {icon: quickIcon,}).bindPopup(locations[i][0], popupOptions);
            marker.on('click', function (e) {
            });
            quickmap.on('popupopen', function (e) {
                L.DomUtil.addClass(e.popup._source._icon, 'clicked');
            }).on('popupclose', function (e) {
                if (e.popup) {
                    L.DomUtil.removeClass(e.popup._source._icon, 'clicked');
                }
            });
            markers.addLayer(marker);
        }
        quickmap.addLayer(markers);
        markerArray.push(markers);
        if (markerArray.length > 0) {
            quickmap.fitBounds(L.featureGroup(markerArray).getBounds().pad(0.2));
        }
        quickmap.removeControl(quickmap.zoomControl);
        var zoomOptions = {zoomInText: '', zoomOutText: '',};
        var zoom = L.control.zoom(zoomOptions);
        zoom.addTo(quickmap);
    }

    function singleListingMap() {
        var lng = parseFloat($('#singleListingMap').data('longitude'));
        var lat = parseFloat($('#singleListingMap').data('latitude'));
        var singleMapIco = "<i class='" + $('#singleListingMap').data('map-icon') + "'></i>";
        var quickIcon = L.divIcon({
            iconAnchor: [0, 0],
            popupAnchor: [0, 0],
            className: 'quick-marker-icon',
            html: '<div class="marker-container no-marker-icon ">' +
            '<div class="marker-card">' +
            '<div class="front face">' + singleMapIco + '</div>' +
            '<div class="back face">' + singleMapIco + '</div>' +
            '<div class="marker-arrow"></div>' +
            '</div>' +
            '</div>'
        });

        if ($(window).width() < 992) {
            var scrollEnabled = false;
        } else {
            var scrollEnabled = true;
        }

        var mapOptions = {center: [lat, lng], zoom: 13, zoomControl: false, gestureHandling: scrollEnabled}
        var map_single = L.map('singleListingMap', mapOptions);
        var zoomOptions = {
            zoomInText: '<i class="fa fa-plus" aria-hidden="true"></i>',
            zoomOutText: '<i class="fa fa-minus" aria-hidden="true"></i>',
        };
        var zoom = L.control.zoom(zoomOptions);
        zoom.addTo(map_single);
        map_single.scrollWheelZoom.disable();
        marker = new L.marker([lat, lng], {icon: quickIcon,}).addTo(map_single);
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: " &copy;  <a href='https://www.mapbox.com/about/maps/'>Mapbox</a> &copy;  <a href='http://www.openstreetmap.org/copyright'>OpenStreetMap</a>",
            maxZoom: 18,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: openstreet_access_token
        }).addTo(map_single);
        $('a#streetView').attr({
            href: 'https://www.google.com/maps/search/?api=1&query=' + lat + ',' + lng + '',
            target: '_blank'
        });
        window.singleListingMap = map_single;
    }

    if (document.getElementById("singleListingMap") !== null) {
        singleListingMap();
    }
});