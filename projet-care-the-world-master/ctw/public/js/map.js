var api = {
    // api connection options
    fetchOptions: {
        mode: 'cors',
    },

    // AJAX function
    connection: function () {
        let APILink = "api/event";
        fetch(APILink, api.fetchOptions)
            .then(api.convertFromJson)
            .then(api.displayCities);
    },

    // convert response in json
    convertFromJson: function (response) {
        return cities = response.json();
    },

    displayCities: function (cities) {
        map.initMap(cities);
    },
}

document.addEventListener('DOMContentLoaded', api.connection);