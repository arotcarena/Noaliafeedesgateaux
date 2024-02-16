/**
 * renvoie true si le click a été fait sur l'élément, sinon false
 * @param {MouseEvent} e 
 * @param {HTMLElement} elt 
 * @returns {boolean}
 */
export function clickIsOnElement(e, elt) {
    const rect = elt.getBoundingClientRect();
    return (
        e.clientX >= rect.x
        &&
        e.clientX <= rect.x + rect.width
        &&
        e.clientY >= rect.y 
        &&
        e.clientY <= rect.y + rect.height
    );
}


/**
 * 
 * @param {HTMLElement} elt 
 * @returns {{top: number, bottom: number, right: number, left: number}}
 */
export function getPosition(elt) {
    return {
        top: getTopPosition(elt),
        bottom: getBottomPosition(elt),
        left: getLeftPosition(elt),
        right: getRightPosition(elt),
    }
}



export function getTopPosition(elt) {
    if(elt.parentElement) {
        return elt.offsetTop + getTopPosition(elt.parentElement);
    }
    return elt.offsetTop;
}
export function getBottomPosition(elt) {
    if(elt.parentElement) {
        return elt.offsetBottom + getBottomPosition(elt.parentElement);
    }
    return elt.offsetBottom;
}
export function getLeftPosition(elt) {
    if(elt.parentElement) {
        return elt.offsetLeft + getLeftPosition(elt.parentElement);
    }
    return elt.offsetLeft;
}
export function getRightPosition(elt) {
    if(elt.parentElement) {
        return elt.offsetRight + getRightPosition(elt.parentElement);
    }
    return elt.offsetRight;
}


/**
 * 
 * @param {HTMLElement} elt 
 */
export function onStaticClick(elt, callableToApply) {
    elt.addEventListener('mousedown', function(e) {
        const x = e.clientX;
        const y = e.clientY;
        const onMouseUp = e => {
            if(e.button !== 0) {
                e.currentTarget.removeEventListener('mouseup', onMouseUp);
                return;
            }
            if(e.clientX > (x - 20) && e.clientX < (x + 20) && e.clientY > (y - 20) && e.clientY < (y + 20)) {
                e.currentTarget.removeEventListener('mouseup', onMouseUp);
                callableToApply(elt);
            }
        }
        e.currentTarget.addEventListener('mouseup', onMouseUp);
    });
}
