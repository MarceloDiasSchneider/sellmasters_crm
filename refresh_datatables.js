function modifica(id) {
    nome_id = "at";
    nome_id = nome_id + id;
    $('#' + nome_id).addClass('selezionato');
}

function referta(id) {
    nome_id = "rf";
    nome_id = nome_id + id;
    $('#' + nome_id).addClass('refertato');
}

function paga(id) {
    nome_id = "pg";
    nome_id = nome_id + id;
    $('#' + nome_id).addClass('pagamento');
}

function check_attivita(id) {
    nome_id = "co";
    nome_id = nome_id + id;
    $('#' + nome_id).addClass('selezionato3');
}

function check_positivo(id, positivo) {
    nome_id = "co";
    nome_id = nome_id + id;
    dichiaralo_positivo(id, positivo);
}

function dichiaralo_positivo(id, positivo) {
    $.ajax({
        type: "POST",
        url: "ajax/ajax.php",
        data: { tipo: 'check_positivo', id: id, positivo: positivo },
        dataType: "json",
        async: false,
        success: function(data) {
            alert(data[1]);
            fetch_data();
        },
        error: function(msg) {
            alert("Failed: " + msg.status + ": " + msg.statusText);
        }
    });
}
$(document).ready(function() {
    // funzione che carica inizialmente i dati e passa in Ajax i valori dei filtri
    function fetch_data() {
        table = $('#example').DataTable({
            // funzione somma a fine pagina

            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                // Rimuove la formattazione
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };
                // Totale generale
                total = api
                    .column(3)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Totale di pagina
                pageTotal = api
                    .column(3, { page: 'current' })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
            },
            // fine funzione somma a fine pagina
            "processing": true,
            "bJQueryUI": true,
            "serverSide": true,
            destroy: true,
            paging: true,
            "searching": false,
            language: {
                url: 'ajax/language.json'
            },
            fixedHeader: {
                footer: true
            },
            "pageLength": 25,
            "lengthMenu": [
                [10, 25, 50, 100, 250, 1000000000],
                [10, 25, 50, 100, 250, "Tutti"]
            ],
            "ajax": {
                "url": "ajax/json_report1.php",
                "data": function(d) {
                    d.presidio = $('#presidior').val();
                    d.data = $('#datar').val();
                    d.data_fine = $('#dataf').val();
                }
            },
            columns: [
                { data: "dataita", orderable: false },
                { data: "nome_presidio" },
                { data: "numero_pre" },
                { data: "tipo_tampone" },
                { data: "pagato", orderable: false },
                { data: "tipo_pag", orderable: false },
                { data: "cat_sco", orderable: false },
                { data: "fatturato", orderable: false },
                { data: "referto", orderable: false },
                { data: "upload", orderable: false },
                { data: "elimina", orderable: false },
            ],
            columnDefs: [
                { targets: [0, 3, 4], visible: true, "className": "text-center" }
            ],
            "order": [
                [3, "asc"]
            ]
        });
    };

    // call datatables
    fetch_data();

    $('#searchc-button').click(function() {
        $('#calendar').fullCalendar('refetchEvents');
    });

    $('#search-button').click(function() {
        fetch_data();
    });

    $('#resetc-button').click(function() {
        $('#arear').val("").trigger('change');
        $('#presidior').val("").trigger('change');
    });

    $('#reset-button').click(function() {
        $('#data').val("").trigger('change');
        $('#presidior').val("").trigger('change');
        $('#nomer').val("");

        fetch_data();
    });

});