export class PhoneFieldManager {


    static phoneValidated = 'phonevalidated';

    /** @type {HTMLElement} */
    #inputElt;

    /** @type {string} */
    #value;

    /** @type {string} */
    #lastCar;



    /**
     * 
     * @param {HTMLElement} inputElt
     */
    constructor(inputElt) {
        this.#inputElt = inputElt;
        this.#inputElt.addEventListener('input', e => this.#onInput(e));
        this.#inputElt.addEventListener('click', e => this.#moveCursorToEnd());
        this.#inputElt.addEventListener('focus', e => this.#moveCursorToEnd());
        this.#inputElt.addEventListener('keydown', e => this.#onKeydown(e));
    }

    #onKeydown(e) {
        if([
            'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'
        ].includes(e.code)) {
            e.preventDefault();
        }
    }

    #moveCursorToEnd() {
        const inputElt = this.#inputElt;
        setTimeout(function(){
            inputElt.selectionStart = inputElt.selectionEnd = inputElt.value.length;
            inputElt.focus();
        }, 0);
    }

    /**
     * 
     * @param {Event} e 
     */
    #onInput(e) {
        this.#value = e.target.value;
        this.#lastCar = e.target.value.substr(-1);

        if(e.inputType === 'deleteContentBackward') {
            this.#onDelete();
        } else if(e.inputType === 'insertFromPaste') {
            this.#onPaste();
        } else if(e.inputType === 'insertText') {
            this.#onInsert()
        }
    }

    #onDelete() {
        if([2, 5, 8, 11].includes(this.#value.length)) {
            this.#inputElt.value = this.#value.slice(0, -1);
        }
    }

    #onPaste() {
        const entryArray = this.#value.split('');
        let sortedValue = '';
        for(let i in entryArray) {
            if(!entryArray[i].match(/^\(?([0-9])\)?$/)) {
                break;
            }
            if(sortedValue.length >= 4) {
                break;
            }
            sortedValue += entryArray[i];
            if([2, 5, 8, 11].includes(sortedValue.length)) {
                sortedValue += ' ';
            }
        }
        this.#inputElt.value = sortedValue;
    }

    #onInsert() {
        if(!this.#lastCar.match(/^\(?([0-9])\)?$/) || this.#value.length > 14) {
            this.#inputElt.value = this.#value.slice(0, -1);
          } else if([2, 5, 8, 11].includes(this.#value.length)) {
            this.#inputElt.value = this.#value + ' ';
          } else if(this.#value.length === 14) {
            this.#inputElt.dispatchEvent(new CustomEvent((PhoneFieldManager.phoneValidated)));
            this.#inputElt.blur();
          } else if(this.#value.length === 1) {
            if(this.#value !== '0') {
              this.#inputElt.value = '0' + this.#value + ' ';
            }
          }
    }
    
}

