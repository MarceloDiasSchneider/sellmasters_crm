$(document).ready(function () {

	// inizia una nuova sessione dal form
	$('#autenticazione').submit(function (event) {
		event.preventDefault();
		let datipresidalform = $("#autenticazione").serialize();
		// console.log(datipresidalform);
		$.ajax({
			type: "POST",
			url: "model.php",
			data: "action=autenticazione&" + datipresidalform,
			dataType: "json",
			async: false,
			success: function (data) {
				if (data.state == 'success') {
					// reindirizza la pagina se la autenticazione é riuscida, 
					document.location.href = data.url;
				} else if (data.state == 'unauthorized') {
					// mostra il messaggio di errore se la autenticazione non é riuscida, 
					$('#feedback').text(data.message)
				} else if (data.state == 'error') {
					// mostra il messaggio di errore ha un problema , 
					alert('Problema')
					console.log(data.message);
				}
			},
			error: function (msg) {
				alert("Failed: " + msg.status + ": " + msg.statusText);
			}
		});
	});

	// send a mail to recovery the password
	$('#forgot-password').submit(function (event) {
		event.preventDefault();
		let datipresidalform = $("#forgot-password").serialize();
		$.ajax({
			type: "POST",
			url: "model.php",
			data: "action=forgot_password&" + datipresidalform,
			dataType: "json",
			async: false,
			success: function (data) {
				if (data.state == 'success') {
					toastr.success(data.message)
					$('#feedback').text('')
				} else if (data.state == 'error') {
					alert('Problema')
				} else if (data.state == 'unauthorized') {
					$('#feedback').text(data.message)
				} else if (data.state == 'Internal server error') {
					toastr.error(data.message)
				}
			},
			error: function (msg) {
				alert("Failed: " + msg.status + ": " + msg.statusText);
			}
		});
	});

	$('#recover-password').submit(function (event) {
		event.preventDefault();
		let datipresidalform = $("#recover-password").serialize();
		$.ajax({
			type: "POST",
			url: "model.php",
			data: "action=recover_password&" + datipresidalform,
			dataType: "json",
			async: false,
			success: function (data) {
				if(data.state == 'bad request'){
					toastr.warning(data.message)
				} else if (data.state == 'success') {
					toastr.success(data.message)
					// set a time to redirect the user to authentication page
					setTimeout(function(){ 
						window.location.href = "index.php";
					}, 1500);
				} else if (data.state == 'error'){
					alert('Problema, trove più tarde')
					console.log(data.message);
				} else if(data.state == 'unauthorized'){
					toastr.error(data.message)
				}
			},
			error: function (msg) {
				alert("Failed: " + msg.status + ": " + msg.statusText);
			}
		});
	});
});