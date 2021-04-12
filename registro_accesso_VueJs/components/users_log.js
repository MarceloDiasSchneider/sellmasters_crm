app.component('users_log', {
    props: {
    },
    template:
        /*html*/
        `<div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Registri di Accessi</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="registri_accessi" class="table table-bordered table-hover">
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->`,
    data() {
        return {

        }
    },
    methods: {
        get_users_log() {
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
        }
    },
    mounted() {
        this.get_users_log()
    }
})