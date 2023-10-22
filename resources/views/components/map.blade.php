@props(['MapEvents'])

@php
    // Replace the &quot; with " in the json string
    $MapEvents = str_replace('&quot;', '"', $MapEvents);
    $MapEvents = json_decode($MapEvents);
@endphp

{{-- Create the map --}}
<div id="mapid"></div>

<script>
    // Get the map events from the component
    var MapEvents = @json($MapEvents);

    // Create the map with the events
    var mymap = L.map('mapid').setView([MapEvents[0].lat, MapEvents[0].lng], 9);

    //Change the map language
    var lang = document.getElementsByTagName('html')[0].getAttribute('lang');
    var langMap = 'en';
    if (lang == 'es') {
        langMap = 'es';
    }

    // Add the tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        maxZoom: 18,
        minZoom: 2,
        language: langMap,

    }).addTo(mymap);

    // Add a marker
    var marker = L.marker([MapEvents[0].lat, MapEvents[0].lng]).addTo(mymap);

    // Add a circle
    var circle = L.circle([MapEvents[0].lat, MapEvents[0].lng], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.5,
        radius: 500
    }).addTo(mymap);
    circle.bindPopup(MapEvents[0].title);


    //Add events in the map
    for (let index = 0; index < MapEvents.length; index++) {
        var marker = L.marker([MapEvents[index].lat, MapEvents[index].lng]).addTo(mymap);
        marker.bindPopup(MapEvents[index].title);
        // //add data link
        marker.on('click', function(e) {
            window.location.href = MapEvents[index].source;
        });
    }


    // Add a popup
    marker.bindPopup(MapEvents[0].title).openPopup();

    // Add a popup to the map
    var popup = L.popup()
        .setLatLng([MapEvents[0].lat, MapEvents[0].lng])
        .setContent(MapEvents[0].title)
        .openOn(mymap);

    
</script>
