$(document).ready(function () {

	$.ajax({
		type: "POST",
		url: "model.php",
		data: "action=registri_accessi",
		dataType: "json",
		async: false,
		success: function (data) {
			if (data.state == 'success'){
				$(function () {
					$("#registri_accessi").DataTable({
						'data': data.logs,
						columns: [
							{ title: "ID", data: "id_log"},
							{ title: "Utente", data: "nome"},
							{ title: "Data", data: "datatime"},
							{ title: "Ip", data: "ip_server"},
							{ title: "Remote Port", data: "remote_port"},
							{ title: "User Agent", data: "user_agent"},
						],
						"responsive": true, "lengthChange": false, "autoWidth": false,
						"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
					}).buttons().container().appendTo('#registri_accessi_wrapper .col-md-6:eq(0)');
				})
			} else if (data.state == 'error' ){
				alert ('Problema durante il caricamento dei dati')
				console.log(data.message);
			}
		},
		error: function (msg) {
			alert("Failed: " + msg.status + ": " + msg.statusText);
		}
	});
});