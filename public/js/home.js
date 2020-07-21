//Récupération des inputs dateDebut
const dateDebutDayInput = document.querySelector('#liste_sortie_dateDebut_day');
const dateDebutMonthInput = document.querySelector('#liste_sortie_dateDebut_month');
const dateDebutYearInput = document.querySelector('#liste_sortie_dateDebut_year');

//Récupération des inputs dateFin
const dateFinDayInput = document.querySelector('#liste_sortie_dateFin_day');
const dateFinMonthInput = document.querySelector('#liste_sortie_dateFin_month');
const dateFinYearInput = document.querySelector('#liste_sortie_dateFin_year');


//Ajout des classes dateDebut et dateFin
function addClassDate()
{
    dateDebutDayInput.classList.add('dateDebut');
    dateDebutMonthInput.classList.add('dateDebut');
    dateDebutYearInput.classList.add('dateDebut');

    dateFinDayInput.classList.add('dateFin');
    dateFinMonthInput.classList.add('dateFin');
    dateFinYearInput.classList.add('dateFin');
}

addClassDate()


//Récupération des éléments de class dateDébut et dateFin
dateDebut = $(".dateDebut");
dateFin = $(".dateFin");

//Fonction permettant d'utiliser plusieurs fonction pour l'event ready
function gestionRequiredMeta() {
    gestionRequired(dateDebut, dateFin);
    gestionRequired(dateFin,dateDebut);
}

//Fonction gérant l'ajout et la suppression dynamique des required
function gestionRequired(triggerElement, addRequiredElement)
{
    triggerElement.change(function() {
        addRequiredElement.prop('required',true);

        if (triggerElement.val() === "")
        {
            addRequiredElement.removeAttr('required');
        }
    });
}

$(document).ready(gestionRequiredMeta())



