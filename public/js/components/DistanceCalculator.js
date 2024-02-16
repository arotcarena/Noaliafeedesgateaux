import { myFetchPost } from "../functions/api.js";

export class DistanceCalculator {

    /** @type {HTMLElement} */
    #distanceCalculator;
    
    /** @type {Object} */
    #locationData;
    
    /** @type {HTMLElement} */
    #loader;

    /**
     * 
     * @param {HTMLElement} distanceCalculator 
     * @param {Object} locationData 
     */
    constructor(distanceCalculator, locationData, loader) {
        this.#loader = loader;
        this.#distanceCalculator = distanceCalculator;
        this.#locationData = locationData;
        this.#open();
    }

    async #open() {
        this.#loader.classList.add('visible');
        const distance = await myFetchPost(this.#distanceCalculator.dataset.entrypoint, {
            x: this.#locationData.x * 100,
            y: this.#locationData.y * 100
        });
        this.#loader.classList.remove('visible');
        this.#hydrateElt(distance);
        this.#distanceCalculator.classList.add('visible');
    }
    /**
     * 
     * @param {string} distance 
     * @returns {HTMLElement}
     */
    #hydrateElt(distance) {
        this.#distanceCalculator.querySelector('.title').innerText = this.#locationData.city + ', ' + this.#locationData.postcode;
        this.#distanceCalculator.querySelector('.distance').innerText = distance + ' km';
    }
}