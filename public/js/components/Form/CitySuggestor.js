import { myFetch } from "../../functions/api.js";
import { CloseHandler } from "../CloseHandler.js";
import { Config } from "../Config.js";
import { Suggestor } from "./Suggestor.js";

export class CitySuggestor {

    //type des events émis par le autoSuggestor
    static suggestValidated = 'suggestValidated';
    static suggestMouseValidated = 'suggestMouseValidated';
    static suggestClose = 'suggestClose';
    static suggestOpen = 'suggestOpen';
    //type de validation
    static keyboardValidation = 'keyboardValidation';
    static mouseValidation = 'mouseValidation';


    /** @type {HTMLElement} */
    #inputElt;

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
            this.#onChoice(currentItem, CitySuggestor.keyboardValidation);
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
        this.#inputElt.dataset.x = '';
        this.#inputElt.dataset.y = '';
        this.#inputElt.dataset.city = '';
        this.#inputElt.dataset.postcode = '';
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
        this.#inputElt.dispatchEvent(new CustomEvent(CitySuggestor.suggestOpen));
        this.#container.classList.add('loading');

        try {
            const data = await this.#callApi(q);
            if(data.features.length === 0) {
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

        const url = Config.apiAddress + '&limit=5&q=' + q.replace(' ', '+');
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
        for(const feature of data.features) {
            const label = feature.properties.name + ' (' + feature.properties.postcode + ')';
            const x = feature.properties.x * 100;
            const y = feature.properties.y * 100;
            const city = feature.properties.name;
            const postcode = feature.properties.postcode;
            this.#suggestList.append(this.#createItem(label, x, y, city, postcode));
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
     * @param {string} label 
     * @param {string} x 
     * @param {string} y 
     * @param {string} city
     * @param {string} postcode
     * @returns {HTMLElement}
     */
    #createItem(label, x, y, city, postcode) {
        const item = document.createElement('li');
        item.classList.add('suggest-item');
        item.setAttribute('value', label);
        item.innerText = label;
        item.dataset.x = x;
        item.dataset.y = y;
        item.dataset.city = city;
        item.dataset.postcode = postcode;
        item.addEventListener('mouseover', e => this.#onMouseoverItem(e));
        item.addEventListener('click', e => this.#onChoice(e.currentTarget, CitySuggestor.mouseValidation))
        return item;
    }

    #onMouseoverItem(e) {
        const currentItem = this.#suggestList.querySelector('.suggest-item.active');
        if(currentItem) {
            currentItem.classList.remove('active');
        }
        e.currentTarget.classList.add('active');
    }

    /**
     * 
     * @param {HTMLElement} choiceItem 
     * @param {string} validationType (either 'keyboardValidation' or 'mouseValidation')
     */
    #onChoice(choiceItem, validationType) {
        this.#inputElt.value = choiceItem.getAttribute('value');
        this.#inputElt.dataset.x = choiceItem.dataset.x;
        this.#inputElt.dataset.y = choiceItem.dataset.y;
        this.#inputElt.dataset.city = choiceItem.dataset.city;
        this.#inputElt.dataset.postcode = choiceItem.dataset.postcode;
        this.#inputElt.dataset.suggestvalidated = '1';
        this.#inputElt.dispatchEvent(new CustomEvent(CitySuggestor.suggestValidated, {bubbles: true, detail: validationType}));
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
        this.#inputElt.dispatchEvent(new CustomEvent(CitySuggestor.suggestClose));
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


