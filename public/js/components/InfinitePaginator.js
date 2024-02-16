import { myFetchPost } from "../functions/api.js"

export class InfinitePaginator {

    /**event dispatched */
    static newItems = 'infinitepaginationnewitems';

    /** @type {HTMLElement} */
    #control;

    /** @type {string} */
    #entrypoint;

    /** @type {HTMLElement} */
    #container;

    /** @type {HTMLTemplateElement} */
    #template;

    /** @type {IntersectionObserver} */
    #observer;

    /** @type {boolean} */
    #isLoading = false;

    /** @type {NodeList} */
    #followingContent;

    #applyToEachEltLoaded;

    /** @type {bool} */
    isAlive = false;

    constructor(controlSelector, applyToEachEltLoaded) {
        this.#applyToEachEltLoaded = applyToEachEltLoaded;
        this.#control = document.querySelector(controlSelector);
        this.#followingContent = document.querySelectorAll(controlSelector + ' ~ *');

        this.#entrypoint = this.#control.dataset.entrypoint;
        this.#container = document.querySelector(this.#control.dataset.container);
        this.#template = document.querySelector(this.#control.dataset.template).content;
    }

    start() {
        this.#followingDisappear();
        this.#control.classList.remove('disappear');
        this.#observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if(entry.intersectionRatio > 0.9) {
                    this.#onIntersection();
                }
            })
        }, {
            threshold: [0.8, 0.9, 1]
        });
        this.#observer.observe(this.#control);
        this.isAlive = true;
    }

    #followingDisappear() {
        this.#followingContent.forEach(elt => {
            elt.classList.add('disappear');
        })
    }
    #followingAppear() {
        this.#followingContent.forEach(elt => {
            elt.classList.remove('disappear');
        })
    }

    async #onIntersection() {
        if(this.#isLoading) {
            return;
        }
        this.#isLoading = true;

        const data = await myFetchPost(this.#entrypoint, {
            offset: this.#control.dataset.offset
        });
        if(data.isEmpty) {
            this.stop()
        } else {
            this.#control.dataset.offset = data.newOffset;
            this.#viewItems(data.items);
            if(data.isLast) {
                this.stop();
            }
        }
        this.#isLoading = false;
    }


    /**
     * 
     * @param {array} items 
     * @returns {HTMLElement[]}
     */
    #viewItems(items) {
        for(const item of items) {
            this.#container.append(
                this.#createItem(item)
                );
        }
    }

    /**
     * 
     * @param {array} item
     * @returns {HTMLElement}
     */
    #createItem(item) {
        const card = this.#template.cloneNode(true).firstElementChild;
        card.dataset.id = item.id;
        if(item.title) {
            card.querySelector('.board p').innerText = item.title;
        } else {
            card.querySelector('.board').remove();
        }
        card.style.background = 'url('+item.firstPicture+') center center / cover no-repeat';
        this.#applyToEachEltLoaded(card);
        return card;
    }

    stop() {
        this.#observer.disconnect();
        this.#followingAppear();
        this.#control.classList.add('disappear');
        this.isAlive = false;
    }

    /**
     * 
     * 
     */
    onScrollOver() {
        let scroll = window.scrollY;
        const firstFollowing = this.#followingContent[0];
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    scroll = window.scrollY;
                } else {
                    if(window.scrollY < scroll) {
                        this.start();
                    }
                }
            });
        });
        observer.observe(firstFollowing);
    }

}