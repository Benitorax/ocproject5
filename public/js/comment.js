document.addEventListener("DOMContentLoaded", function(event) {

    let formEl = document.querySelector('.js-form-comment');
    let buttonEl = formEl.querySelector('.js-button');
    let alertEl = formEl.querySelector('.js-alert');
    let errorEl = formEl.querySelector('.js-error');
    let csrfInput = formEl.querySelector('input[name=csrf_token]');

    formEl.addEventListener('submit', async e => {
        e.preventDefault();
        resetFormError();
        buttonEl.disabled = true;

        try {
            let response = await fetch(formEl.getAttribute('action'), {
                method: 'POST',
                body: new FormData(formEl)
            });
            let data = await response.json();

            if (response.status === 422) {
                let error = data.error;

                if (error.content !== null) {
                    errorEl.textContent = error.content;
                    errorEl.hidden = false;
                }

                if (error.csrf !== null) {
                    alertEl.textContent = error.csrf;
                    alertEl.hidden = false;
                    csrfInput.value = data.csrf_token;
                }

                buttonEl.disabled = false;

            } else if (response.status === 303) {
                document.location.href = data.url;
            } else {
                console.log('ahqsiuhq');
                throw 500;
            }
        } catch (error) {
            alertEl.textContent = "Sorry, the website can't currently submit your comment. Please, try again later.";
            alertEl.hidden = false;
        }

    });

    const resetFormError = function() {
        errorEl.textContent = '';
        errorEl.hidden = true;
        alertEl.textContent = '';
        alertEl.hidden = true;
    };
});