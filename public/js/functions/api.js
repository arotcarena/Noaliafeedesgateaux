/**
 * 
 * @param {string} url 
 * @param {Object} options 
 */
export async function myFetch(url, options) {
    const res = await fetch(url, options);
    if(res.ok) {
        return res.json();
    }
    throw new Error('server error');
}


export async function myFetchPost(url, data) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            "Accept": "application/json",
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    });
    if(res.ok) {
        return res.json();
    }
    throw new Error('server error');
}

