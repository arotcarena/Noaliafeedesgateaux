import { myFetch } from "../../functions/api.js";
import { CheckboxManager } from "./CheckboxManager.js";


export class CheckboxDeleteManager {
    /** @type {HTMLElement} */
    #trash;
    /** @type {CheckboxManager} */
    #checkboxManager;
    /** @type {string} */
    #entryPoint;

    /**
     * 
     * @param {CheckboxManager} checkboxManager 
     */
    constructor(checkboxManager, trashSelector) {
        this.#checkboxManager = checkboxManager;
        this.#trash = document.querySelector(trashSelector);
        this.#entryPoint = this.#trash.dataset.entrypoint;
        this.#trash.addEventListener('click', e => this.#onTrashClick(e));
    }

    /**
     * 
     * @param {MouseEvent} e 
     */
    async #onTrashClick(e) {
        if(this.#checkboxManager.checked.length === 0) {
            return;
        }
        const ids = this.#checkboxManager.getCheckedIds();
        if(window.confirm(this.#getConfirmSentence(ids.length))) {
            const result = await this.#callApi(ids);
            if(result === 'ok') {
                window.location.reload();
            }
        }
    }

    /**
     * 
     * @param {number[]} ids 
     * @returns {Promise}
     */
    #callApi(ids) {
        return myFetch(this.#entryPoint, {
            method: 'POST',
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify(ids)
        });
    }

    /**
     * 
     * @param {number} length 
     * @returns 
     */
    #getConfirmSentence(length) {
        return length > 1 ? 
        'Voulez-vous vraiment supprimer ces '+length+' commandes ?'
        : 'Voulez-vous vraiment supprimer cette commande ?';
    }
}
