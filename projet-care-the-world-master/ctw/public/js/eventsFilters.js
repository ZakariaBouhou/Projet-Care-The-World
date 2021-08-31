const eventsFilters = {

    init: function() {
        document.querySelector('#filter_zipCode').addEventListener('keyup', eventsFilters.api);

        window.onload = eventsFilters.loadCitiesIfZipCode();

    },

    api: function(event) {
        let zipeCodeValue = document.querySelector('#filter_zipCode').value;

        let apiUrl = "https://geo.api.gouv.fr/communes?codePostal=" + zipeCodeValue + "&fields=,nom,code,codesPostaux,centre,surface,codeDepartement,departement,codeRegion,region,population&format=json&geometry=centre";

        fetch(apiUrl, {mode: 'cors'})
            .then(eventsFilters.returnJson)
            .then(eventsFilters.displayCities)
    },

    returnJson : function(response) {

        return data = response.json();
    },

    displayCities : function(cities) {

        let citiesElement = document.querySelector('#filter_city');
        let citiesElementValue = citiesElement.value
        

        citiesElement.innerHTML = "";

        for(let city in cities) {

            let option = document.createElement('OPTION');

            option.textContent = cities[city].nom;
            option.value = cities[city].nom;

            if (option.value == citiesElementValue) {
                
                option.setAttribute('selected', 'selected');
            }

            citiesElement.appendChild(option);
        }

    },

    loadCitiesIfZipCode : function() {
        let zipeCodeValue = document.querySelector('#filter_zipCode').value;
        let cityValue = document.querySelector('#filter_city');

        // for reload cities list 
        if (zipeCodeValue) {
            eventsFilters.api();
        }
    },

 
}

document.addEventListener('DOMContentLoaded', eventsFilters.init);