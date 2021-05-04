document.addEventListener("DOMContentLoaded", function(event) {
    var modalForm = document.getElementById("deleteModalForm");
    
    // adds click listener to delete button to show delete modal
    var deleteButtons = document.querySelectorAll(".js-button-delete");
    deleteButtons.forEach(function(deleteButton) {
        deleteButton.addEventListener("click", function(e) {
            modalForm.action = this.dataset.url;
            
            // use jquery to bootstrap method
            $("#deleteModal").modal("show");
        });
    });

    // submits the form when modal button is clicked
    var deleteModalButton = document.getElementById("deleteModalButton");
    deleteModalButton.addEventListener("click", function(e) {
        modalForm.submit();
    });
});