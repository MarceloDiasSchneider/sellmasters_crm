app.component('all_profiles', {
    template:
        /*html*/
        `<div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Tutti i profili</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="profiles" class="table table-bordered table-striped nowrap">
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
            // variable to control the loading card
            loading: false
        }
    },
    methods: {
        // get all profiles to set datatables
        get_all_profiles() {
            $("#profiles").DataTable({
                'ajax': {
                    type: "POST",
                    url: "../profile/model.php",
                    data: { 'action': 'get_profiles' },
                    dataType: "json",
                    async: true,
                    dataSrc: ""
                },
                columns: [
                    { title: "Descrizione", data: "descrizione" },
                    { title: "Attivo", data: "attivo" },
                    { title: "Azione", data: "azione" }
                ],
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#profiles_wrapper .col-md-6:eq(0)');
        },
        // refresh the datatables
        refresh_datatables() {
            $('#profiles').DataTable().ajax.reload(null, false);
        },
        // call method get profile data from component register profile
        profile_edit() {
            // set the variable proxy to use Vue Js in jQuery
            let proxy = this
            $('#profiles').on('click', '.update_profile', function () {
                let id_profile = $(this).attr('id');
                // get the profile's id
                id_profile = Number(id_profile.replace(/^\D+/g, ''));
                // call a method get profile data from component register profile
                proxy.$emit('profile_data', id_profile)
            });
        },
        // toggle user to active or disabled
        toggle_user_active() {
            let self = this
            $("#profiles").on("click", ".disable_profile", function () {
                // get the user id
                let id_to_toggle = $(this).attr('id');
                // remove the prefix ut_ to get the id
                id_to_toggle = id_to_toggle.replace(/^\D+/g, '');
                const requestOptions = {
                    method: 'POST',
                    mode: 'same-origin',
                    headers: { 'content-type': 'application/json' },
                    body: JSON.stringify({ 'action': 'toggle_profile_active', 'id_profile': id_to_toggle })
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
                                self.refresh_datatables()
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
        this.get_all_profiles()
        // call the functions to active jQuery event listener
        this.toggle_user_active()
        this.profile_edit()
    }
})