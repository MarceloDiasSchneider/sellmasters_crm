$(document).ready(function () {

	$.ajax({
		type: "POST",
		url: "model.php",
		data: "action=registri_accessi",
		dataType: "json",
		async: false,
		success: function (data) {
			// console.log(data);
			// let dati = []
			// data.forEach(value => {
			// 	dati.push(Object.values(value))
			// })
			// console.log(dati);
			// costruisce la tabella con DataTables
			$(function () {
				$("#registri_accessi").DataTable({
					'data': data,
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
		},
		error: function (msg) {
			alert("Failed: " + msg.status + ": " + msg.statusText);
		}
	});
});