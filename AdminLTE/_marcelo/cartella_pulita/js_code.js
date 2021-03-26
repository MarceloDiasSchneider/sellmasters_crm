$(document).ready(function () {

	// inizia una nuova sessione dal form
	$('#form').submit(function (event) {
		event.preventDefault();
		let datipresidalform = $("#form").serialize();
		console.log(datipresidalform);
		$.ajax({
			type: "POST",
			url: "model.php",
			data: "action=azione&" + datipresidalform,
			dataType: "json",
			async: false,
			success: function (data) {
				console.log(data);
				// creare tutto quello che vuoi \o/ !!!
			},
			error: function (msg) {
				alert("Failed: " + msg.status + ": " + msg.statusText);
			}
		});
	});
});