import { myFetch } from "../../functions/api.js";
import { CloseHandler } from "../CloseHandler.js";

export class Suggestor {

    //type des events émis par le autoSuggestor
    static suggestValidated = 'suggestValidated';
    static suggestClose = 'suggestClose';
    static suggestOpen = 'suggestOpen';
    //event reçu par Suggestor
    static mustClose = 'mustclose';

    /** @type {HTMLElement} */
    #inputElt;

    /** @type {string} */
    #entryPoint;

    /** @type {number} */
    #minQLength = 3;
   
    /** @type {HTMLElement} */
    #container;

    /** @type {HTMLElement} */
    #suggestList

    /** @type {CloseHandler} */
    #closeHandler

    /** @type {boolean} */
    #open = false;

    /** @type {AbortController} */
    #controller;

    /** @type {number} timestamp (ms)*/
    #lastFetchCall;

    /** @type {number} (ms) */
    #timeout = 300;

    /** @type {number} */
    #apiRetryDelay = 15000;

    /** @type {number} */
    #apiLastErrorTimestamp

    /**
     * 
     * @param {HTMLElement} inputElt 
     */
    constructor(inputElt) {
        this.#inputElt = inputElt;
        this.#entryPoint = this.#inputElt.dataset.entrypoint;
        this.#container = this.#inputElt.parentElement;

        this.#suggestList = this.#createList();
        this.#container.append(this.#suggestList);

        this.#inputElt.addEventListener('input', e => this.#onInput(e));
        this.#inputElt.addEventListener('keydown', e => this.#onKeydown(e));
    }

    #onKeydown(e) {
        if(!this.#open || !['Enter', 'ArrowDown', 'ArrowUp'].includes(e.code)) {
            return;
        }
        e.preventDefault();
        const currentItem = this.#suggestList.querySelector('.suggest-item.active');
        if(e.code === 'Enter' && currentItem) {
                currentItem.dispatchEvent(new MouseEvent('click'));
        }
        if(['ArrowDown', 'ArrowUp'].includes(e.code)) {
            if(currentItem) {
                currentItem.classList.remove('active');
            }
            if(e.code === 'ArrowDown') {
                if(currentItem && currentItem.nextElementSibling) {
                    currentItem.nextElementSibling.classList.add('active');
                } else {
                    this.#suggestList.querySelector('.suggest-item:first-of-type').classList.add('active');
                }
            } else {
                if(currentItem && currentItem.previousElementSibling) {
                    currentItem.previousElementSibling.classList.add('active');
                } else {
                    this.#suggestList.querySelector('.suggest-item:last-of-type').classList.add('active');
                }
            }
        }
    }

    async #onInput(e) {
        this.#inputElt.dataset.suggestvalidated = '0';
        
        if(this.#apiLastErrorTimestamp + this.#apiRetryDelay > Date.now()) {
            this.#container.classList.remove('loading');
            return;
        }
        if(Date.now() - this.#lastFetchCall < this.#timeout) {
            this.#container.classList.remove('loading');
            this.#controller.abort();
        }

        const q = e.target.value;
        if(q.length < this.#minQLength) {
            this.#container.classList.remove('loading');
            if(this.#open) {
                this.#close();
            }
            return;
        }
        this.#inputElt.dispatchEvent(new CustomEvent(Suggestor.suggestOpen));
        this.#container.classList.add('loading');

        try {
            const data = await this.#callApi(q);
            if(data.length === 0) {
                this.#container.classList.remove('loading');
                if(this.#open) {
                    this.#close();
                }
                return;
            }
            
            this.#loadList(data);
            this.#container.classList.remove('loading');
            if(!this.#open) {
                this.#openList();
            }
        } catch(e) {
            if(e instanceof DOMException) {  //si e est une instance de DOMException, il s'agit d'un controller.abort(), donc pas de pb
                return;
            }
            this.#apiLastErrorTimestamp = Date.now();           
            this.#container.classList.remove('loading');
            if(this.#open) {
                this.#close();
            }
        }        
    }

    #callApi(q) {

        const url = this.#entryPoint + '?q=' + q.replace(' ', '+');
        this.#controller = new AbortController();
        this.#lastFetchCall = Date.now();
        return myFetch(url, {
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            signal: this.#controller.signal
        });
    }

    async #loadList(data) {
        this.#suggestList.innerHTML = '';
        for(const d of data) {
            this.#suggestList.append(this.#createItem(d))
        }
    }

    /**
     * 
     * @returns {HTMLElement}
     */
    #createList() {
        const list = document.createElement('ul');
        list.classList.add('suggest-list');
        return list;
    }

    /**
     * 
     * @param {string} value 
     * @returns {HTMLElement}
     */
    #createItem(value) {
        const item = document.createElement('li');
        item.classList.add('suggest-item');
        item.setAttribute('value', value);
        item.innerText = value;
        item.addEventListener('mouseover', e => this.#onMouseoverItem(e));
        item.addEventListener('click', e => this.#onChoice(e))
        return item;
    }

    #onMouseoverItem(e) {
        const currentItem = this.#suggestList.querySelector('.suggest-item.active');
        if(currentItem) {
            currentItem.classList.remove('active');
        }
        e.currentTarget.classList.add('active');
    }

    #onChoice(e) {
        this.#inputElt.value = e.currentTarget.getAttribute('value');
        this.#inputElt.dataset.suggestvalidated = '1';
        this.#inputElt.dispatchEvent(new CustomEvent(Suggestor.suggestValidated, {bubbles: true}));
        this.#close();
        this.#inputElt.blur();
    }

    async #openList() {
        this.#suggestList.classList.add('visible');
        this.#open = true;

        this.#suggestList.addEventListener(Suggestor.mustClose, e => this.#close());
        
        //si on met le focus sur un autre champ, on ferme la suggest-list
        const form = this.#findParentFormElement(this.#inputElt);
        if(form) {
            form.addEventListener('focusin', e => {
                if(e.target !== this.#inputElt) {
                    this.#close();
                }
            });
        }

        this.#closeHandler = new CloseHandler(this.#suggestList);
        await this.#closeHandler.start();
        this.#close();
    }

    #close() {
        this.#inputElt.dispatchEvent(new CustomEvent(Suggestor.suggestClose));
        this.#suggestList.innerHTML = '';
        this.#suggestList.classList.remove('visible');
        this.#closeHandler.stop();
        this.#open = false;
        this.#controller.abort();
        this.#container.classList.remove('loading');
    }

    /**
    * 
    * @param {HTMLElement} elt 
    * @returns {HTMLFormElement|null}
    */
    #findParentFormElement(elt) {
        if(!elt.parentElement) {
            return null;
        }
        if(elt.parentElement.querySelector('form')) {
            return elt.parentElement.querySelector('form');
        } else {
            return this.#findParentFormElement(elt.parentElement);
        }
    }
}




