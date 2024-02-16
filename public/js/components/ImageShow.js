
import { myFetchPost } from "../functions/api.js";
import { ImageShowPicturesManager } from "./CarouselPicturesManager.js";

export class ImageShow {

    /**Event */
    static showOpen = 'showopen';

    /** @type {HTMLElement} */
    #elt;

    /** @type {HTMLElement} cakes-list */
    #container;
    
    /** @type {HTMLElement} */
    #modal;

    /** @type {number} */
    #scroll;


    constructor(elt) {
        this.#elt = elt;
        this.#container = this.#elt.parentElement;

        this.#scroll = this.#calcScroll();
        window.scroll(0, this.#scroll);
        document.body.classList.add('frozen');
        this.#openShow();
    }

    async #openShow() {
        this.#modal = document.querySelector('#image-show-template').content.cloneNode(true).firstElementChild;
        document.body.append(this.#modal);
        this.#modal.classList.add('loading');

        const cakeData = await myFetchPost(this.#container.dataset.entrypoint, this.#elt.dataset.id);
        const hasSecondaryPictures = this.#hydrateShow(cakeData);

        this.#elt.dispatchEvent(new CustomEvent(ImageShow.showOpen, {bubbles: true}));

        this.#modal.classList.remove('loading');
        this.#modal.querySelector('.image-show').classList.add('visible');
        this.#listenToClose();
        new ImageShowPicturesManager(this.#modal, '.image-show-carousel', hasSecondaryPictures);
    }

    async #listenToClose() {
        this.#modal.addEventListener('click', e => this.#close());
        this.#modal.querySelector('.image-show').addEventListener('click', e => {
            e.stopPropagation();
        });
        this.#modal.querySelector('.image-show-closer').addEventListener('click', e => this.#close());
    }

    #close() {
        this.#modal.querySelector('.image-show').classList.add('close');
        this.#modal.classList.add('close');
        this.#modal.addEventListener('animationend', e => {
            this.#modal.remove();
            document.body.classList.remove('frozen');
        })
    }

    #calcScroll() {
        const eltY = this.#elt.getBoundingClientRect().top + window.scrollY;
        const eltCenterY = eltY + (this.#elt.getBoundingClientRect().height / 2);
        return eltCenterY - (window.innerHeight / 2);
    }
    /**
     * 
     * @param {array} cakeData 
     */
    #hydrateShow(cakeData) {
        if(cakeData.title) {
            this.#modal.querySelector('.image-show-title').innerText = cakeData.title;
        } else {
            this.#modal.querySelector('.image-show-title').remove();
        }
        if(cakeData.description) {
            this.#modal.querySelector('.image-show-description').innerText = cakeData.description;
        } else {
            this.#modal.querySelector('.image-show-description').remove();
        }
        const carousel = this.#modal.querySelector('.image-show-carousel');
        const smallList = this.#modal.querySelector('.small-list');

        //ajout de la first picture
        carousel.append(this.#getCarouselItem(cakeData.firstPicture, 0));
        // ajout des secondary pictures
        if(cakeData.secondaryPictures) {
            smallList.append(this.#getSmallImg(cakeData.firstPicture, 0));
            let i = 0;
            for(const picture of cakeData.secondaryPictures) {
                i++;
                carousel.append(this.#getCarouselItem(picture, i));
                smallList.append(this.#getSmallImg(picture, i));
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param {string} pictureUrl
     * @param {number} id
     * @returns {HTMLElement}
     */
    #getCarouselItem(pictureUrl, id) {
        const item = document.querySelector('#image-show-carousel-item-template').content.cloneNode(true).firstElementChild;
        item.querySelector('.image-show-carousel-img').style.background = 'url('+pictureUrl+') center center / cover no-repeat';
        item.setAttribute('href', pictureUrl);
        item.dataset.id = id;
        return item;
    }

    /**
     * 
     * @param {string} pictureUrl
     * @param {number} id
     * @returns {HTMLElement}
     */
    #getSmallImg(pictureUrl, id) {
        const smallImg = document.querySelector('#small-img-template').content.cloneNode(true).firstElementChild;
        smallImg.style.background = 'url('+pictureUrl+') center center / cover no-repeat';
        smallImg.dataset.id = id;
        if(id === 0) {
            smallImg.classList.add('active');
        }
        return smallImg;
    }


    

}