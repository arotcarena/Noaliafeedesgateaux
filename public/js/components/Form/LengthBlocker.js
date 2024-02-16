import { Suggestor } from "./Suggestor.js";

export class LengthBlocker {

    /** @type {HTMLFormElement} */
    #form;

    /**
     * 
     * @param {HTMLFormElement} form 
     * @param {Object} mapping {field: maxLength, ...}
     */
    constructor(form, mapping) {
        this.#form = form;

        for(const [field, maxLength] of Object.entries(mapping)) {
            const input = this.#form.querySelector('[name='+field+']');
            input.addEventListener('input', e => this.#onInput(e, maxLength))
        }
    }

    /**
     * 
     * @param {Event} e 
     * @param {number} maxLength 
     */
    #onInput(e, maxLength) {
        if(e.currentTarget.value.length > maxLength) {
            e.currentTarget.value = e.currentTarget.value.substring(0, maxLength);
            this.#addAlert(e.currentTarget, maxLength);
        }
    }

    /**
     * @param {HTMLElement} input
     * @param {number} maxLength
     */
    #addAlert(input, maxLength) {
        const container = input.parentElement;
        if(container.querySelector('.suggest-list')) {
            container.querySelector('.suggest-list').dispatchEvent(new CustomEvent(Suggestor.mustClose));
        }
        container.classList.add('is-maxLength');
        if(!container.querySelector('.maxLengthError')) {
            const errorElt = document.createElement('div');
            errorElt.classList.add('maxLengthError');
            container.append(errorElt);
        }
        container.querySelector('.maxLengthError').innerHTML = maxLength + ' caractères maximum';
        setTimeout(() => {
            if(container.querySelector('.maxLengthError')) {
                container.querySelector('.maxLengthError').remove();
            }
            container.classList.remove('is-maxLength');
        }, 1000);
    }

}