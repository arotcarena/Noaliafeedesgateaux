export class Scrollspy {

    /** @type {NodeList} */
    #spies;
    /** @type {HTMLElement} */
    #navitem;
    /** @type {number} */
    #windowH;
    /** @type {number} */
    #timeout;

    #timer;

    /** @type {HTMLElement} */
    #lastActiveItem;
    /** @type {HTMLElement|null} */
    #navitemsContainer;

    /** @type {IntersectionObserver} */
    #observer;

    constructor(spies, navitem, navitemsContainer = null, timeout = 500) {
        this.#spies = spies;
        this.#navitem = navitem;
        this.#navitemsContainer = navitemsContainer;
        this.#timeout = timeout;

        this.init();

        window.addEventListener('resize', e => this.#onResize());
    }

    init() {
        this.#observer?.disconnect();

        const y = window.innerHeight * 50 / 100;
        const marginBottom = window.innerHeight - y - 1;
        this.#observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    if(this.#navitemsContainer.querySelector('.active') && this.#navitemsContainer.querySelector('.active') !== this.#navitem) {
                        this.#lastActiveItem = this.#navitemsContainer.querySelector('.active');
                        this.#lastActiveItem.classList.remove('active');
                    }
                    this.#navitem.classList.add('active');
                } else {
                    this.#navitem.classList.remove('active');
                    this.#lastActiveItem?.classList.add('active');
                }
            })
        }, {rootMargin: `-${y}px 0px -${marginBottom}px 0px`});

        this.#spies.forEach(spy => {
            this.#observer.observe(spy);
        })
    }

    #onResize() {
        if(this.#windowH !== window.innerHeight) {
            this.#windowH = window.innerHeight;
            if(this.#timer) {
                clearTimeout(this.#timer);
            }
            const scrollspy = this;
            this.#timer = setTimeout(() => { 
                scrollspy.init(); 
            }, this.#timeout);
        }
    }
}