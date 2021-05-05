document.addEventListener("DOMContentLoaded", (event) => {
    let formEl = document.querySelector(".js-form-comment");
    let buttonEl = formEl.querySelector(".js-button");
    let alertEl = formEl.querySelector(".js-alert");
    let errorEl = formEl.querySelector(".js-error");
    let csrfInput = formEl.querySelector("input[name=csrf_token]");

    formEl.addEventListener("submit", async(e) => {
        e.preventDefault();
        resetFormError();
        buttonEl.disabled = true;

        try {
            let response = await fetch(formEl.getAttribute("action"), {
                method: "POST",
                body: new FormData(formEl)
            });

            let data = await response.json();

            if (response.status === 422) {
                let error = data.error;

                if (error.content !== null) {
                    // sets the message in the form
                    errorEl.textContent = error.content;
                    errorEl.hidden = false;
                }

                if (error.csrf !== null) {
                    // sets the message in the alert element
                    alertEl.textContent = error.csrf;
                    alertEl.hidden = false;
                    // sets a new csrf token
                    csrfInput.value = data.csrf_token;
                }

                buttonEl.disabled = false;

            } else if (response.status === 303) {
                document.location.href = data.url;
            } else {
                throw 500;
            }
        } catch (error) {
            alertEl.textContent = "Sorry, the website can't currently submit your comment. Please, try again later.";
            alertEl.hidden = false;
        }

    });

    const resetFormError = function() {
        errorEl.textContent = "";
        errorEl.hidden = true;
        alertEl.textContent = "";
        alertEl.hidden = true;
    };
});