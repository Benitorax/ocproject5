document.addEventListener("DOMContentLoaded", (event) => {
    let modalForm = document.getElementById("deleteModalForm");

    // adds click listener to delete button to show delete modal
    let deleteButtons = document.querySelectorAll(".js-button-delete");
    deleteButtons.forEach((deleteButton) => {
        deleteButton.addEventListener("click", (e) => {
            modalForm.action = deleteButton.dataset.url;

            // use jquery to bootstrap method
            $("#deleteModal").modal("show");
        });
    });

    // submits the form when modal button is clicked
    let deleteModalButton = document.getElementById("deleteModalButton");
    deleteModalButton.addEventListener("click", (e) => {
        modalForm.submit();
    });
});