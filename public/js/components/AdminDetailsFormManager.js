import { myFetchPost } from "../functions/api.js";
import { CitySuggestor } from "./Form/CitySuggestor.js";
import { FormValidator } from "./Form/FormValidator.js";
import { LengthBlocker } from "./Form/LengthBlocker.js";
import { PhoneFieldManager } from "./Form/PhoneFieldManager.js";
import { EmailValidator } from "./Form/Validator/EmailValidator.js";
import { NotBlankValidator } from "./Form/Validator/NotBlankValidator.js";
import { PhoneValidator } from "./Form/Validator/PhoneValidator.js";
import { SuggestValidator } from "./Form/Validator/SuggestValidator.js";

export class AdminDetailsFormManager {
    /** @type {HTMLFormElement} */
    #form;
    /** @type {HTMLInputElement} */
    #locationInput;
    /** @type {HTMLElement} */
    #update;
    /** @type {HTMLElement} */
    #cancel;
    /** @type {boolean} */
    #initialized = false;

    /**
     * 
     * @param {HTMLFormElement} form 
     * @param {HTMLElement} update 
     * @param {HTMLElement} cancel 
     */
    constructor(form, update, cancel) {
        this.#form = form;
        this.#update = update;
        this.#cancel = cancel;
        this.#locationInput = this.#form.querySelector('[name=location]')
        this.#update.addEventListener('click', e => this.#openForm(e));
    }

    /**
     * 
     * @param {MouseEvent} e 
     */
    #openForm(e) {
        this.#form.parentElement.classList.add('update');
        if(!this.#initialized) {
            this.#initialized = true;
            this.#configureForm();
            this.#cancel.addEventListener('click', e => this.#onCancel());
            this.#form.addEventListener(FormValidator.submitSuccess, e => this.#onSubmit(e.detail.submitEvent));
        }
    }


    #onCancel() {
        if(this.#cancel.classList.contains('disabled')) {
            return;
        }
        this.#form.parentElement.classList.remove('update');
        // this.#form.querySelectorAll('.error, .maxLengthError').forEach(elt => {
        //     elt.parentElement.classList.remove('is-invalid', 'is-maxLength');
        //     elt.remove();
        // });
    }
    
    /**
     * 
     * @param {Event} e 
     */
    async #onSubmit(e) {
        e.preventDefault();

        const submitButton = this.#form.querySelector('[type=submit]');
        submitButton.classList.add('loading');
        submitButton.classList.remove('enabled');
        submitButton.previousElementSibling.classList.add('disabled');

        await myFetchPost(this.#form.dataset.entrypoint, this.#prepareData());
        this.#form.submit(); //sers juste à recharger la page, aucun traitement n'est fait sur le formulaire soumis
    }

    /**
     * 
     * @returns {Object}
     */
    #prepareData() {
        return {
            phone: this.#form.querySelector('[name=phone]').value,
            email: this.#form.querySelector('[name=email]').value,
            facebook: this.#form.querySelector('[name=facebook]').value,
            city: this.#locationInput.dataset.city,
            postcode: this.#locationInput.dataset.postcode,
            x: this.#locationInput.dataset.x,
            y: this.#locationInput.dataset.y,
        };
    }

    #configureForm() {
        new CitySuggestor(this.#form.querySelector('.js-city-suggest'));
        new FormValidator(
            this.#form,
            {
            email: [
                new EmailValidator('Adresse email invalide'),
                new NotBlankValidator('L\'adresse email est obligatoire')
            ],
            phone: [
                new PhoneValidator('Le numéro saisi n\'est pas valide'),
                new NotBlankValidator('Le numéro de téléphone est obligatoire')
            ],
            facebook: [
                new NotBlankValidator('Le lien facebook est obligatoire')
            ],
            location: [
                new NotBlankValidator('La ville est obligatoire'),
                new SuggestValidator('Veuillez sélectionner une ville parmis les choix proposés')
            ] 
            }
        );
        new PhoneFieldManager(this.#form.querySelector('[name=phone]'));
        new LengthBlocker(
            this.#form, 
            {
              location: 200,
              email: 200,
              facebook: 200
            }
          );
    }
}

