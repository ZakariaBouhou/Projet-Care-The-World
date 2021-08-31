var map = {
    // center map on France
    lat: 46.927638,
    lon: 2.213749,
    mymap: null,

    // map initialization
    initMap: function (cities) {
        // icon path
        let iconBase = "img/logo/";

        // create map object in div id and center on Paris with zoom 5
        map.myMap = L.map('mapid').setView([map.lat, map.lon], 6);

        // markers group
        markerClusters = L.markerClusterGroup();

        // connect to open street map
        L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
            // link to open street map for copyrights
            attribution: 'données © <a href="https://www.openstreetmap.org/copyright"</a>/ODbL - rendu <a href="http://www.openstreetmap.fr">OSM France</a>',
            // zoom, by default min=1 and max=20
            minZoom: 1,
            maxZoom: 16
        }).addTo(map.myMap);

        // loop on each city
        for (city in cities) {
            // status 0=> active, is_verified 2 => active
            if (cities[city].status == 0 && cities[city].is_verified == 2) {
                // icon define
                let myIcon = L.icon({
                    iconUrl: iconBase + "marker.png",
                    iconSize: [28, 40],
                    iconAnchor: [14, 40],
                    popupAnchor: [0, -35],
                });

                // add marker on city + icon
                let marker = L.marker([cities[city].latitude, cities[city].longitude], {
                    icon: myIcon
                });

                // transform end date format to FR format
                let endAtToFr = new Date(cities[city].endAt);
                let endAt = endAtToFr.toLocaleString("fr-FR", {
                    year: 'numeric',
                    day: 'numeric',
                    month: 'numeric'
                });

                // transform start date to FR format
                let startAtToFr = new Date(cities[city].startAt);
                let startAt = startAtToFr.toLocaleString("fr-FR", {
                    year: 'numeric',
                    day: 'numeric',
                    month: 'numeric'
                });


                // add popup on marker with HTML informations for each project
                // with template strings (https://developer.mozilla.org/fr/docs/Web/JavaScript/Reference/Template_literals)
                marker.bindPopup(
                    `<div class='card' style='width: 200px;'>
                        <img src='img/categories/${cities[city].category.name}/${cities[city].image}' class='card-img-top w-100 image-fluid' style='height: 100px;'>
                        <div class='card-body p-2'>
                            <h5 class='mt-1 mb-1'>${cities[city].title}</h5>
                            <h6 class='mt-1 mb-1 font-italic'> ${cities[city].category.name}</h6>
                            <p class='m-0'>Du ${startAt} au ${endAt}</p>
                            <p class='mt-1 mb-1'><a href='/event/${cities[city].id}'>Accèder au détail de l'évènement</a></p>
                        </div>
                        
                    </div>`
                );

                // add marker
                markerClusters.addLayer(marker);
            }
            map.myMap.addLayer(markerClusters);
        }
    },
}