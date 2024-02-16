export class PhoneValidator {
    /** @type {string} */
    #errorMessage;
    
    constructor(errorMessage = 'numéro de téléphone invalide') {
        this.#errorMessage = errorMessage;
    }

    /**
     * 
     * @returns {string|null} errorMessage | null
     */
    validate(value) {
        if(value.length === 0) {
            return null;
        }
        if(!value.match(/^\(?([0-9]{2})\)?[-./ ]?([0-9]{2})[-./ ]?([0-9]{2})[-./ ]?([0-9]{2})\)?[-./ ]?([0-9]{2})\)?$/)) {
            return this.#errorMessage;
        }
        return null;
    }
}