export class SuggestValidator {
    /** @type {string} */
    #errorMessage;
    
    constructor(errorMessage = 'Veuillez sélectionner un élément parmis les choix proposés') {
        this.#errorMessage = errorMessage;
    }

    /**
     * @param {HTMLElement} elt
     * @returns {string|null} errorMessage | null
     */
    validate(elt) {
        if(elt.dataset.suggestvalidated !== '1') {
            return this.#errorMessage;
        }
        return null;
    }
}