import { myFetch } from "../functions/api.js";
import { CheckboxManager } from "./CheckboxList/CheckboxManager.js";
import { CheckboxMarkStatusManager } from "./CheckboxList/CheckboxMarkStatusManager.js";

export class OrderShowManager {

    //event
    static simultaneousOpening = 'simultaneousopening';
    static open = 'orderShowOpen';
    static close = 'orderShowClose';

    /** @type {NodeListOf} */
    #orderItems;
    /** @type {HTMLElement} */
    #orderItemFocused;
    /** @type {HTMLElement} */
    #ordersList;
    /** @type {string} */
    #orderShowTemplate;
    /** @type {HTMLElement} */
    #orderShow;

    /** @type {CheckboxManager} */
    #checkboxManager;
    /** @type {AbortController} */
    #abortController;


    /**
     * 
     * @param {NodeListOf} orderItems
     * @param {HTMLElement} ordersList 
     * @param {string} orderShowTemplateSelector 
     * @param {CheckboxManager} checkboxManager
     */
    constructor(orderItems, ordersList, orderShowTemplateSelector, checkboxManager) {
        this.#checkboxManager = checkboxManager;
        this.#orderItems = orderItems;
        this.#ordersList = ordersList;
        this.#orderShowTemplate = document.querySelector(orderShowTemplateSelector).content;

        this.#orderItems.forEach(orderItem => {
            orderItem.addEventListener('click', e => this.#onOrderItemClick(e));
        })
    }

    /**
     * 
     * @param {MouseEvent} e 
     */
    #onOrderItemClick(e) {
        this.#cleanShow();
        this.#orderItemFocused = e.currentTarget;
        this.#orderItemFocused.classList.add('focus');
        this.#abortController = new AbortController();
        this.#openShow();
    }

    async #openShow() {
        try {
            const order = await this.#callApi(
                this.#orderItemFocused.dataset.entrypoint, 
                this.#orderItemFocused.dataset.id
                );
            this.#orderShow = this.#createOrderShow(order);
            this.#ordersList.append(this.#orderShow);
            this.#activeCloser();
            this.#checkboxManager.markOneAsSeen(this.#orderItemFocused);
            document.querySelector('.js-not-seen-counter').dispatchEvent(new CustomEvent('updatecounter'));
            this.#ordersList.dispatchEvent(new CustomEvent(OrderShowManager.open, {
                detail: this.#orderShow
            }));
        } catch(e) {
            //aborted
        }   
    }

    #activeCloser() {
        const closer = this.#ordersList.querySelector('.order-show-closer');
        closer.classList.add('visible');
        closer.addEventListener('click', e => this.#close());
    }
    #disactiveCloser() {
        const closer = this.#ordersList.querySelector('.order-show-closer');
        closer.classList.remove('visible');
    }

    #cleanShow() {
        if(this.#abortController) {
            this.#abortController.abort();
        }
        if(this.#orderItemFocused) {
            this.#orderItemFocused.classList.remove('focus');
        }
        if(this.#orderShow) {
            this.#orderShow.remove();
            this.#disactiveCloser();
        }
    }

    /**
     * 
     * @param {string} entrypoint 
     * @param {number} id 
     * @returns {Promise}
     */
    #callApi(entrypoint, id) {
        return myFetch(entrypoint, {
            method: 'POST',
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify(id),
            signal: this.#abortController.signal
        });
    }

    #close() {
        this.#ordersList.dispatchEvent(new CustomEvent(OrderShowManager.close));
        this.#orderItemFocused.classList.remove('focus');
        this.#orderShow.remove();
        this.#disactiveCloser();
    }

    /**
     * 
     * @param {Object} order 
     */
    #createOrderShow(order) {
        const orderShow = this.#orderShowTemplate.cloneNode(true).firstElementChild;
        for(const [selector, value] of Object.entries(order)) {
            orderShow.querySelector(selector).innerText = value;
        }
        const mail = orderShow.querySelector('.js-order-show-mail');
        mail.setAttribute('href', 'mailto:'+order['.js-order-show-mail']);
        mail.setAttribute('title', 'Répondre à '+order['.js-order-show-mail']);
        return orderShow;
    }
}