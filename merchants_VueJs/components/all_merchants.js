app.component('all_merchants', {
    props: {

    },
    template:
        /*html*/
        `<div class="row">
            <div class="col-md-12">
                <!-- table with all merchants -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Tutti gli commercianti</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="merchants" class="table table-bordered table-striped">
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <!-- loading -->
                    <div class="overlay dark" v-show="loading">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <!-- /.loading -->
                </div>
                <!-- /.tabella con tutti gli utente registrate -->
            </div>
        </div>`,
    data() {
        return {
            loading: false
        }
    },
    methods: {
        // Load all merchants to the DataTables
        get_all_merchantes() {
            $("#merchants").DataTable({
                'ajax': {
                    type: "POST",
                    url: "../merchants/model.php",
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
        },
        // refresh the datatables
        refresh_datatables() {
            $('#merchants').DataTable().ajax.reload(null, false);
        },
        // call method get user data from component register user
        user_edit() {
            // set the variable proxy to use Vue Js in jQuery
            let proxy = this
            $('#merchants').on('click', '.update', function () {
                let id = $(this).attr('id');
                // get the user's id
                id = Number(id.replace(/^\D+/g, ''));
                // call a method get user data from component register user
                proxy.$emit('merchant_data', id)
            });
        },
        // toggle to active or disabled
        toggle_active_desable_merchant() {
            let proxy = this
            $("#merchants").on("click", ".able_disable", function () {
                // get the merchant id
                let id_to_toggle = $(this).attr('id');
                // remove the prefix mc_ to get the id
                id_to_toggle = id_to_toggle.replace(/^\D+/g, '');
                const requestOptions = {
                    method: 'POST',
                    mode: 'same-origin',
                    headers: { 'content-type': 'application/json' },
                    body: JSON.stringify({ 'action': 'toggle_merchant', 'id': id_to_toggle })
                }
                fetch('model.php', requestOptions)
                    // process the backend response
                    .then(async response => {
                        const data = await response.json()
                        switch (data.code) {
                            case '500':
                                // reporting an internal server error. ex: try catch
                                alert(data.state)
                                console.log(data.message);
                                break;
                            case '200':
                                // show a success message. ex: merchant updated
                                toastr.success(data.message)
                                proxy.refresh_datatables()
                                break;
                            default:
                        }
                    })
                    // report an error if there is
                    .catch(error => {
                        this.errorMessage = error;
                        console.error('There was an error!', error);
                    });
            });
        }
    },
    mounted() {
        // call the datatables when Vue Js is ready
        this.get_all_merchantes()
        // call the functions to active jQuery event listener
        this.toggle_active_desable_merchant()
        this.user_edit()

    }
})