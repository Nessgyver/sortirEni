/**
 * fonction qui récupère la valeur de ville sélectionnée par l'utilisateur
 * pour afficher uniquement qui y sont associés dynamiquement
 * cette méthode fait appel à une méthode ajax pour récupérer ces infos
 */
$(document).on('change', '#sortie_ville',function(){

    //on initialise les valeurs nécessaires pour que l'appel ajax fonctionne
    let $field = $(this)
    let $dateHeureDebut = $('#sortie_dateHeureDebut')
    let $dateLimite = $('#sortie_dateLimiteInscription')
    let $form = $field.closest('form')
    let data = {}
    data[$field.attr('name')] = $field.val()
    data[$dateHeureDebut.attr('name')] = $dateHeureDebut.val()
    data[$dateLimite.attr('name')] = $dateLimite.val()

    //on envoie une requête ajax qui permet de récupérer la liste des lieux
    $.post($form.attr('action'), data)
        .then(function(data){
            //récupère le champ #sortie_lieu à l'intérieur de la page générée par la requête ajax
            let $input = $(data).find('#sortie_lieu')
            //injecte ce champ en lieu et place du champ existant
            $('#sortie_lieu').replaceWith($input)
            //vide la div #info_lieu pour ne pas conserver les infos d'un lieu sélectionné précédemment
            info_lieu.innerHTML = ''
    })
})

/**
 * fonction qui récupère le lieu sélectionné par l'utilisateur
 * et fait appel à l'api getLieu() qui permet de récupérer les infos le concernant
 * pour pouvoir les afficher dans la page
 */
$(document).on('change', '#sortie_lieu', function () {

    //on initialise les valeurs nécessaires pour que l'appel axios fonctionne
    let lieuId = document.getElementById('sortie_lieu').value;
    let url = rootUrl + "/api/1/lieu";

    //on envoie la requête axios à l'api concernée pour récupérer les infos
    axios.get(url,{
        params: {
            lieuId: lieuId
        },
        dataType: "json"
    })
        .then(function(response){
            info_lieu.innerHTML=''
            //on récupère les infos concernant le lieu dans la requête
            let lieu = response.data.lieu[0];
            //si un lieu a été sélectionné, on injecte les infos le concernant
            if(lieu != null){
                info_lieu.innerHTML =
                    `<div class="form-group "><label>Rue :</label> <input class="form-control-plaintext" value="${lieu.rue}" disabled></div>
                    <div class="form-group "><label>Longitude :</label> <input class="form-control-plaintext" value="${lieu.longitude}" disabled></div>
                    <div class="form-group "><label>Latitude :</label> <input class="form-control-plaintext" value="${lieu.latitude}" disabled></div>`;
            }
        })
})

