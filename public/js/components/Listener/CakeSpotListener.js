import { myFetch } from "../../functions/api.js";

export class CakeSpotListener {

    /*html title attr*/
    static spotOff = 'Enlever de la Une';
    static spotOn = 'Mettre à la Une';

    /** @type {HTMLElement} */
    #cakeItem;
    /** @type {HTMLElement} */
    #spotContainer;
    /** @type {HTMLElement} */
    #onlySpotlightedNav;


    /**
     * 
     * @param {HTMLElement} cakeItem 
     */
    constructor(cakeItem) {
        this.#cakeItem = cakeItem;
        this.#spotContainer = this.#cakeItem.querySelector('.cake-card-spot');
        this.#onlySpotlightedNav = document.querySelector(this.#spotContainer.dataset.onlyspotlightednav);
        this.#spotContainer.addEventListener('click', this.#onClick());
    }


    /**
     * 
     * @param {MouseEvent} e 
     */
    #onClick() {
        return async e => {
            e.preventDefault();
            e.stopPropagation();
            const add = this.#spotContainer.classList.contains('spot-on') ? -1: 1;
            this.#spotContainer.classList.toggle('spot-on');
            this.#setTitle(this.#spotContainer);
            if(this.#onlySpotlightedNav.classList.contains('active')) {
                this.#cakeItem.classList.add('removing');
                document.querySelectorAll('.i-spot').forEach(spot => {
                    spot.addEventListener('click', e => {
                        e.preventDefault();
                        e.stopPropagation();
                    })
                })
            }
            const res = await myFetch(this.#spotContainer.dataset.entrypoint);
            // if full spotlighted
            if(res.ok) {
                const countSpotlighted = document.querySelector('.js-count-spotlighted');
                countSpotlighted.innerText = (parseInt(countSpotlighted.innerText) + add).toString();
                countSpotlighted.classList.add('flash');
                setTimeout(() => {
                    countSpotlighted.classList.remove('flash');
                }, 2000);
            }
            else {
                this.#spotContainer.classList.remove('spot-on');
                this.#setTitle(this.#spotContainer);
                const flash = document.querySelector('#flash-danger-template').content.cloneNode(true).firstElementChild;
                flash.querySelector('.flash-message').innerText = res.message;
                const container = document.querySelector('.main-admin');
                if(container.querySelector('.alert')) {
                    container.querySelector('.alert').remove();
                }
                document.querySelector('.js-flashes').append(flash);
                setTimeout(() => {
                    flash.remove();
                }, 3000);
                window.scroll(0, 0);
                return;
            }

            if(this.#onlySpotlightedNav.classList.contains('active')) {
                document.querySelector(this.#spotContainer.dataset.form).submit();
            }
        }
       
    }

    /**
     * 
     * @param {HTMLElement} spotContainer 
     */
    #setTitle(spotContainer){
        if(spotContainer.classList.contains('spot-on')) {
            spotContainer.setAttribute('title', CakeSpotListener.spotOff);
        } else {
            spotContainer.setAttribute('title', CakeSpotListener.spotOn);
        }
    }
}

