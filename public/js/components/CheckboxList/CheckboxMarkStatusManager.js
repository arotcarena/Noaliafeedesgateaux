import { myFetch } from "../../functions/api.js";
import { CheckboxManager } from "./CheckboxManager.js";


export class CheckboxMarkStatusManager {
    /** @type {CheckboxManager} */
    #checkboxManager;
    /** @type {string} */
    #entryPoint;
    /** @type {HTMLElement} */
    #statusMarker;


    constructor(checkboxManager, statusMarkerSelector) {
        this.#checkboxManager = checkboxManager;
        this.#statusMarker = document.querySelector(statusMarkerSelector);
        this.#entryPoint = this.#statusMarker.dataset.entrypoint;

        this.#statusMarker.addEventListener('click', e => this.#onStatusMarkerClick(e));
    }

    /**
     * 
     * @param {MouseEvent} e 
     */
    async #onStatusMarkerClick(e) {
        if(this.#checkboxManager.checked.length === 0) {
            return;
        }
        const status = this.#checkboxManager.getStatusToMark();
        if(status === CheckboxManager.status_not_seen) {
            this.#checkboxManager.markAsNotSeen();
        } else {
            this.#checkboxManager.markAsSeen();
        }
        await this.#callApi({
            ids: this.#checkboxManager.idsToMark,
            statusToMark: status
        });
        document.querySelector('.js-not-seen-counter').dispatchEvent(new CustomEvent('updatecounter'));
    }

    /**
     * 
     * @param {{ids: number[], statusToMark: string}} data 
     */
    #callApi(data) {
        return myFetch(this.#entryPoint, {
            method: 'POST',
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });
    }

    
}
