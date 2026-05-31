const form = document.querySelector("form");
        
var erreurs = [];
var divError=null;
var pError=null;

if (form) {
    form.addEventListener("submit", (e) => {
        // On empêche le comportement par défaut (submit)
        e.preventDefault();

        erreurs = [];
        let textError = '';

        let divError = document.getElementById("jsError");
        let pError = document.querySelector('div#jsError p');

        let user_form_prenom = document.getElementById('user_form_prenom');
        let user_form_nom = document.getElementById('user_form_nom');
        let user_form_email= document.getElementById('user_form_email');
        let user_form_password_first= document.getElementById('user_form_password_first');
        let user_form_password_second= document.getElementById('user_form_password_second');
        let user_form_CGU= document.getElementById('user_form_CGU');

        let erreurJS = '';
        if (!validateNom(user_form_prenom)){
            user_form_prenom.classList.add('border-danger');
            erreurJS += " prenom";    
        } 
        if (!validateNom(user_form_nom)) erreurJS += " nom";
        if (!validateEmail(user_form_email)) erreurJS += " email";
        if (!validatePassword(user_form_password_first,user_form_password_second)) erreurJS += " password";
        if (!validateCGU(user_form_CGU)) erreurJS += " CGU";
        if (erreurJS) {
            console.log('Erreurs sur le(s) champs suivant(s) : '+ erreurJS );
        }

        if (erreurs.length)
        {
            for (let i=0; i<erreurs.length;i++) {
                textError = textError.concat('<br/>',erreurs[i]);
            }
            pError.innerHTML = textError;
            divError.classList.remove('invisible');
        }
        else {
            divError.classList.add('invisible');
            pError.innerHTML = '';
            form.submit();
        }
    })
}

function validateNom(nom) {
    let regExp = new RegExp("^[\\w]+$");

    if (regExp.test(nom.value)){
        nom.classList.remove('border-danger');
        return true;
    }
    else {
        nom.classList.add('border-danger');
        let label =document.querySelector(`label[for="${nom.id}"]`);
        let txt = label.textContent.replace(' *','');
        erreurs.push( `Le champ <em><strong>${txt}</strong></em> est invalide.`);
        return false;
    }
}

function validateEmail(email) {
    let regExp = new RegExp("^[\\w-+\.]+@[\\w-\.]+\\.[\\w\.]+$");
    if (regExp.test(email.value)) { 
        email.classList.remove('border-danger');
        return true;
    }
    else {
        email.classList.add('border-danger');
        erreurs.push('Le champ <em><strong>Adresse email</strong></em> est invalide.');
        return false;
    }
}

function validatePassword(pass1,pass2) {
    if (pass1.value.length==0) {
        pass1.classList.add('border-danger');
        erreurs.push('Le champ <em><strong>mot de passe</strong></em> ne doit pas etre vide.');
        return false
    }
    if (pass1.value != pass2.value) {
        erreurs.push('Les mots de passe ne correspondent pas.');
        pass1.classList.add('border-danger');
        pass2.classList.add('border-danger');
        return false;
    }
    pass1.classList.remove('border-danger');
    pass2.classList.remove('border-danger');
    return true;
}

function validateCGU(cgu){
    if (!cgu.checked) {
        cgu.classList.add('border-danger');
        erreurs.push('Vous devez accepter les <em><strong>CGU</strong></em>.');
        return false;
    }
    return true;
}