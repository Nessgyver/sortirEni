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
        sortie_lieu.innerHTML =
            '<option value selected>Veuillez sélectionner un lieu</optionvalue>';
        console.log(response.data.lieuxAssocies);
        for(let i = 0; i < response.data.lieuxAssocies.length; i++){
            let lieu = response.data.lieuxAssocies[i];
            sortie_lieu.innerHTML +=
                `<option value = "${i}" >${lieu.nom}</option>`;
        }
    })
});

$("#sortie_lieu").change(function(){
    var lieuId = document.getElementById('sortie_lieu').value;


})
