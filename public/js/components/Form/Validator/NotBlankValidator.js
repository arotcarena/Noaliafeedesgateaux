export class NotBlankValidator {
    /** @type {string} */
    #errorMessage;
    
    constructor(errorMessage = 'Ce champs ne doit pas être vide') {
        this.#errorMessage = errorMessage;
    }

    /**
     * 
     * @returns {string|null} errorMessage | null
     */
    validate(value) {
        if(value.length === 0) {
            return this.#errorMessage;
        }
        return null;
    }
}

