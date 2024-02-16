import { CitySuggestor } from "../components/Form/CitySuggestor.js";
import { FormValidator } from "../components/Form/FormValidator.js";
import { EmailValidator } from "../components/Form/Validator/EmailValidator.js";
import { NotBlankValidator } from "../components/Form/Validator/NotBlankValidator.js";
import { LengthValidator } from "../components/Form/Validator/LengthValidator.js";
import { PhoneValidator } from "../components/Form/Validator/PhoneValidator.js";
import { PhoneFieldManager } from "../components/Form/PhoneFieldManager.js";
import { OrderSubmit } from "../components/OrderSubmit.js";
import { LengthBlocker } from "../components/Form/LengthBlocker.js";
import { manageCakeShow } from "../functions/cakeShow.js";
import { CloseHandler } from "../components/CloseHandler.js";
import { Scrollspy } from "../components/Scrollspy.js";


/*hidden-menu*/
if(document.querySelector('.navbar')) {
  const navbar = document.querySelector('.navbar');
  if(navbar.querySelector('.js-hidden-menu-opener')) {
    navbar.querySelector('.js-hidden-menu-opener').addEventListener('click', function(e) {
      if(!navbar.classList.contains('open')) {
        navbar.classList.add('open');
        (new CloseHandler(navbar, document.querySelector('.js-hidden-menu-closer'), document.querySelector('.js-order-button')))
        .start()
        .then(e => {
            navbar.classList.remove('open');
        })
      } else {
        navbar.classList.remove('open');
      }
    });
  }
}



/* cake show */
document.querySelectorAll('.js-cake-card').forEach(function(cakeCard) {
  manageCakeShow(cakeCard);
}); 



/* form validation */
new FormValidator(
  document.querySelector('.form'),
  {
    email: [
      new EmailValidator('Adresse email invalide'),
      new NotBlankValidator('Vous devez renseigner une adresse email')
    ],
    fullName: [
      new NotBlankValidator('Vous devez renseigner un nom'),
      new LengthValidator({min: 3, minErrorMessage: '3 caractères minimum'})
    ],
    phone: [
      new PhoneValidator('Le numéro saisi n\'est pas valide')
    ],
    city: [
      new NotBlankValidator('Vous devez renseigner une ville'),
      new LengthValidator({min: 3, minErrorMessage: '3 caractères minimum'})
    ]
  }
);

new LengthBlocker(
  document.querySelector('.form'), 
  {
    message: 2000,
    city: 200,
    fullName: 200,
    email: 200
  }
);

/*city suggestor*/
new CitySuggestor(document.querySelector('.js-city-suggest'));
/*si la ville est validée on met le focus sur le champ suivant : phone*/
document.querySelector('.form [name=city]').addEventListener(CitySuggestor.suggestValidated, e => {
  if(e.detail === CitySuggestor.keyboardValidation) {
    document.querySelector('.form [name=phone]').focus();
  }
});

/* traitement spécial sur le champ phone */
new PhoneFieldManager(document.querySelector('.form [name=phone]'));
/*si un phone est validé on place le focus sur le champ suivant : message*/
document.querySelector('.form [name=phone]').addEventListener(PhoneFieldManager.phoneValidated, e => {
  document.querySelector('.form [name=message]').focus();
});

/*form submit*/  
document.querySelector('.form').addEventListener(FormValidator.submitSuccess, e => {
  e.detail.submitEvent.preventDefault();
  new OrderSubmit(
    e.currentTarget, 
    e.currentTarget.querySelector('[name=city]'),
    e.currentTarget.querySelector('.submit-alert')
  );
});




/*spyscroll order form*/
new Scrollspy(
  document.querySelectorAll('.js-scrollspy-order'),
  document.querySelector('.js-order-button').parentElement,
  document.querySelector('.navbar')
);
