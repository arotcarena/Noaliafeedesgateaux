import { InfinitePaginator } from "../../components/InfinitePaginator.js";
import { manageCakeShow } from "../../functions/cakeShow.js";

const appearsObserver = new IntersectionObserver((entries) => {
    entries.forEach(function(entry) {
        // entry.target.style.opacity = entry.intersectionRatio;    A VOIR AVEC JESS QUEL EFFET ELLE PREFERE
        if(entry.isIntersecting) {
            entry.target.classList.add('appears-on');
        }
    })
});

document.querySelectorAll('.appears, .appears-later, .appears-faster').forEach(function(elt) {
    appearsObserver.observe(elt);
});



const infinitePaginator = new InfinitePaginator(
    '.infinite-pagination-control',
    cakeCard => {
        manageCakeShow(cakeCard);
        appearsObserver.observe(cakeCard);
        if(cakeCard.querySelector('.board')) {
            appearsObserver.observe(cakeCard.querySelector('.board'));
        }
    }
);
infinitePaginator.start();


/* passer-commande */
document.querySelector('.js-order-button').addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    if(infinitePaginator.isAlive) {
        infinitePaginator.stop();
        document.querySelector('.fullName-input').focus();
        infinitePaginator.onScrollOver();
    } else {
        document.querySelector('.fullName-input').focus();
    }
  });



