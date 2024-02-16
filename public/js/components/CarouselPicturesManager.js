export class ImageShowPicturesManager {
    /** @type {HTMLElement} */
    #show;
    /** @type {HTMLElement} */
    #smallImgs;
    /** @type {number} */
    #smallImgActiveOffset = 0;

    /** @type {HTMLElement} */
    #carouselEltSelector;

    /**
     * @param {HTMLElement} show
     * @param {string} carouselEltSelector 
     * @param {boolean} hasSecondaryPictures 
     */
    constructor(show, carouselEltSelector, hasSecondaryPictures) {
        this.#show = show;
        this.#carouselEltSelector = carouselEltSelector;
        $(this.#carouselEltSelector).slick({arrows: false});

        if(hasSecondaryPictures) {
            //mise en place listeners flèches
            this.#specialArrows();
        } else {
            //suppression des flèches
            this.#show.querySelectorAll('.image-show-car-arrow')
                        .forEach(function(arrow) {
                            arrow.remove();
                        })
        }
        this.#registerSmallImgs();
        $(this.#carouselEltSelector).on('afterChange', (event, slick, direction) => this.#onChange(direction));
    }

    #specialArrows() {
        const carouselEltSelector = this.#carouselEltSelector;
        /* flèches perso */
        this.#show.querySelector('.image-show-car-arrow-left').addEventListener('click', function(e) {
            $(carouselEltSelector).slick('slickPrev');
        });
        this.#show.querySelector('.image-show-car-arrow-right').addEventListener('click', function(e) {
            $(carouselEltSelector).slick('slickNext');
        });
    }


    /**
     * 
     * @param {Event} e 
     */
    #onChange(direction) {
        this.#smallImgs[this.#smallImgActiveOffset].classList.remove('active');
        this.#smallImgs[direction].classList.add('active');
        this.#smallImgActiveOffset = direction;
    }

    #registerSmallImgs() {
        this.#smallImgs = this.#show.querySelectorAll('.small-img');
        for(const smallImg of this.#smallImgs) {
            smallImg.addEventListener('click', e => this.#onSmallImgClick(e));
        }
    }

    #onSmallImgClick(e) {
        const dir = e.currentTarget.dataset.id;
        $(this.#carouselEltSelector).slick('slickGoTo', dir);
    }
}



