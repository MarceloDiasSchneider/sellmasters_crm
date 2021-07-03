app.component('all_merchants', {
    props: {
        selected_row: {
            type: String
        }
    },
    template:
        /*html*/
        `<div class="row">
            <div class="col-md-12">
                <!-- table with all merchants -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Commercianti</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="merchants" class="table table-bordered table-striped nowrap">
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
            let self = this
            $("#merchants").DataTable({
                'ajax': {
                    type: "POST",
                    url: "model.php",
                    contentType: "application/json",
                    data(){
                        return JSON.stringify({ 'action': 'get_merchants' })
                    },
                    dataType: "json",
                    async: true,
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
                ],
                select: {
                    style: 'single'
                },
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#merchants_wrapper .col-md-6:eq(0)');
            $('#merchants').DataTable().on('select', function (e, dt) { 
                self.emit_selected_row(dt.row({selected: true}).data().id)
            })
            $('#merchants').DataTable().on('deselect', function () { 
                self.emit_selected_row(null)
            })
        },
        // refresh the datatables
        refresh_datatables() {
            $('#merchants').DataTable().ajax.reload(null, false);
        },
        // call method get user data from component register user
        user_edit() {
            // set the variable self to use Vue Js in jQuery
            let self = this
            $('#merchants').on('click', '.update', function () {
                let id = $(this).attr('id');
                // get the user's id
                id = Number(id.replace(/^\D+/g, ''));
                // call a method get user data from component register user
                self.$emit('merchant_data', id)
            });
        },
        // toggle to active or disabled
        toggle_active_desable_merchant() {
            let merchant_id = this.selected_row
            this.emit_selected_row(null)
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({ 'action': 'toggle_merchant', 'id': merchant_id })
            }
            fetch('model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json()
                    switch (data.code) {
                        case 500:
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            console.log(data.message);
                            break;
                        case 200:
                            // show a success message. ex: merchant updated
                            toastr.success(data.message)
                            this.refresh_datatables()
                            break;
                        default:
                            break;
                    }
                })
                // report an error if there is
                .catch(error => {
                    this.errorMessage = error;
                    console.error('There was an error!', error);
                });
        },
        emit_selected_row(data){
            this.$emit('set_selected_row', data)
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