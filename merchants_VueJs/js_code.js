$(document).ready(function () {

    // get back to regiter new user
    $('#back_register').click(function (event) {
        event.preventDefault();
        // reset all input to null
        $('#merchant').trigger("reset");
        // hidden the button to get back to register utente
        $("#back_register").addClass("d-none");
        // Set up the button to get back to register
        $('#register').text('Registra');
        // Remove the input hidden with the id to set up to register
        if ($('#id').length == 1) {
        	$("#id").remove();
        }
        // show a message if success
        toastr.success("Merchant pronto per essere registrato")

    })
});