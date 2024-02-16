import { Suggestor } from "./Suggestor.js";
import { SuggestValidator } from "./Validator/SuggestValidator.js";

export class FormValidator {

    static submitSuccess = 'submitsuccess';

    /** @type {HTMLFormElement} */
    #form;

    /** @type {HTMLButtonElement} */
    #submit;

    /** @type {Object} */
    #validators;


    /**
     * 
     * @param {HTMLElement} form
     * @param {Object} validators {field: validator[], ...}
     */
    constructor(form, validators = {}) {
        this.#form = form;
        this.#validators = validators;
        this.#submit = this.#form.querySelector('.form-submit');

        this.#form.addEventListener('input', e => this.#onInput(e));
        this.#form.addEventListener(Suggestor.suggestValidated, e => this.#onInput(e));
        this.#form.addEventListener('submit', e => this.#onSubmit(e));
    }

    #onInput(e) {
        const field = e.target.name;
        const value = e.target.value;
        const errorMessages = this.#validateOne(field, value);
        if(errorMessages.length > 0)  {
            this.#addAlerts(field, errorMessages);
            this.#submit.classList.remove('enabled');
        } else {
            this.#removeAlerts(field);
            const errors = this.#validateAll();
            if(Object.values(errors).length === 0) {
                this.#submit.classList.add('enabled');
            }
        }
    }

    /**
     * 
     * @param {Event} submitEvent 
     */
    #onSubmit(submitEvent) {
        const errors = this.#validateAll();
        if(Object.values(errors).length > 0) {
            for(const [field, errorMessages] of Object.entries(errors)) {
                this.#addAlerts(field, errorMessages);
                submitEvent.preventDefault();
            }
        } else{
            this.#form.dispatchEvent(new CustomEvent(FormValidator.submitSuccess, {
                detail: {
                    submitEvent: submitEvent
                }
            }));
        }
    }
    /**
     * 
     * @returns {Object} errors {field: errorMessages}
     */
    #validateAll() {
        const formData = new FormData(this.#form);
        let errors = {};
        for(const [field, value] of formData.entries()) {
            const errorMessages = this.#validateOne(field, value);
            if(errorMessages.length > 0) {
                errors[field] = errorMessages;
            }
        }
        return errors;
    }

    /**
     * 
     * @param {string} field 
     * @param {string} value 
     * @returns {array} errorMessages
     */
    #validateOne(field, value) {
        if(!this.#validators[field]) {
            return [];
        }
        const validators = this.#validators[field];
        let errorMessages = [];
        for(const validator of validators) {
            let errorMessage = null;
            if(validator instanceof SuggestValidator) {
                errorMessage = validator.validate(
                    this.#form.querySelector('[name='+field+']')
                );
            } else {
                errorMessage = validator.validate(value);
            }
            if(errorMessage !== null) {
                errorMessages.push(errorMessage);
            }
        }
        return errorMessages;
    }

    /**
     * @param {string} field
     * @param {string[]} errorMessages
     */
    #addAlerts(field, errorMessages) {
        const container = this.#getContainer(field);
        container.classList.add('is-invalid');
        if(!container.querySelector('.error')) {
            const errorElt = document.createElement('div');
            errorElt.classList.add('error');
            container.append(errorElt);
        }
        container.querySelector('.error').innerHTML = errorMessages.join('<br>');
    }
    #removeAlerts(field) {
        const container = this.#getContainer(field);
        container.classList.remove('is-invalid');
        if(container.querySelector('.error')) {
            container.querySelector('.error').remove();
        }
    }


    /**
     * 
     * @param {string} field 
     * @returns {HTMLElement} field container
     */
    #getContainer(field) {
        return this.#form.querySelector('[name='+field+']').parentElement;
    }
}