$(document).on('change', '#sortie_ville',function(){
    let $field = $(this)
    let $dateHeureDebut = $('#sortie_dateHeureDebut')
    let $dateLimite = $('#sortie_dateLimiteInscription')
    let $form = $field.closest('form')
    let data = {}
    data[$field.attr('name')] = $field.val()
    data[$dateHeureDebut.attr('name')] = $dateHeureDebut.val()
    data[$dateLimite.attr('name')] = $dateLimite.val()
    $.post($form.attr('action'), data).then(function(data){
        let $input = $(data).find('#sortie_lieu')
        $('#sortie_lieu').replaceWith($input)
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

