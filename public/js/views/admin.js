import { AdminDetailsFormManager } from "../components/AdminDetailsFormManager.js";
import { CheckboxDeleteManager } from "../components/CheckboxList/CheckboxDeleteManager.js";
import { CheckboxManager } from "../components/CheckboxList/CheckboxManager.js";
import { CheckboxMarkStatusManager } from "../components/CheckboxList/CheckboxMarkStatusManager.js";
import { CloseHandler } from "../components/CloseHandler.js";
import { Config } from "../components/Config.js";
import { DistanceCalculator } from "../components/DistanceCalculator.js";
import { Suggestor } from "../components/Form/Suggestor.js";
import { CakeSpotListener } from "../components/Listener/CakeSpotListener.js";
import { OrderShowManager } from "../components/OrderShowManager.js";
import { myFetch } from "../functions/api.js";


const navbar = document.querySelector('.navbar');
/*hidden-menu*/
navbar.querySelector('.js-hidden-menu-opener').addEventListener('click', function(e) {
  if(!navbar.classList.contains('open')) {
    navbar.classList.add('open');
    (new CloseHandler(navbar, document.querySelector('.js-hidden-menu-closer')))
    .start()
    .then(e => {
        navbar.classList.remove('open');
    })
  } else {
    navbar.classList.remove('open');
  }
});


/* filter spot */
document.querySelectorAll('.js-filter-spotlighted, .js-filter-all')
        .forEach(function(elt) {
            elt.addEventListener('click', function(e) {
                const form = document.querySelector('.filter-search-form');
                form.querySelector('[name=spotlighted]').value = e.currentTarget.dataset.value;
                form.submit();
            });
        });

/* spot toggle */
document.querySelectorAll('.admin-cake-card')
        .forEach(function(cakeItem) {
            new CakeSpotListener(cakeItem);
        })


/* cakes sort oldest/latest */
if(document.querySelector('.js-cakes-select')) {
    document.querySelector('.js-cakes-select').addEventListener('change', function(e) {
        document.querySelector('.filter-search-form').submit();
    })
}



/*q auto suggest*/
if(document.querySelector('.js-auto-suggest')) {
    new Suggestor(document.querySelector('.js-auto-suggest'));

    document.querySelector('.filter-search-form').addEventListener(Suggestor.suggestValidated, function(e) {
        e.currentTarget.submit();
    })
}



/* picture delete */
document.querySelectorAll('.js-picture-delete')
        .forEach(function(elt) {
            elt.addEventListener('click', function(e) {
                const confirm = window.confirm('Voulez-vous vraiment supprimer cette photo ?');
                if(confirm) {
                    const elt = e.currentTarget;
                    myFetch(elt.dataset.entrypoint + '?id=' + elt.parentElement.dataset.pictureid)
                    .then(function(value) {
                        // A FAIRE AJOUTER UN FLASH
                        elt.parentElement.remove();
                    });
                }
            });
        });


/*picture to first*/
document.querySelectorAll('.js-picture-to-first')
        .forEach(function(elt) {
            elt.addEventListener('click', function(e) {
                const elt = e.currentTarget;
                myFetch(elt.dataset.entrypoint + '?cake_id='+ elt.parentElement.dataset.cakeid + '&picture_id='+ elt.parentElement.dataset.pictureid)
                .then(function(value) {

                    const image = elt.parentElement.querySelector('img');
                    const toFirst_id = elt.parentElement.dataset.pictureid;
                    const firstImage = document.querySelector(elt.dataset.firstimage);
                    const firstImage_id = firstImage.dataset.pictureid;
                    const toFirstSrc = image.getAttribute('src');

                    image.setAttribute('src', firstImage.getAttribute('src'));
                    image.parentElement.dataset.pictureid = firstImage_id;
                    firstImage.setAttribute('src', toFirstSrc);
                    firstImage.dataset.pictureid = toFirst_id;
                    
                    // A FAIRE AJOUTER UN FLASH
                });
            });
        });


/*cake delete*/
document.querySelectorAll('.cake-delete-button')
        .forEach(deleteButton => {
            deleteButton.addEventListener('click', e => {
                if(!window.confirm('Voulez-vous vraiment supprimer ce gâteau ?')) {
                    e.preventDefault();
                }
            });
        });




/* order sort oldest/latest */
if(document.querySelector('.js-order-select')) {
    document.querySelector('.js-order-select').addEventListener('change', function(e) {
        document.querySelector('.order-search-filter-form').submit();
    })
}



/*order-list checkbox-list*/
if(document.querySelector('.orders-list')) {
    const checkboxManager = new CheckboxManager(
        '.js-item-checkbox',
        '.js-order-masterCheckbox',
        '.js-order-trash',
        '.js-order-mark-status'
    );
    
    new CheckboxDeleteManager(checkboxManager, '.js-order-trash');

    new CheckboxMarkStatusManager(
        checkboxManager, 
        '.js-order-mark-status'
        );

    /*order-show*/
    new OrderShowManager(
        document.querySelectorAll('.order-item'),
        document.querySelector('.orders-list'),
        '#order-show-template',
        checkboxManager
    );
    /*mise a jour du compteur de non lus*/  
    document.querySelector('.js-not-seen-counter').addEventListener('updatecounter', async e => {
        const counter = e.currentTarget;
        const count = await myFetch(counter.dataset.entrypoint);
        if(count.toString() !== counter.innerText) {
            counter.innerText = count.toString();
            counter.classList.add('flash');
            setTimeout(() => {
                counter.classList.remove('flash');
            }, 2000); 
        }
    });
    /*distanceCalc*/
    document.querySelector('.orders-list').addEventListener(OrderShowManager.open, e => {
        const orderShow = e.detail;
        const city = orderShow.querySelector('.js-order-show-city').innerText;
        if(city.length >= 3) {
            myFetch(Config.apiAddress + '&limit=1&q=' + city)
            .then(data => {
                if(data.features.length !== 0) {
                    new DistanceCalculator(
                        orderShow.querySelector('.distance-calculator'),
                        data.features[0].properties,
                        orderShow.querySelector('.distance-calculator-loader')
                    );
                }
            })
        }
    });
    /*suppression des controls quand order show est ouvert*/
    document.querySelector('.orders-list').addEventListener(OrderShowManager.open, e => {
        document.querySelector('.order-controls').style.display = 'none';
        e.currentTarget.style.marginTop = '70px';
    });
    document.querySelector('.orders-list').addEventListener(OrderShowManager.close, e => {
        document.querySelector('.order-controls').removeAttribute('style');
        e.currentTarget.removeAttribute('style');
    });
    /*si on ferme le order show en étant sur les non lus, forcément il y a un order qui est lu et donc on recharge la page*/
    document.querySelector('.orders-list').addEventListener(OrderShowManager.close, e => {
        if(document.querySelector('.spot-control button:first-child').classList.contains('active')) {
            document.querySelector('.spot-control button:first-child').dispatchEvent(new MouseEvent('click'));
        }
    });
}


/*admin-details*/
if(document.querySelector('.js-admin-details-form')) {
    const form = document.querySelector('.js-admin-details-form');
    new AdminDetailsFormManager(
        form,
        form.querySelector('.js-admin-details-update'),
        form.querySelector('.js-admin-details-cancel')
    );
}


/*admin-logout*/
if(document.querySelector('.admin-logout')) {
    document.querySelector('.admin-logout').addEventListener('click', e => {
        if(!window.confirm('Voulez-vous vraiment vous déconnecter ? Le mot de passe vous sera demandé pour vous reconnecter')) {
            e.preventDefault();
            e.stopPropagation();
        }
    })
}


if(document.querySelector('.js-visit-search-filter-form')) {
    document.querySelector('.js-visit-search-filter-form').addEventListener('change', e => {
        e.currentTarget.submit();
    });
}



/*i-loader*/
if(document.querySelector('form')) {
    document.querySelector('form').addEventListener('submit', function(e) {
        e.currentTarget.classList.add('submitting');
    });
}
  





