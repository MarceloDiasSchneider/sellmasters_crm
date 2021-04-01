$(document).ready(function () {
    // register or update a merchant
    $('#merchant').submit(function (event) {
        event.preventDefault();
        let datipresidalform = $("#merchant").serialize();
        $.ajax({
            type: "POST",
            url: "model.php",
            data: "action=insert_or_update_merchants&" + datipresidalform,
            dataType: "json",
            async: false,
            success: function (data) {
                switch (data.code) {
                    case '500':
                        // reporting an internal server error. ex: try catch
                        alert(data.state)
                        console.log(data.message);
                        break;
                    case '401':
                        // reporting an unauthorized error. ex: session code doesn't match 
                        alert(data.state)
                        console.log(data.message);
                        break;
                    case '409':
                        // reporting already inserted data. ex: nome and merchants already used
                        toastr.warning(data.message)
                        break;
                    case '201':
                        // show a success message. ex: merchant inserted
                        toastr.success(data.message)
                        $('#merchants').dataTable().api().ajax.reload(null, false);
                        break;
                    case '200':
                        // show a success message. ex: merchant updated
                        toastr.success(data.message)
                        $('#merchants').dataTable().api().ajax.reload(null, false);
                        break;
                    default:
                }
            },
            error: function (msg) {
                alert("Failed: " + msg.status + ": " + msg.statusText);
            }
        });
    });

    // Load all merchants the DataTables
    $("#merchants").DataTable({
        'ajax': {
            type: "POST",
            url: "model.php",
            data: { 'action': 'get_merchants' },
            dataType: "json",
            async: false,
            dataSrc: ""
        },
        columns: [
            { title: "Nome", data: "nome" },
            { title: "Nome sociale", data: "nome_sociale" },
            { title: "Merchant ID", data: "merchant_id" },
            { title: "MWS", data: "mws" },
            { title: "Intervallo tra i controlli", data: "interval_between_check" },
            { title: "Nome contatto", data: "nome_contatto" },
            { title: "Telefono", data: "telefono" },
            { title: "Email", data: "email" },
            { title: "Indirizzo", data: "indirizzo" },
            { title: "Numero civico", data: "numero_civico" },
            { title: "Citta", data: "citta" },
            { title: "Cap", data: "cap" },
            { title: "Stato", data: "stato" },
            { title: "Provincia", data: "provincia" },
            { title: "Attivo", data: "attivo" },
            { title: "Azione", data: "azione" }
        ],
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#utenti_wrapper .col-md-6:eq(0)');

    // toggle to active or disabled
    $("#merchants").on("click", ".able_disable", function () {
        // get the id
        let id_to_toggle = $(this).attr('id');
        // remove the prefix mc_ to get the id
        id_to_toggle = 'id=' + id_to_toggle.replace(/^\D+/g, '');
        $.ajax({
            type: "POST",
            url: "model.php",
            data: "action=toggle_merchant&" + id_to_toggle,
            dataType: "json",
            async: false,
            success: function (data) {
                switch (data.code) {
                    case '500':
                        // reporting an internal server error. ex: try catch
                        alert(data.state)
                        console.log(data.message);
                        break;
                    case '401':
                        // reporting an unauthorized error. ex: session code doesn't match 
                        alert(data.state)
                        console.log(data.message);
                        break;
                    case '409':
                        // reporting already inserted data. ex: nome and merchants already used
                        toastr.warning(data.message)
                        break;
                    case '201':
                        // show a success message. ex: merchant inserted
                        toastr.success(data.message)
                        $('#merchants').dataTable().api().ajax.reload(null, false);
                        break;
                    case '200':
                        // show a success message. ex: merchant updated
                        toastr.success(data.message)
                        $('#merchants').dataTable().api().ajax.reload(null, false);
                        break;
                    default:
                }
            },
            error: function (msg) {
                alert("Failed: " + msg.status + ": " + msg.statusText);
            }
        });
    });

    // Get the merchant's info to update
    $('#merchants').on('click', '.update', function () {
        let id = $(this).attr('id');
        id = id.replace(/^\D+/g, '');
        id_merchant = 'id=' + id;
        $.ajax({
            type: "POST",
            url: "model.php",
            data: "action=get_merchant_data&" + id_merchant,
            dataType: "json",
            async: false,
            success: function (data) {
                console.log(data);
                switch (data.code) {
                    case '500':
                        // reporting an internal server error. ex: try catch
                        alert(data.state)
                        console.log(data.message);
                        break;
                    case '401':
                        // reporting an unauthorized error. ex: session code doesn't match 
                        alert(data.state)
                        console.log(data.message);
                        break;
                    case '409':
                        // reporting already inserted data. ex: nome and merchants already used
                        toastr.warning(data.message)
                        break;
                    case '201':
                        // show a success message. ex: merchant inserted
                        toastr.success(data.message)
                        break;
                    case '200':
                        // reset all input to null
                        $('#merchant').trigger("reset");
                        // set all inputs with the values
                        for (const [key, value] of Object.entries(data.merchant)) {
                            $(`#${key}`).val(value);
                            console.log(`#${key} V${value}`);
                        }
                        // Append a input hidden with the id to set up to update
                        if ($('#id').length == 0) {
                            $('<input>').attr({
                                type: 'hidden',
                                id: 'id',
                                name: 'id',
                                value: id
                            }).appendTo('#merchant');
                        } else {
                            $('#id').val(id);
                        }

                        // Set up the button to get back to nuovo utente
                        $('#register').text('Aggiornare');

                        // show the button to get back to register utente
                        $("#back_register").removeClass("d-none");

                        // show a success message. ex: merchant updated
                        toastr.success(data.message)

                        break;
                    default:
                }
            },
            error: function (msg) {
                alert("Failed: " + msg.status + ": " + msg.statusText);
            }
        });
    });

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