export class EmailValidator {
    /** @type {string} */
    #errorMessage;
    
    constructor(errorMessage = 'email invalide') {
        this.#errorMessage = errorMessage;
    }

    /**
     * 
     * @returns {string|null} errorMessage | null
     */
    validate(value) {
        if(value.length > 0 && !value.match(/[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/)) {
            return this.#errorMessage;
        }
        return null;
    }
}