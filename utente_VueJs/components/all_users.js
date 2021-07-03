app.component('all_users', {
    props: {
        selected_row: {
            type: String
        }
    },
    template:
        /*html*/
        `<div class="row">
            <div class="col-md-12">
                <!-- tabella con tutti gli utente registrate -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Utenti</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="utenti" class="table table-bordered table-striped nowrap">
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
        <!-- /.row -->
        </div>`,
    data() {
        return {
            loading: false
        }
    },
    methods: {
        // get all users to set datatables
        get_all_users() {
            let self = this
            $("#utenti").DataTable({
                'ajax': {
                    type: "POST",
                    url: "model.php",
                    contentType: "application/json",
                    data() {
                        return JSON.stringify({"action":"get_all_users"});
                    },
                    dataType: "json",
                    async: true,
                    dataSrc: ""
                },
                columns: [
                    { title: "Nome", data: "nome" },
                    { title: "Cognome", data: "cognome" },
                    { title: "Email", data: "email" },
                    { title: "Codice Fiscale", data: "codice_fiscale" },
                    { title: "Telefono", data: "telefono" },
                    { title: "Data", data: "data_nascita" },
                    { title: "profile", data: "id_profile" },
                    { title: "Attivo", data: "attivo" },
                ],
                select: {
                    style: 'single'
                },
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#utenti_wrapper .col-md-6:eq(0)')
            $('#utenti').DataTable().on('select', function (e, dt) { 
                self.emit_selected_row(dt.row({selected: true}).data().id_utente)
            })
            $('#utenti').DataTable().on('deselect', function () { 
                self.emit_selected_row(null)
            })
        },
        // refresh the datatables
        refresh_datatables() {
            $('#utenti').DataTable().ajax.reload(null, false);
        },
        // toggle user to active or disabled
        toggle_user_active() {
            let user_id = this.selected_row
            this.emit_selected_row(null)
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({ 'action': 'toggle_user_active', 'id_utente': user_id })
            }
            fetch('model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json()
                    switch (data.code) {
                        case 500:
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            console.log(data.message)
                            break;
                        case 200:
                            // reporting an internal server error. ex: try catch
                            toastr.success(data.message)
                            // call method datatables refresh
                            this.refresh_datatables()
                            break
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
        this.get_all_users()
    }
})
