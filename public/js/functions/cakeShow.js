import { ImageShow } from "../components/ImageShow.js";
import { myFetchPost } from "./api.js";
import { onStaticClick } from "./spatial.js";



export function manageCakeShow(cakeCard) {
    onStaticClick(cakeCard, (cakeCard) => {
        new ImageShow(cakeCard, '#image-show-template');
        cakeVisitCount(
            cakeCard.dataset.id, 
            cakeCard.parentElement.dataset.apivisitcake
            );
    });
}




function cakeVisitCount(cakeId, entrypoint) {
    const visit = sessionStorage.getItem('visit') ? JSON.parse(sessionStorage.getItem('visit')): [];
    if(!visit.includes(cakeId)) {
        visit.push(cakeId);
        sessionStorage.setItem('visit', JSON.stringify(visit));
        myFetchPost(entrypoint, cakeId);
    }
}