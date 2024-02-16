/*checkbox*/
document.querySelector('.custom-checkbox-container').addEventListener('click', e => {
    e.stopPropagation();
    e.preventDefault();
    const customCheckbox = e.currentTarget.querySelector('.custom-checkbox');
    const input = e.currentTarget.querySelector('input[type=checkbox]');
    customCheckbox.classList.toggle('checked');
    if(customCheckbox.classList.contains('checked')) {
        input.setAttribute('checked', '');
    } else {
        input.removeAttribute('checked');
    }

})


/*i-loader*/
if(document.querySelector('form')) {
    document.querySelector('form').addEventListener('submit', function(e) {
        e.currentTarget.classList.add('submitting');
    });
}
  
