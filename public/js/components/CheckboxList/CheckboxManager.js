export class CheckboxManager {

    //STATUS TO MARK
    static status_seen = 'seen';
    static status_not_seen = 'notSeen'

    /** @type {NodeListOf} */
    #checkboxes;
    /** @type {HTMLElement} */
    #masterCheckbox;
    /** @type {HTMLElement} */
    #delete;
    /** @type {HTMLElement} */
    #markStatus;
    /** @type {array} */
    checked = [];
    /** @type {array} */
    seenChecked = [];
    /** @type {array} */
    notSeenChecked = [];
    /** @type {number[]} */
    idsToMark = [];

    /** @type {string} */
    #checkboxSelector;

    constructor(
        checkboxSelector,
        masterCheckboxSelector,
        deleteControlSelector,
        markStatusSelector
    ) {
        this.#checkboxSelector = checkboxSelector;
        this.#checkboxes = document.querySelectorAll(checkboxSelector);
        this.#masterCheckbox = document.querySelector(masterCheckboxSelector);
        this.#delete = document.querySelector(deleteControlSelector);
        this.#markStatus = document.querySelector(markStatusSelector);

        for(const checkbox of this.#checkboxes) {
            checkbox.addEventListener('click', e => this.#onCheckboxClick(e));
        }
        
        this.#masterCheckbox.addEventListener('click', e => this.#onMasterCheckboxClick(e));

    }

    verificationsOnChange() {
        //on vérifie si on doit cocher ou décocher le masterCheck
        if(this.checked.length === this.#checkboxes.length && !this.#masterCheckbox.classList.contains('checked')) {
            this.#masterCheck();
        } else if(this.checked.length !== this.#checkboxes.length && this.#masterCheckbox.classList.contains('checked')) {
            this.#masterUncheck();
        }

        //s'il y a des éléments sélectionnés, on active les controls, sinon on les désactive
        if(this.checked.length > 0) {
            this.#delete.classList.add('active');
            this.#markStatus.classList.add('active');
        } else {
            this.#delete.classList.remove('active');
            this.#markStatus.classList.remove('active');
            return;
        }
        // s'il y a des non lus sélectionnés, on propose de marquer lu, sinon on propose de marquer non lu
        if(this.notSeenChecked.length > 0) {
            this.#markStatus.classList.add('control-mark-seen');
            this.#markStatus.setAttribute('title', 'marquer lu');
            this.#markStatus.classList.remove('control-mark-not-seen');
        } else {
            this.#markStatus.classList.add('control-mark-not-seen');
            this.#markStatus.setAttribute('title', 'marquer non lu');
            this.#markStatus.classList.remove('control-mark-seen');
        }
    }

    /**
     * 
     * @param {MouseEvent} e 
     */
    #onCheckboxClick(e) {
        e.preventDefault();
        e.stopPropagation();
        const checkbox = e.currentTarget;
        if(!checkbox.classList.contains('checked')) {
            this.#check(checkbox);
        } else {
            this.#uncheck(checkbox);
        }
        this.verificationsOnChange();
    }

    /**
     * 
     * @param {MouseEvent} e 
     */
    #onMasterCheckboxClick(e) {
        if(!this.#masterCheckbox.classList.contains('checked')) {
            this.#checkboxes.forEach(checkbox => {
                this.#check(checkbox);
            });
            this.#masterCheck();
        } else {
            this.#checkboxes.forEach(checkbox => {
                this.#uncheck(checkbox);
            });
            this.#masterUncheck();
        }
        this.verificationsOnChange();
    }

    #masterCheck() {
        this.#masterCheckbox.classList.add('checked');
        this.#masterCheckbox.setAttribute('title', 'Tout désélectionner');
    }

    #masterUncheck() {
        this.#masterCheckbox.classList.remove('checked');
        this.#masterCheckbox.setAttribute('title', 'Tout sélectionner');
    }

    /**
     * 
     * @param {HTMLElement} checkbox 
     */
    #check(checkbox) {
        if(!checkbox.classList.contains('checked')) {
            checkbox.classList.add('checked');
            this.checked.push(checkbox);

            if(checkbox.dataset.seen === '1') {
                this.seenChecked.push(checkbox);
            } else {
                this.notSeenChecked.push(checkbox);
            }
        }
    }
    /**
     * 
     * @param {HTMLElement} checkbox 
     */
    #uncheck(checkbox) {
        if(checkbox.classList.contains('checked')) {
            checkbox.classList.remove('checked');
            this.checked = this.removeItem(this.checked, checkbox);
            if(checkbox.dataset.seen === '1') {
                this.seenChecked = this.removeItem(this.seenChecked, checkbox);
            } else {
                this.notSeenChecked = this.removeItem(this.notSeenChecked, checkbox);
            }
        }
    }


    /**
     * 
     * @param {array} nodeList 
     * @param {HTMLElement} elt 
     * @returns {array}
     */
    removeItem(nodeList, elt) {
        return nodeList.filter(item => item !== elt);
    }

    /**
     * 
     * @param {HTMLElement} orderItem 
     */
    markOneAsSeen(orderItem) {
        orderItem.classList.remove('active');
        const checkbox = orderItem.querySelector(this.#checkboxSelector);
        checkbox.dataset.seen = '1';
        if(this.notSeenChecked.includes(checkbox)) {
            this.notSeenChecked = this.removeItem(this.notSeenChecked, checkbox);
            this.seenChecked.push(checkbox);
        }
        this.verificationsOnChange();
    }


    markAsSeen() {
        this.idsToMark = [];
        this.notSeenChecked.forEach(checkbox => {
            this.idsToMark.push(checkbox.dataset.id);
            checkbox.parentElement.parentElement.classList.remove('active');
            checkbox.dataset.seen = '1';
            this.seenChecked.push(checkbox);
        });
        this.notSeenChecked = [];
        this.verificationsOnChange();
    }

    markAsNotSeen() {
        this.idsToMark = [];
        this.seenChecked.forEach(checkbox => {
            this.idsToMark.push(checkbox.dataset.id);
            checkbox.parentElement.parentElement.classList.add('active');
            checkbox.setAttribute('data-seen', '');
            this.notSeenChecked.push(checkbox);
        });
        this.seenChecked = [];
        this.verificationsOnChange();
    }

    getStatusToMark() {
        return this.#markStatus.classList.contains('control-mark-seen') 
                ? CheckboxManager.status_seen 
                : CheckboxManager.status_not_seen;
    }


    /**
     * 
     * @returns {number[]}
     */
    getCheckedIds() {
        let ids = [];
        this.checked.forEach(checkbox => {
            ids.push(checkbox.dataset.id);
        })
        return ids;
    }
}
