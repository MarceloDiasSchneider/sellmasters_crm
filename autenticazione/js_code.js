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
				if (data.state == 'success'){
					// reindirizza la pagina se la autenticazione é riuscida, 
					document.location.href= data.url;
				} else if (data.state == 'unauthorized'){
					// mostra il messaggio di errore se la autenticazione non é riuscida, 
					$('#feedback').text(data.message)
				} else if (data.state == 'error'){
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
				if (data.state == 'success'){
					toastr.success(data.message)
					$('#feedback').text('')
				} else if (data.state == 'error'){
					alert('Problema')
				} else if (data.state == 'unauthorized'){
					$('#feedback').text(data.message)
				} else if (data.state == 'Internal server error'){
					toastr.error(data.message)
				}
			},
			error: function(msg){
				alert("Failed: " + msg.status + ": " + msg.statusText);
			}
		})
	})

});