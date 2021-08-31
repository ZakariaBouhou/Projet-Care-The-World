const app = {

    elementDataList: document.getElementById('event_city'),
    elementInputZipcode: document.getElementById('zipcode'),
    regex : new RegExp('^([0-9]){5}$'),
    fetOptions:{ mode: 'cors',},

    convertFromJson: function(response){
        return data = response.json();
    },

    displayCities: function (cities) {
        if(cities.length > 0){
            let option;
            app.elementDataList.innerHTML = '';
            let compteur = 1;
            for (let city in cities) {
                option = document.createElement('option');
                option.text = cities[city].nom;
                option.value = cities[city].code;
                app.elementDataList.add(option);
                if(compteur == 1){
                    app.elementDataList.options[app.elementDataList.selectedIndex].setAttribute("selected","selected");
                    compteur++;
                }
            }
        }
    },

    api : function(){
        const zipcode = document.getElementById('event_zipCode').value;

        if (app.regex.test(zipcode)) {
            const APILink = "https://geo.api.gouv.fr/communes?codePostal=" + zipcode + "&fields=,nom,code,codesPostaux,centre,surface,codeDepartement,departement,codeRegion,region,population&format=json&geometry=centre";
    
            fetch(APILink, app.fetOptions)
                .then(app.convertFromJson)
                .then(app.displayCities)
            ;
        }
        else {
            app.elementDataList.innerHTML = '<option selected>Selectionner une ville</option>';
        }
    },

    listen: function(){
        const select = document.querySelector('#event_city');
        const selected = select.querySelectorAll("option[selected='selected']");
        for(let i = 0;i < selected.length; i++){
            selected[i].removeAttribute("selected");
        }
        app.elementDataList.options[app.elementDataList.selectedIndex].setAttribute("selected","selected");
    },

    init : function(){
        document.getElementById('event_zipCode').addEventListener('keyup',app.api);
        app.elementDataList.addEventListener('click', app.listen);
        window.onload = app.loadCitiesIfZipCode();
    },

    loadCitiesIfZipCode : function() {
        let zipeCodeValue = document.querySelector('#event_zipCode').value;
        let cityValue = document.querySelector('#event_city');

        // for reload cities list 
        if (zipeCodeValue) {
            app.api();
        }
    },
}

document.addEventListener('DOMContentLoaded', app.init);
