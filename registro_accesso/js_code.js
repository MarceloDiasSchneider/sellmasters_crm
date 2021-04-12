$(document).ready(function () {
	$("#registri_accessi").DataTable({
		'ajax': {
			type: "POST",
			url: "../registro_accesso/model.php",
			data: { 'action': 'registri_accessi' },
			dataType: "json",
			async: false,
			dataSrc: ""
		},
		columns: [
			{ title: "ID", data: "id_log" },
			{ title: "Utente", data: "nome" },
			{ title: "Data", data: "datatime" },
			{ title: "Ip", data: "ip_server" },
			{ title: "Remote Port", data: "remote_port" },
			{ title: "User Agent", data: "user_agent" },
		],
		"responsive": true, 
		"lengthChange": false, 
		"autoWidth": false,
		"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
	}).buttons().container().appendTo('#registri_accessi_wrapper .col-md-6:eq(0)');
});