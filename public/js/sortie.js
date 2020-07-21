$("#sortie_ville").change(function(){
    var villeId = document.getElementById('sortie_ville').value;
    let url = rootUrl + "/api/1/lieux";
    axios.get(url,{
        params: {
            villeId: villeId
        },
        dataType: "json"
    })
    .then(function(response){
        let codePostal = response.data.codePostal;
        sortie_lieu.innerHTML =
            '<option value selected>Veuillez sélectionner un lieu</optionvalue>';
        for(let i = 0; i < response.data.lieuxAssocies.length; i++){
            let lieu = response.data.lieuxAssocies[i];
            sortie_lieu.innerHTML +=
                `<option value = "${lieu.id}">${lieu.nom}</option>`;
        }
        info_lieu.innerHTML = "";
        if(codePostal != null){
            info_lieu.innerHTML +=
                `<div class="form-group "><label>Code Postal :</label> <input class="form-control-plaintext" value="${codePostal}"></div>`;
        }
    })
})

$("#sortie_lieu").change(function(){
    let lieuId = document.getElementById('sortie_lieu').value;
    let url = rootUrl + "/api/1/lieu";
    axios.get(url,{
        params: {
            lieuId: lieuId
        },
        dataType: "json"
    })
        .then(function(response){
            let lieu = response.data.lieu;
            lieu = lieu[0];
            if(lieu != null){
                info_lieu.innerHTML +=
                    `<div class="form-group "><label>Rue :</label> <input class="form-control-plaintext" value="${lieu.rue}"></div>
                    <div class="form-group "><label>Longitude :</label> <input class="form-control-plaintext" value="${lieu.longitude}"></div>
                    <div class="form-group "><label>Latitude :</label> <input class="form-control-plaintext" value="${lieu.latitude}"></div>`;
            }
        })
})

