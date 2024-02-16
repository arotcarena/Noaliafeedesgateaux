export class LengthValidator {
    /** @type {string} */
    #minErrorMessage = 'pas assez de caractères';

    /** @type {string} */
    #maxErrorMessage = 'trop de caractères';

    /** @type {number|null} */
    #min = null;

    /** @type {number|null} */
    #max = null;
    
    constructor(options) {
        if(options.minErrorMessage) {
            this.#minErrorMessage = options.minErrorMessage;
        }
        if(options.maxErrorMessage) {
            this.#maxErrorMessage = options.maxErrorMessage;
        }
        if(options.min) {
            this.#min = options.min;
        }
        if(options.max) {
            this.#max = options.max;
        }
    }

    /**
     * 
     * @returns {string|null} errorMessage | null
     */
    validate(value) {
        if(value.length === 0) {
            return null;
        }
        if(this.#min !== null) {
            if(value.length < this.#min) {
                return this.#minErrorMessage;
            }
        }
        if(this.#max !== null) {
            if(value.length > this.#max) {
                return this.#maxErrorMessage;
            }
        }
        return null;
    }
}

