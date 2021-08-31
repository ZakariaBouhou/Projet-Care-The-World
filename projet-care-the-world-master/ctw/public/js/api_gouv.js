const elementInputZipcode = document.getElementById('zipcode');
const elementDataList = document.getElementById('event_city');

const regex = new RegExp('^([0-9]){5}$');
let fetOptions =
    {
        mode: 'cors',
    }

function api(event) {

    let zipcode = document.getElementById('event_zipCode').value;

    if (regex.test(zipcode)) {
        let APILink = "https://geo.api.gouv.fr/communes?codePostal=" + zipcode + "&fields=,nom,code,codesPostaux,centre,surface,codeDepartement,departement,codeRegion,region,population&format=json&geometry=centre";

        fetch(APILink, fetOptions)
            .then(convertFromJson)
            .then(displayCities)
        ;
    }
    else {
        elementDataList.innerHTML = '<option selected>Selectionner une ville</option>';
    }
}

function convertFromJson(response) {
    return data = response.json();
}

function displayCities (cities) {
    let option;
    elementDataList.innerHTML = '';
    let compteur = 1;
    for (let city in cities) {
        option = document.createElement('option');
        option.text = cities[city].nom;
        option.value = cities[city].code;
        elementDataList.add(option);
        if(compteur == 1){
            elementDataList.options[elementDataList.selectedIndex].setAttribute("selected","selected");
            compteur++;
        }
    }
}

elementDataList.addEventListener('click',function(){
    const select = document.querySelector('#event_city');
    const selected = select.querySelectorAll("option[selected='selected']");
    for(let i = 0;i < selected.length; i++){
        selected[i].removeAttribute("selected");
    }
    elementDataList.options[elementDataList.selectedIndex].setAttribute("selected","selected");
});

document.getElementById('event_zipCode').addEventListener('keyup',api);
