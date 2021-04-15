app.component('all_users', {
    props: {

    },
    template:
        /*html*/
        `<div class="row">
            <div class="col-md-12">
                <!-- tabella con tutti gli utente registrate -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Tutti gli utenti</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="utenti" class="table table-bordered table-striped">
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
            $("#utenti").DataTable({
                'ajax': {
                    type: "POST",
                    url: "../utente/model.php",
                    data: { 'action': 'get_utenti' },
                    dataType: "json",
                    async: false,
                    dataSrc: ""
                },
                columns: [
                    { title: "Nome", data: "nome" },
                    { title: "Cognome", data: "cognome" },
                    { title: "Email", data: "email" },
                    { title: "Codice Fiscale", data: "codice_fiscale" },
                    { title: "Telefono", data: "telefono" },
                    { title: "Data", data: "data_nascita" },
                    { title: "Livello", data: "id_livello" },
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
            $('#utenti').dataTable().api().ajax.reload(null, false);
        },
        // call method get user data from component register user
        user_edit() {
            // set the variable proxy to use Vue Js in jQuery
            let proxy = this
            $('#utenti').on('click', '.update_user', function () {
                let id_utente = $(this).attr('id');
                // get the user's id
                id_utente = Number(id_utente.replace(/^\D+/g, ''));
                // call a method get user data from component register user
                proxy.$emit('user_data', id_utente)
            });
        },
        // toggle user to active or disabled
        toggle_user_active() {
            $("#utenti").on("click", ".disable_user", function () {
                // get the user id
                var id_to_toggle = $(this).attr('id');
                // remove the prefix ut_ to get the id
                id_to_toggle = id_to_toggle.replace(/^\D+/g, '');
                const requestOptions = {
                    method: 'POST',
                    mode: 'same-origin',
                    headers: { 'content-type': 'application/json' },
                    body: JSON.stringify({ 'action': 'toggle_user_active', 'id_utente': id_to_toggle })
                }
                fetch('model.php', requestOptions)
                    // process the backend response
                    .then(async response => {
                        const data = await response.json()
                        switch (data.code) {
                            case '500':
                                // reporting an internal server error. ex: try catch
                                alert(data.state)
                                console.log(data.message)
                                break;
                            case '200':
                                // reporting an internal server error. ex: try catch
                                toastr.success(data.message)
                                // call datatables refresh
                                app.component('all_users').methods.refresh_datatables()
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
            });
        },
    },
    mounted() {
        // call the datatables when Vue Js is ready
        this.get_all_users()
        // call the functions to active jQuery event listener
        this.toggle_user_active()
        this.user_edit()
    }
})
