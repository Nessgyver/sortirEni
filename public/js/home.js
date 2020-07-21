//Récupération des deux inputs date
const DATE_DEBUT = $('#liste_sortie_dateDebut');
const DATE_FIN = $('#liste_sortie_dateFin');

//====================================================================================================================

//Fonction gérant l'ajout et la suppression dynamique des required
function gestionRequired(triggerElement, addRequiredElement)
{
    triggerElement.change(function() {
        addRequiredElement.prop('required',true);

        gestionEcartDate();
        if (triggerElement.val() === "")
        {
            addRequiredElement.removeAttr('required');
        }
    });
}

function gestionEcartDate()
{
    if (DATE_DEBUT.val() !== "" && DATE_FIN.val() !== "")
    {
        if (DATE_DEBUT.val() > DATE_FIN.val())
        {
            DATE_DEBUT.val(DATE_FIN.val())
        }
    }
}


$(document).ready(function () {
    gestionRequired(DATE_DEBUT, DATE_FIN);
    gestionRequired(DATE_FIN, DATE_DEBUT);


})

