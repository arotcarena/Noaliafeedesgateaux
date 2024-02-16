import { myFetchPost } from "../functions/api.js";

export class OrderSubmit {

    static timeout = 30000;

    /** @type {HTMLFormElement} */
    #form;
    /** @type {HTMLInputElement} */
    #input;
    /** @type {HTMLElement} */
    #flash;
    /** @type {string} */
    #entrypoint;
    /** @type {number} */
    #startingTime;
    
    /**
     * 
     * @param {HTMLFormElement} form 
     * @param {HTMLInputElement} input 
     * @param {HTMLElement} flash 
     */
    constructor(form, input, flash) {
        this.#flash = flash;
        this.#form = form;
        this.#entrypoint = this.#form.dataset.entrypoint;
        this.#input = input;
        this.#startingTime = Date.now();
        if(this.#form.querySelector('[type=submit]').classList.contains('enabled')) {
            this.#submit(this.#prepareData());
        }
    }

    /**
     * 
     * @param {{fullName: string, email: string, phone, string, cityManualEntry: string, city: string, postcode, string, x: string, y: string, message: string}} data 
     */
    async #submit(data) {
        this.#form.querySelector('[type=submit]').classList.add('loading');
        this.#form.querySelector('[type=submit]').classList.remove('enabled');
        try {
            const success = await myFetchPost(this.#entrypoint, data);
            this.#form.querySelector('[type=submit]').classList.remove('loading');
            this.#addFlash(success);
        } catch(e) {
            //RETRY
            if(Date.now() < (this.#startingTime + OrderSubmit.timeout)) {
                this.#submit(data);
            } else {  //TIMEOUT
                this.#form.querySelector('[type=submit]').classList.remove('loading');
                this.#addFlash(false);
            }
        }
    }

    #addFlash(success) {
        if(success) {
          this.#flash.classList.add('success');
          this.#flash.classList.remove('failure');
          this.#flash.querySelector('.submit-alert-label').innerText = 'Votre demande a bien été envoyée !';
          this.#resetForm();
        } else {
          this.#flash.classList.add('failure');
          this.#flash.classList.remove('success');
          this.#flash.querySelector('.submit-alert-label').innerText = 'Suite à un incident technique, votre demande n\'a pas pu être envoyée. Réessayez dans quelques instants.';
          setTimeout(() => {
            this.#form.querySelector('[type=submit]').classList.add('enabled');
          }, 4000);
        }
        this.#flash.classList.add('visible');
        setTimeout(() => {
          this.#flash.classList.remove('visible');
        }, 4000);
    }

    #resetForm() {
        this.#form.reset();
        this.#input.dataset.x = '';
        this.#input.dataset.y = '';
        this.#input.dataset.city = '';
        this.#input.dataset.postcode = '';
        this.#form.querySelectorAll('.error, .maxLengthError').forEach(elt => {
            elt.remove();
        });
        this.#form.querySelectorAll('.is-invalid, .is-maxLength').forEach(elt => {
            elt.classList.remove('is-invalid', 'is-maxLength');
        });
    }

    /**
     * @returns {{fullName: string, email: string, phone, string, cityManualEntry: string, city: string, postcode, string, x: string, y: string, message: string}}
     */
    #prepareData() {
        const formData = new FormData(this.#form);
        return {
            fullName: formData.get('fullName'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            cityManualEntry: formData.get('city'),
            city: this.#input.dataset.city,
            postcode: this.#input.dataset.postcode,
            x: this.#input.dataset.x,
            y: this.#input.dataset.y,
            message: formData.get('message')
        };
    }
}

