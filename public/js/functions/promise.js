/**
 * 
 * @param {number} duration (ms) 
 */
export function wait(duration) {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            resolve('ok');
        }, duration);
    })
}