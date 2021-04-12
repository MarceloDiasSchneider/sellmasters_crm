$(document).ready(function () {

	// registra il nuovo utente dal form
	$('#nuovo_utente').submit(function (event) {
		event.preventDefault();
		let datipresidalform = $("#nuovo_utente").serialize();
		$.ajax({
			type: "POST",
			url: "model.php",
			data: "action=insert_or_update_user&" + datipresidalform,
			dataType: "json",
			async: false,
			success: function (data) {
				if (data.state == "unauthorized") {
					alert(`Error ${data.code}: ${data.message}`)
					document.location.href = '../autenticazione';
				} else if (data.state == "success") {
					toastr.success(data.message)
					// reload the table updated with ajax
					$('#utenti').dataTable().api().ajax.reload(null, false);
				} else if (data.state == "error") {
					toastr.error(data.message)
				} else if (data.state == "warning") {
					toastr.warning(data.message)
				}
			},
			error: function (msg) {
				alert("Failed: " + msg.status + ": " + msg.statusText);
			}
		});
	});

	// Cerca tutti gli utenti registrato
	$("#utenti").DataTable({
		'ajax': {
			type: "POST",
			url: "model.php",
			data: { 'action': 'get_utenti' },
			dataType: "json",
			async: false,
			dataSrc: ""
		},
		columns: [
			{ title: "Nome", data: "nome" },
			{ title: "Cognome", data: "cognome" },
			{ title: "Email", data: "email" },
			{ title: "Codice Fiscale", data: "codice_fiscale" },
			{ title: "Telefono", data: "telefono" },
			{ title: "Data", data: "data_nascita" },
			{ title: "Livello", data: "id_livello" },
			{ title: "Attivo", data: "attivo" },
			{ title: "Azione", data: "azione" }
		],
		"responsive": true,
		"lengthChange": false,
		"autoWidth": false,
		"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
	}).buttons().container().appendTo('#utenti_wrapper .col-md-6:eq(0)');

	// Toggle attivare o disattivare l'utente
	$("#utenti").on("click", ".disable_user", function () {
		let id_to_toggle = $(this).attr('id');
		id_to_toggle = 'id_utente=' + id_to_toggle.replace(/^\D+/g, '');
		$.ajax({
			type: "POST",
			url: "model.php",
			data: "action=toggle_utente&" + id_to_toggle,
			dataType: "json",
			async: false,
			success: function (data) {
				if (data.state == "Success") {
					toastr.success(data.message)
					// reload the table updated with ajax
					$('#utenti').dataTable().api().ajax.reload(null, false);
				}
			},
			error: function (msg) {
				alert("Failed: " + msg.status + ": " + msg.statusText);
			}
		});
	});

	// Cerca livello per creare select
	$.ajax({
		type: "POST",
		url: "model.php",
		data: "action=get_livelli",
		dataType: "json",
		async: false,
		success: function (data) {
			if (data.code == '200'){
				data.livelli.forEach(livello => {
					$("#livello").append(`<option value='${livello.id_livello}'>${livello.descrizione}</option>`);
				});
			} else if (data.code == '500'){
				alert('Problema durante il caricamento delle opzioni de livello');
				console.log(data.message);
			}
		},
		error: function (msg) {
			alert("Failed: " + msg.status + ": " + msg.statusText);
		}
	});

	// Cerca un utente con id per fare un aggiornamento
	$('#utenti').on('click', '.update_user', function () {
		let id_utente = $(this).attr('id');
		id = id_utente.replace(/^\D+/g, '');
		id_utente = 'id_utente=' + id;
		$.ajax({
			type: "POST",
			url: "model.php",
			data: "action=get_user_data&" + id_utente,
			dataType: "json",
			async: false,
			success: function (data) {
				if (data.state == 'Success') {

					// reset all input to null
					$('#nuovo_utente').trigger("reset");

					// set all inputs with the values
					for (const [key, value] of Object.entries(data.user)) {
						$(`#${key}`).val(value);
					}

					// Remove the riquired from the password
					$('#password').removeAttr('required')
					$('#verificaPassword').removeAttr('required')

					// Append a input hidden with the id_utente to set up to update
					if ($('#id_utente').length == 0) {
						$('<input>').attr({
							type: 'hidden',
							id: 'id_utente',
							name: 'id_utente',
							value: id
						}).appendTo('#nuovo_utente');
					} else {
						$('#id_utente').val(id);
					}

					// Set up the button to get back to nuovo utente
					$('#bottone_registra_utente').text('Aggiornare');

					// show the button to get back to register utente
					$("#bottone_nuovo_utente").removeClass("d-none");

					// show a message if success
					toastr.success(data.message)
				}
			},
			error: function (msg) {
				alert("Failed: " + msg.status + ": " + msg.statusText);
			}
		});
	});

	// get back to regiter new user
	$('#bottone_nuovo_utente').click(function (event) {
		event.preventDefault();

		// reset all input to null
		$('#nuovo_utente').trigger("reset");

		// Add the riquired to the password input
		$('#password').prop('required', true);
		$('#verificaPassword').prop('required', true);

		// Set up the button to get back to register
		$('#bottone_registra_utente').text('Registra');

		// hidden the button to get back to register utente
		$("#bottone_nuovo_utente").addClass("d-none");

		// Remove the input hidden with the id_utente to set up to register
		if ($('#id_utente').length == 1) {
			$("#id_utente").remove();
		}
		// show a message if success
		toastr.success("Utente pronto per essere registrato")

	})
});